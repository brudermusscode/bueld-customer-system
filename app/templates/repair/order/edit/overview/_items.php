<?php

use Bruder\Model\Repair\RepairOrder;

/**
 * @var RepairOrder $RepairOrder
 */

?>

<section repairs fl fldircol gap=smol+>
  <a href="/repair/order/edit/<?= $RepairOrder->id ?>/items#activeObject" no-scroll-top>
    <div fl fldircol gap rounded=wide outlined p24 clickable>
      <div fl gap alic jucsb>
        <div fl alic gap=smol+>
          <mi wide>category</mi>
          <div fl fldircol gap=smolest>
            <p text slight smolplus>Voraussichtliche Reparaturen sind unverbindlich und können sich im Laufe der Reparatur noch ändern</p>
          </div>
        </div>
        <?php if (!$RepairOrder->items->count()) : ?>
          <mi style="height:1.4em;min-width:1.4em;" mid circled fl alic jucc color=dark-orange background=light-orange>trip_origin</mi>
        <?php else : ?>
          <mi style="height:1.4em;min-width:1.4em;" mid circled fl alic jucc color=dark-green background=light-green>done</mi>
        <?php endif; ?>
      </div>

      <div fl fldircol>
        <?php if (!$RepairOrder->items->count()) : ?>
          <div fl gap=smol jucc alic p12 rounded filled slight>
            <p tac text smolplus mb mt>Keine Reparaturen ausgewählt</p>
          </div>
          <?php else:
          foreach ($RepairOrder->items as $Item) : ?>
            <div fl alic gap flexone>
              <mi mid bold color=green style=margin-left:4px;>done</mi>
              <div fl jucsb alic gap=smol flexone>
                <p text><?= htmlspecialchars($Item->type->description ?? $Item->custom_field ?? "Keine Beschreibung verfügbar"); ?></p>
                <p text bold color=green>
                  <?= $Item->type?->formatted_price() ?? "0,00" ?> €
                </p>
              </div>
            </div>
        <?php endforeach;
        endif; ?>
      </div>
    </div>
  </a>

  <?php if ($RepairOrder->items->count()) : ?>
    <div fl fldircol gap=smoler style=padding-right:2px;>
      <div fl jucend gap main-distance slighter tar>
        <p text smol>Reparaturen gesamt</p>
        <p text bold style="width:7.2em;"><?= number_format($RepairOrder->items_price(), 2, ",", ".") ?> €</p>
      </div>
    </div>
  <?php endif; ?>
</section>