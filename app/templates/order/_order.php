<?php

use Bruder\Model\Repair\RepairOrder;
use Bruder\Time\Time;

/**
 * @var RepairOrder $Order
 * @var int $key
 */

/**
 * @var string
 */
$href = !in_array($Order->status, ["REPAIR", "DONE"])
  ? $Order->next_step_uri()
  : "/repair/order/$Order->id" . ($Order->in_repair() ? "#activeObject" : "");

?>

<div posrel>

  <div style="position:absolute;top:12px;left:12px;z-index:1;">
    <form request="bookmark:create" reload>
      <input type=hidden name=id value=<?= $Order->id ?> />
      <input type=hidden name=type value=order />
      <?php if ($Order->bookmark) : ?>
        <mbutton submit-closest material icon-only size=mid hoverable color=red has-tooltip=bottom>
          <mi>bookmark_remove</mi>
          <div ttooltip>
            Nicht merken
          </div>
        </mbutton>
      <?php else : ?>
        <mbutton submit-closest material icon-only size=mid hoverable has-tooltip=bottom>
          <mi slight>bookmark</mi>
          <div ttooltip>
            Merken
          </div>
        </mbutton>
      <?php endif; ?>
    </form>
  </div>

  <a href="<?= $href ?>">
    <div fl alic jucsb gap=smol+ hoverable rounded p12 style=padding-right:32px; <?= ($key % 2 === 0) ? "filled" : "" ?>>
      <div fl alic gap=smol+>
        <mbutton material icon-only size=mid hoverable has-tooltip=bottom style="opacity:0;visibility:hidden;">
          <mi slight>bookmark</mi>
          <div ttooltip>
            Merken
          </div>
        </mbutton>

        <div style="width:188px;" fl alic gap=smol>
          <mi mid semibold><?= $Order->display_type_icon() ?></mi>
          <p text smolplus semibold><?= $Order->display_type() ?></p>
        </div>

        <!-- <p text smol semibold style="width:88px;"><?= $Order->reference_id ?></p> -->

        <div style="width:188px;">
          <?php if ($Order->customer_id) : ?>
            <p text smoler ttup slighter semibold><?= $Order->customer->city ?></p>
            <p text smolplus semibold>
              <?=
              $Order->customer->exists
                ? $Order->customer->full_name(shortened: true)
                : "Gelöscht"
              ?>
            </p>
          <?php else : ?>
            <p text smol slight><i>Kein Kunde</i></p>
          <?php endif; ?>
        </div>

        <?php if ($Order->employee) : ?>
          <p text smol semibold style="width:188px;"><?= $Order->employee->full_name() ?></p>
        <?php else : ?>
          <p text smol style="width:188px;" slight><i>Kein Mitarbeiter</i></p>
        <?php endif; ?>
      </div>

      <div fl alic gap=smol>
        <div style="width:180px;" fl>
          <mchip outlined tag has-icon=left <?= $Order->display_status_color(); ?>>
            <mi midler><?= $Order->display_status_icon() ?></mi>
            <?= $Order->display_status() ?>
          </mchip>
        </div>

        <p text smol slight style=width:118px;text-align:right;><?= Time::ago($Order->updated_at) ?></p>
      </div>
    </div>
  </a>
</div>