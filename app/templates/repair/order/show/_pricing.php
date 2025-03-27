<?php

use Bruder\Model\Repair\RepairOrder;

/**
 * @var string $base_url
 * @var RepairOrder $RepairOrder
 */

?>

<timeline-option id=activeObject
  <?= $RepairOrder->in_repair() || $RepairOrder->is_done() ? "done" : "active" ?>
  posrel fl fldircol gap=mid>
  <timeline-dot <?= $RepairOrder->in_repair() || $RepairOrder->is_done() ? "style=top:-.7em" : "" ?>>
    <div class="circle-loader"></div>
    <mi></mi>
  </timeline-dot>

  <div fl fldircol gap>
    <div>
      <p text bold midplus>
        <?= $RepairOrder->is_done() ? "Alles fertig!" : "Stimmt alles?" ?>
      </p>
      <p text>
        <?= $RepairOrder->is_done() ? "Dieser Auftrag ist bereits abgeschlossen" : "Überprüfe deine Eingaben und die Kosten" ?>
      </p>
    </div>

    <?php if (!$RepairOrder || !$RepairOrder->customer || !$RepairOrder->employee || !$RepairOrder->type) : ?>

      <tipp-box rounded filled background=light-orange color=dark-orange>
        <mi>warning</mi>
        Der Auftrag kann erst aufgegeben werden, wenn alle vorherigen Felder ausgefüllt sind.
      </tipp-box>

    <?php else : ?>

      <div fl fldircol gap=smol+>
        <div fl fldircol gap=smoler outlined p24 rounded=mid>
          <!-- <divide horiz mt=smol+ mb=smol+></divide> -->

          <div fl jucend alic gap slighter tar>
            <p text>Grundpreis Reparaturen & Ersatzteile</p>
            <p text bold style="width:7.2em;">
              <?= number_format($RepairOrder->parts_price() + $RepairOrder->items_price(), 2, ",", ".") ?> €
            </p>
          </div>

          <div fl jucend alic gap>
            <p text bold color=green>Gesamtkosten</p>
            <p text bold color=green style="width:7.2em;text-align:right;">
              <?= $RepairOrder->real_price_formatted() ?> €
            </p>
          </div>

          <div fl jucend alic gap slighter>
            <p text>Enthaltene MwSt.</p>
            <p text bold style="width:7.2em;text-align:right;">
              <?= $RepairOrder->real_price_ust_formatted() ?> €
            </p>
          </div>
        </div>

        <div mt fl jucc>
          <?php if (!$RepairOrder->is_done()) : ?>
            <form data-form="repair:order:finish" fl fldircol>
              <input type=hidden name=id value=<?= $RepairOrder->id ?> />
              <input type=hidden name=status value=DONE />
              <mbutton submit-closest size=wide has-icon=left material background=green color=light>
                <mi>done_all</mi>
                Rechnung erstellen
              </mbutton>
            </form>
          <?php endif; ?>
        </div>
      </div>

    <?php endif; ?>

  </div>
</timeline-option>