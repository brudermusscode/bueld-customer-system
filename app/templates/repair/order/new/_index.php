<?php

/**
 * @var string
 */
$page_title = "type";

?>

<div repair-order pb62>
  <div mt=wide main-distance>
    <div content-width=smol+ fl fldircol content-gap center-content>

      <?php

      /**
       * Include heading.
       */
      include dirname(__DIR__) . "/_header.php"; ?>

      <timeline center-content posrel>
        <timeline-line></timeline-line>

        <timeline-option active>
          <timeline-dot>
            <div class="circle-loader"></div>
            <mi></mi>
          </timeline-dot>

          <form data-form="repair:order:create">
            <section fl fldircol content-gap>
              <div fl fldircol gap=smol+>
                <div>
                  <p text bold midplus>Art</p>
                  <p text>Wähle aus, was repariert werden soll</p>
                </div>

                <tipp-box rounded background=light-green>
                  <mi>help</mi>
                  Die Auswahl erstellt automatisch einen neuen Auftrag, der anschließend bearbeitet werden kann.
                </tipp-box>

                <mradio material size=wide fl fldircol gap=smol>
                  <radio-option submit-closest data-value=bike flone has-icon=left material outlined>
                    <mi>directions_bike</mi>
                    Fahrrad
                  </radio-option>
                  <radio-option submit-closest data-value=sewing flone has-icon=left material outlined>
                    <mi>group_work</mi>
                    Nähmaschine
                  </radio-option>

                  <input radio-input type=hidden name=type value />
                </mradio>
              </div>
            </section>
          </form>
        </timeline-option>

        <timeline-option>
          <timeline-dot>
            <mi></mi>
          </timeline-dot>


          <div slight outlined rounded=mid p24 fl alic gap=smol+>
            <div>
              <p text bold>Weitere Optionen</p>
              <p text>Wähle einen Reparatur-Typ aus, um weitere Optionen zu sehen</p>
            </div>
          </div>
        </timeline-option>

      </timeline>
    </div>
  </div>
</div>