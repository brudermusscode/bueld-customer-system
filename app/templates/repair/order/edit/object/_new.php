<?php

use Bruder\Model\Leasing\LeasingCompany;
use Bruder\Model\Repair\RepairOrder;
use Bruder\Model\Brand;
use Bruder\Model\Customer\CustomerObject;

/**
 * @var RepairOrder $RepairOrder
 * @var ?Brand $Brand
 * @var ?CustomerObject $Object
 */

/**
 * * Object
 */

?>

<div fl fldircol gap>
  <div posrel>
    <timeline-sub-icon fl alic jucc>
      <mi mid><?= $RepairOrder->display_type_icon() ?></mi>
    </timeline-sub-icon>
    <p text midler bold><?= $RepairOrder->display_type() ?></p>
    <!-- <p text slight>Informationen zum Objekt</p> -->
  </div>

  <?php

  /**
   * Include basic customer object creation form.
   */
  include TEMPLATE . "/customer/object/_form.php"; ?>

  <tipp-box background=light-green color=dark-green rounded=mid>
    <mi>lightbulb</mi>
    <p>Gib eine <?= $RepairOrder->type === "bike"
                  ? "Rahmen-Nummer"
                  : "Maschinen-Nummer"
                ?> an, damit du das Gerät bei der nächsten Reparatur direkt auswählen kannst!</p>
  </tipp-box>
</div>