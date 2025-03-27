<?php

use Bruder\Model\Customer;

/**
 * @var Customer $Customer
 * @var int $key
 */

?>

<div posrel>

  <div style="position:absolute;top:12px;left:12px;">
    <form request="bookmark:create" reload>
      <input type=hidden name=id value=<?= $Customer->id ?> />
      <input type=hidden name=type value=customer />
      <?php if ($Customer->bookmark) : ?>
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

  <a href="/customer/<?= $Customer->id ?>">
    <div fl alic jucsb gap=smol+ hoverable rounded p12 style=padding-right:32px; <?= ($key % 2 === 0) ? "filled" : "" ?>>
      <div fl alic gap=smol+>
        <mbutton style="visibility:hidden;opacity:0;" material icon-only size=mid hoverable has-tooltip=bottom>
          <mi slight>bookmark</mi>
          <div ttooltip>
            Merken
          </div>
        </mbutton>

        <p text smolplus semibold style="width:2.4em;"><?= $Customer->id ?></p>

        <div fl alic gap=smol+ style=width:280px;>
          <mi stdplus><?= $Customer->sex === "m" ? "face" : "face_2" ?></mi>
          <div fl fldircol>
            <p smolplus text semibold trimt><?= $Customer->full_name() ?></p>
            <!-- <p text smol slight><?= $Customer->mail ?></p> -->
          </div>
        </div>

        <p text semibold style=width:20px;>
          <?=
          $Customer->orders()
            ->count()
          ?>
        </p>
      </div>

      <div fl gap=smol+ alic>
        <div style=width:120px; fl>
          <mchip background=slight tag has-icon=left>
            <mi>share_location</mi>
            <?= $Customer->city ?>
          </mchip>
        </div>

        <p text smol slight style=width:118px;text-align:right;><?= $Customer->last_activity() ?></p>
      </div>

    </div>
  </a>
</div>