<?php

use Bruder\Model\Repair\RepairPart;

/**
 * @var RepairOrder $RepairOrder
 */

/**
 * @var bool
 */
$editable ??= true;

?>

<section order-parts fl fldircol gap>
  <div list fl fldircol gap=smoler>
    <?php

    /**
     * @var RepairPart
     */
    $OrderParts = $RepairOrder->parts;

    foreach ($OrderParts ?? [] as $OrderPart) :

      /**
       * Increase tabindex.
       */
      $tabindex++;

      template("repair/order/part/input", variables: [
        "OrderPart" => $OrderPart,
        "editable" => $editable
      ]);
    endforeach;

    if (!$editable && !$OrderParts->count()) : ?>
      <div fl gap alic jucsb rounded="wide" outlined p24>
        <div fl alic gap="smol+" flexone>
          <mi wide>stroke_partial</mi>
          <div fl gap="smol" alic w100>
            <div fl gap="smol" jucc alic p12 rounded filled slight w100>
              <p text smolplus>Keine Ersatzteile</p>
            </div>
          </div>
        </div>
        <mi style="height:1.4em;min-width:1.4em;" mid circled fl alic jucc color="dark-orange" background="light-orange">trip_origin</mi>
      </div>
    <?php endif; ?>
  </div>

  <?php if ($editable) : ?>
    <div fl jucc>
      <mbutton request-get="repair:order:part:new" data-id="<?= $RepairOrder->id ?>" data-type="<?= $RepairOrder->type ?>" material size=mid has-icon=left outlined>
        <mi>add</mi>
        Ersatzteil hinzufügen
      </mbutton>
    </div>

    <?php $tabindex++ ?>
  <?php endif; ?>

</section>