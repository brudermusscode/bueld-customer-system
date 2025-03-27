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
          <mi wide>build</mi>
          <div fl fldircol gap=smolest>
            <p text slight smolplus>Teile, die am Gerät ausgetauscht werden müssen</p>
          </div>
        </div>
        <?php if (!$RepairOrder->parts->count()) : ?>
          <mi style="height:1.4em;min-width:1.4em;" mid circled fl alic jucc color=dark-orange background=light-orange>trip_origin</mi>
        <?php else : ?>
          <mi style="height:1.4em;min-width:1.4em;" mid circled fl alic jucc color=dark-green background=light-green>done</mi>
        <?php endif; ?>
      </div>

      <div fl fldircol>
        <?php if (!$RepairOrder->parts->count()) : ?>
          <div fl gap=smol jucc alic p12 rounded filled slight>
            <p tac text smolplus mb mt>Keine Ersatzteile ausgewählt</p>
          </div>
          <?php else:
          foreach ($RepairOrder->parts as $OrderPart) :

            /**
             * @var RepairOrderPart $OrderPart
             */

          ?>
            <div fl alic gap flexone>
              <mi mid bold color=green style=margin-left:4px;>done</mi>
              <div fl jucsb alic gap=smol flexone>
                <p text><?= htmlspecialchars($OrderPart->part->name ?? $OrderPart->custom_field ?? "Keine Beschreibung verfügbar"); ?></p>
                <p text bold color=green>
                  <?= $OrderPart->formatted_price() ?> €
                </p>
              </div>
            </div>
        <?php endforeach;
        endif; ?>
      </div>
    </div>
  </a>

  <?php if ($RepairOrder->parts->count()) : ?>
    <div fl jucend gap main-distance slighter tar>
      <p text smol>Ersatzteile gesamt</p>
      <p text bold style="width:7.2em;"><?= number_format($RepairOrder->parts_price(), 2, ",", ".") ?> €</p>
    </div>
  <?php endif; ?>
</section>