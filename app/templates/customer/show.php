<?php

use Bruder\Model\Repair\RepairOrder;
use Bruder\Model\Customer;
use Bruder\Model\Leasing\Leasing;
use Bruder\Model\Customer\CustomerObject;
use Bruder\Time\Time;

/**
 * @var int
 */
$id = filter_var($GLOBALS["route_param_id"] ?? 0, FILTER_VALIDATE_INT);

/**
 * @var string
 */
$object_type = filter_var($GLOBALS["route_param_customer_object_type"] ?? "", FILTER_SANITIZE_SPECIAL_CHARS) ?? "bike";

/**
 * @var Customer
 */
$Customer = Customer::with("bikes")
  ->with("sewing_machines")
  ->with("leasings")
  ->with("orders")
  ->find($id);

/**
 * @var ?CustomerObject
 */
$SewingOrders = $Customer->orders
  ->where("type", "sewing");


/**
 * @var ?CustomerObject
 */
$BikeOrders = $Customer->orders
  ->where("type", "bike");

/**
 * Evaluate the most senseful object type to show when first
 * opening the page.
 */
if (!$object_type)
  $object_type =
    $SewingOrders->count() > $BikeOrders->count()
    || !$Customer->bikes->count() && $Customer->sewing_machines->count()
    ? "sewing"
    : "bike";

/**
 * @var string
 */
$base_url = "/customer/$Customer->id";

if (!$Customer) :
  include UNAVAILABLE;
else :

?>

  <div content-width fl gap>

    <?php

    /**
     * Include the view based on the page type selected.
     */
    include __DIR__ . "/show/_$object_type.php";

    ?>

  </div>

<?php

endif;
