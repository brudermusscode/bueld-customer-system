<?php

use Bruder\Model\Repair\RepairType;

/**
 * @var RepairOrder $RepairOrder
 */

/**
 * @var bool
 */
$editable ??= true;

?>

<section repairs fl fldircol gap>

  <?php

  /**
   * @var RepairType
   */
  $RandomRepairType = RepairType::inRandomOrder()
    ->where("type", $RepairOrder->type)
    ->first();

  /**
   * @var RepairType
   */
  $Repairs = $RepairOrder->repair_order_items;

  /**
   * @var string
   */
  $placeholder = $RandomRepairType->initial_id . " oder " . htmlspecialchars($RandomRepairType->description ?? "Etwas komplett neues");

  ?>

  <div added-repairs list fl fldircol gap=smoler>
    <?php

    /**
     * @var int
     */
    $tabindex = 1;

    foreach ($Repairs ?? [] as $Repair) :

      /**
       * Increase tabindex.
       */
      $tabindex++;

      /**
       * @var RepairType
       */
      $RepairType = $Repair->repair_type;

    ?>
      <div input material has-icon has-text <?= $editable ? "has-delete" : "" ?> w100 has-search clone>
        <mi input-type-icon color=green bold>done</mi>
        <input
          <?= !$editable ? "disabled=no-opacity" : "" ?>
          tabindex=<?= $tabindex ?>
          data-action="repair:type:search"
          name="repair_ids_visible[]"
          type="text"
          autocomplete="off"
          value="<?= htmlspecialchars($RepairType->description ?? $Repair->custom_field ?? "") ?>"
          placeholder="<?= $placeholder ?>" />
        <p input-extra-text text color=green bold>
          <?= $RepairType ? $RepairType->formatted_price() : "0,00"; ?> €
        </p>

        <?php if ($editable) : ?>
          <mbutton input-delete size=mid circled material icon-only hoverable>
            <mi text size=wide color=red>close</mi>
          </mbutton>
        <?php endif; ?>

        <input type=hidden name="repair_ids[]" value="<?= $RepairType->id ?? $Repair->custom_field ?? "" ?>" />
        <div search></div>
      </div>
    <?php endforeach; ?>


    <?php if ($editable) : ?>
      <?php $tabindex++ ?>

      <div input material has-icon has-text has-delete w100 has-search clone>
        <mi input-type-icon>add</mi>
        <input
          tabindex=<?= $tabindex ?>
          data-action="repair:type:search"
          name="repair_ids_visible[]"
          type="text"
          autocomplete="off"
          placeholder="<?= $placeholder ?>" />
        <p input-extra-text text color=green bold>0,00 €</p>
        <mbutton input-delete disabled size=mid circled material icon-only hoverable>
          <mi text size=wide color=red>close</mi>
        </mbutton>
        <input type=hidden name="repair_ids[]" />
        <div search></div>
      </div>
      <?php else :
      if (!$Repairs->count()) : ?>
        <div fl gap alic jucsb rounded="wide" outlined p24>
          <div fl alic gap="smol+" flexone>
            <mi wide>build</mi>
            <div fl gap="smol" alic w100>
              <div fl gap="smol" jucc alic p12 rounded filled slight w100>
                <p text smolplus>Keine Reparaturen</p>
              </div>
            </div>
          </div>
          <mi style="height:1.4em;min-width:1.4em;" mid circled fl alic jucc color="dark-orange" background="light-orange">trip_origin</mi>
        </div>
    <?php
      endif;
    endif;
    ?>
  </div>
</section>