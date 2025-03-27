<?php

use Bruder\Model\Repair\RepairOrder;
use Bruder\Model\Customer\CustomerObject;
use Bruder\Model\Brand;

/**
 * @var int
 */
$id = filter_var($GLOBALS["route_param_id"] ?? null, FILTER_VALIDATE_INT);

/**
 * @var ?string
 */
$sub = filter_var($GLOBALS["route_param_sub"] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var ?RepairOrder
 */
$RepairOrder = RepairOrder::with("customer")
  ->with("brand")
  ->with("customer_object")
  ->find($id);

/**
 * No RepairOrder found?
 */
if (!$RepairOrder) :
  include TEMPLATE . "/global/unavailable.php";
else :

  /**
   * @var ?Employee
   */
  $Employee = $RepairOrder->employee;

  /**
   * @var ?Customer
   */
  $Customer = $RepairOrder->customer;

  /**
   * @var ?Brand
   */
  $Brand = $RepairOrder->brand;

  /**
   * @var ?CustomerObject
   */
  $Object = $RepairOrder->customer_object;

  /**
   * @var ?Leasing
   */
  $Leasing = $RepairOrder->leasing;

  /**
   * @var string
   */
  $submit_error = $RepairOrder->submit_error();

  /**
   * @var string
   */
  $base_url = "/repair/order/edit/" . $RepairOrder->id;

  /**
   * @var array
   */
  $redirect_uri = "$base_url/" . match ($sub) {
    "type" => "employee",
    "employee" => "customer",
    "customer" => "object",
    "leasing",
    "object" => "items",
    "items" => "overview",
    "overview" => "success",
    default => "employee",
  };

  /**
   * Redirect to leasing.
   */
  if ($sub === "object" && $RepairOrder->type === "bike")
    $redirect_uri = "$base_url/leasing";

  /**
   * Redirect to the repair order overview page and add a ref to
   * trigger some additional actions.
   */
  if ($sub === "overview")
    $redirect_uri = "/repair/order/$RepairOrder->id?ref=startedRepairing";
  else

    /**
     * For nice scrolling effects that should help with not losing
     * track of the current tab, add the hashtag id to the redirect
     * uri string.
     */
    $redirect_uri .= "#activeObject";

  /**
   * The uri for going back a step dynamically created based on
   * the current step.
   */
  $back_uri = "$base_url/" . match ($sub) {
    "type" => "type",
    "employee" => "type",
    "customer" => "employee",
    "object" => "customer",
    "leasing" => "object",
    "items" => "object",
    "overview" => "items",
    "success" => "overview",
    default => "employee",
  };

  /**
   * Go back to leasing.
   */
  if ($sub === "items" && $RepairOrder->type === "bike")
    $back_uri = "$base_url/leasing";

  /**
   * Set the hashtag id for the back uri.
   */
  $back_uri .= "#activeObject";


  /**
   * Success kind of is a seperate page with a seperae structure.
   */
  if ($sub !== "success") :

?>

    <form request="repair:order:update" delay=110 responder=error redirect="<?= $redirect_uri ?>" no-scroll-top fl fldircol content-gap pb60>

      <input type=hidden name=id value=<?= $RepairOrder->id ?> />

      <div repair-order pb62>
        <div main-distance mt=wide>
          <div content-width=smol+ fl fldircol center-content gap=smol+>

            <?php

            /**
             * Include heading.
             */
            include __DIR__ . "/_header.php";

            /**
             * @var string
             */
            $file_path = __DIR__ . "/edit/_$sub.php";

            /**
             * Include the existing file or fallback to the index.
             */
            include file_exists($file_path) || !$RepairOrder ? $file_path : TEMPLATE . "/global/unavailable.php"; ?>

          </div>
        </div>
      </div>
    </form>

  <?php else : ?>

    <div repair-order pb62>
      <div main-distance mt=wide>
        <div content-width=smol+ fl fldircol center-content content-gap>

          <?php

          /**
           * Include the existing file or fallback to the index.
           */
          include __DIR__ . "/edit/_success.php"; ?>

        </div>
      </div>
    </div>

<?php

  endif;
endif;

?>