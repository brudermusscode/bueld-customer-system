<?php

use Bruder\Model\Repair\RepairType;

/**
 * @var RepairType $Type
 * @var int $key
 */

?>

<div fl alic jucsb gap=smol+ hoverable rounded p6 style=padding-left:24px;min-height:68px; <?= ($key % 2 === 0) ? "filled" : "" ?>>
  <div fl alic gap=smol+>
    <div style="width:188px;" fl alic gap=smol>
      <mi mid semibold><?= $Type->display_type_icon() ?></mi>
      <p text smolplus semibold><?= $Type->display_type() ?></p>
    </div>


    <div style="width:60px;">
      <p text>
        <?= $Type->initial_id ?>
      </p>
    </div>

    <!-- <p text smol semibold style="width:88px;"><?= $Order->reference_id ?></p> -->

    <div style="width:600px;">
      <p text trimt>
        <?= $Type->description ?>
      </p>
    </div>
  </div>

  <div fl alic jucend gap=smol+>
    <div style="width:120px;">
      <p text>
        <?= $Type->formatted_price() ?>
      </p>
    </div>

    <mbutton <?= !LOGGED ? "disabled" : "" ?> request-get="repair:type:edit" data-id="<?= $Type->id ?>" material icon-only size=mid hoverable has-tooltip=bottom>
      <mi>edit</mi>
      <div ttooltip>
        Bearbeiten
      </div>
    </mbutton>

    <form request="repair:type:delete" responder reload>
      <input type=hidden name=id value="<?= $Type->id ?>" />
      <mbutton <?= !LOGGED ? "disabled" : "" ?> submit-closest material icon-only size=mid hoverable has-tooltip=bottom>
        <mi color=red>delete</mi>
        <div ttooltip>
          Löschen
        </div>
      </mbutton>
    </form>
  </div>
</div>