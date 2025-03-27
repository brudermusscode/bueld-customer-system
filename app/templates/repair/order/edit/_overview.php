<?php

use Bruder\Model\Repair\RepairOrder;

/**
 * @var string $base_url
 * @var RepairOrder $RepairOrder
 */

/**
 * @var string
 */
$page_title = "overview";

?>

<timeline posrel>
  <timeline-line></timeline-line>

  <?php

  /**
   * Include all steps previews for passed ones.
   */
  include dirname(__DIR__) . "/_progress_previous.php"; ?>

  <div id=activeObject pt42>
    <timeline-option
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

        <?php if ($submit_error) : ?>

          <p outlined p24 rounded=mid text bold tac color=red>
            <?=
            match ($submit_error) {
              "no_order_created" => "Erstelle erst einen Auftrag",
              "no_employee" => "Wähle dich als Mitarbeiter aus",
              "no_customer" => "Wähle einen Kunden aus",
              "no_type" => "Wähle einen Auftrag-Typen aus",
              "no_object" => "Wähle ein Objekt aus, das repariert werden soll",
              "invalid_leasing_combination" => "Füge eine Inspektionsnummer zum Leasing hinzu",
              default => "",
            };
            ?>
          </p>

          <tipp-box rounded filled background=light-orange color=dark-orange>
            <mi>warning</mi>
            Der Auftrag kann erst aufgegeben werden, wenn alle vorherigen Felder ausgefüllt sind.
          </tipp-box>

        <?php else : ?>

          <div fl fldircol gap=smol>

            <?php if (!$RepairOrder->is_done()) : ?>
              <tipp-box rounded filled fl alic background=light-green color=dark-green>
                <mi>lightbulb</mi>
                Klicke auf einzelne Schritte oben, um sie noch einmal zu bearbeiten
              </tipp-box>
            <?php endif; ?>

            <div fl fldircol gap=smoler outlined p24 rounded=mid>
              <!-- <divide horiz mt=smol+ mb=smol+></divide> -->

              <div fl jucend gap slighter tar>
                <p text smol>Grundpreis Reparaturen & Ersatzteile</p>
                <p text bold style="width:7.2em;"><?= number_format($RepairOrder->base_price(), 2, ",", ".") ?> €</p>
              </div>

              <div fl jucend gap slighter tar>
                <p text smol>20 % Aufschlag für unvorhersehbare Ersatzteile oder Reparaturen</p>
                <p text bold style="width:7.2em;"><?= number_format($RepairOrder->base_price() * 0.2, 2, ",", ".") ?> €</p>
              </div>

              <div fl jucend gap>
                <p text bold color=green>Voraussichtliche Kosten für den Kunden</p>
                <p text bold color=green style="width:7.2em;text-align:right;"><?= $RepairOrder->prepared_price_formatted() ?> €</p>
              </div>
            </div>

            <input type=hidden name=id value=<?= $RepairOrder->id ?> />
            <input type=hidden name=status value=REPAIR />

            <div mt fl jucc>
              <?php if ($RepairOrder->in_repair() || $RepairOrder->is_done()) : ?>
                <a href="/repair/order/<?= $RepairOrder->id ?>">
                  <mbutton size=mid material background=slight has-icon=right>
                    Zur Auftragsübersicht
                    <mi>arrow_forward</mi>
                  </mbutton>
                </a>
              <?php else : ?>
                <mbutton submit-closest size=wide has-icon=left material background=company3 color=company>
                  <mi>done_all</mi>
                  Auftrag aufgeben
                </mbutton>
              <?php endif; ?>
            </div>
          </div>

        <?php endif; ?>

      </div>

    </timeline-option>
  </div>

  <?php

  /**
   * Include all steps previews for upcoming ones.
   */
  include dirname(__DIR__) . "/_progress_next.php"; ?>

</timeline>