<?php

use Bruder\Model\Repair\RepairOrderPart;
use Bruder\Model\Repair\RepairPart;

/**
 * @var RepairOrderPart $OrderPart
 * @var bool $editable
 */

/**
 * @var ?RepairPart
 */
$Part = $OrderPart->part;

/**
 * @var bool
 */
$editable ??= true;

?>

<div input material has-icon has-text <?= $editable ? "has-delete" : "" ?> w100>
  <mi input-type-icon color=green bold>done</mi>
  <input
    disabled=no-opacity
    autocomplete="off"
    type="text"
    value="<?= htmlspecialchars($OrderPart->custom_field ?? $Part->name ?? "") ?>" />
  <p input-extra-text text color=green bold><?= "(" . $OrderPart->amount . "x) " . $OrderPart->formatted_price(); ?> €</p>
  <?php if ($editable) : ?>
    <mbutton data-action="repair:order:part:delete" data-id="<?= $OrderPart->id ?>" input-delete size=mid circled material icon-only hoverable>
      <mi text size=wide color=red>close</mi>
    </mbutton>
  <?php endif; ?>
</div>