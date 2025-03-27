<?php

use Bruder\Model\Repair\RepairOrder;

/**
 * @var RepairOrder $RepairOrder
 */

?>

<section repairs fl fldircol gap>
  <a href="/repair/order/edit/<?= $RepairOrder->id ?>/items#activeObject" no-scroll-top>
    <div fl gap alic jucsb rounded=wide outlined p24 clickable>
      <div fl alic gap=smol+ flexone>
        <mi wide><?= $RepairOrder->display_type_icon() ?></mi>
        <div fl gap=smol alic w100>
          <?php if (!$RepairOrder->brand) : ?>
            <div fl gap=smol jucc alic p12 rounded filled slight w100>
              <p text smolplus>Keine Marke gewählt</p>
            </div>
          <?php else : ?>
            <div>
              <p text semibold><?= $RepairOrder->brand->name ?></p>
              <p text smolplus><?= $RepairOrder->display_type() ?></p>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <?php if (!$RepairOrder->brand) : ?>
        <mi style="height:1.4em;min-width:1.4em;" mid circled fl alic jucc color=dark-orange background=light-orange>trip_origin</mi>
      <?php else : ?>
        <mi style="height:1.4em;min-width:1.4em;" mid circled fl alic jucc color=dark-green background=light-green>done</mi>
      <?php endif; ?>
    </div>
  </a>
</section>