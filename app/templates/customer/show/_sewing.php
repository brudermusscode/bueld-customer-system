<?php

use Bruder\Time\Time;
use Bruder\Model\Repair\RepairOrder;

/**
 * @var Customer $Customer
 * @var string $base_url
 * @var string $object_type => sewing
 */

?>

<div object-list>

  <?php

  /**
   * Include page type menu.
   */
  include __DIR__ . "/_menu.php"  ?>

  <?php

  /**
   * * Sewing Machines List
   */

  ?>
  <div fl fldircol gap=smol+>
    <div fl fldircol gap=smol+>

      <?php if (!$Customer->sewing_machines->count()) : ?>
        <div filled=darker pv62 rounded fl fldircol gap=smol>
          <mi wider>communities</mi>
          <p tac smol semibold text>Keine Nähmaschine hinterlegt</p>
        </div>
        <?php else :
        foreach ($Customer->sewing_machines as $key => $Machine) : ?>
          <div
            <?= $key !== ($Customer->sewing_machines->count() - 1) ? '' : "" ?>>
            <div filled=lighter rounded fl fldircol style="gap:10px;">
              <div p12 rounded fl alic style="gap:10px;">
                <mi midplus><?= $Machine->display_type_icon() ?></mi>
                <div flex-truncate>
                  <p text smolplus bold trimt><?= $Machine->brand->name ?></p>
                  <p text smoler bold trimt style=line-height:1;>
                    <?=
                    $Machine->object_unique_identifier
                      ? "<span color=company3>" . $Machine->object_unique_identifier . "</span>"
                      : ""
                    ?>
                  </p>
                </div>
              </div>
            </div>
          </div>
      <?php
        endforeach;
      endif;
      ?>
    </div>
  </div>
</div>

<div sidebar-main-content fl fldircol content-gap flone ph42 pb62>

  <?php

  /**
   * Include header partial.
   */
  include __DIR__ . "/_header.php"; ?>



  <!--- GRID CONTENT --->
  <div fl gap>
    <div flexone>

      <?php

      /**
       * @var ?RepairOrder
       */
      $Orders = $Customer->orders()
        ->where("type", "sewing")
        ->orderByDesc("updated_at")
        ->get();

      ?>

      <div fl fldircol gap=smol+>
        <p text midler ph24>
          <strong>Aufträge</strong> <?php if ($Orders->count()) : ?>&middot; <span color=company3><?= $Orders->count(); ?></span><?php endif; ?>
        </p>
        <div fl fldircol gap=smol>

          <?php if (!$Orders->count()) : ?>

            <div filled slighter p62 rounded fl fldircol gap=smol>
              <mi wider>deployed_code_account</mi>
              <p tac text>Keine Aufträge für <?= $Customer->firstname ?></p>
            </div>

            <?php else : foreach ($Orders as $RepairOrder) : ?>
              <div p24 outlined rounded fl fldircol gap=smol>
                <div flone fl jucsb alic>
                  <p text ttup smolplus>Referenz &middot; <span color=company2><?= $RepairOrder->reference_id ?></span></p>
                  <p text slight smol><?= Time::ago($RepairOrder->updated_at) ?></p>
                </div>
                <p text midler bold><?= $RepairOrder->customer_object->brand->name ?></p>
              </div>
          <?php endforeach;
          endif; ?>
        </div>
      </div>
    </div>

  </div>
</div>