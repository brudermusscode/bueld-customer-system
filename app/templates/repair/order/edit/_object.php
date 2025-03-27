<?php

use Bruder\Model\Repair\RepairOrder;

/**
 * @var string $base_url
 * @var RepairOrder $RepairOrder
 */

/**
 * @var string
 */
$page_title = "object";

?>

<timeline posrel>
  <timeline-line></timeline-line>

  <?php

  /**
   * Include all steps previews for passed ones.
   */
  include dirname(__DIR__) . "/_progress_previous.php"; ?>

  <div id=activeObject pt42>
    <timeline-option active>
      <timeline-dot>
        <div class="circle-loader"></div>
        <mi></mi>
      </timeline-dot>

      <section fl fldircol gap>
        <div>
          <p text bold midplus>Marke & Modell</p>
          <p text>Wähle ein Objekt oder erstelle ein neues</p>
        </div>

        <div fl fldircol content-gap
          <?= $RepairOrder->is_leasing ? "active" : "" ?>>
          <?php

          /**
           * Show this when no customer is set.
           */
          if (!$RepairOrder->customer) : ?>

            <tipp-box rounded filled background=light-orange color=dark-orange>
              <mi>warning</mi>
              <p>
                <strong>Wähle erst einen Kunden aus</strong> oder erstelle einen neuen.
              </p>
            </tipp-box>

            <div fl jucend>
              <a href="<?= "$base_url/customer#activeObject" ?>" no-scroll-top>
                <mbutton has-icon=right size=mid material background=company color=light>
                  Kunden auswählen
                  <mi>arrow_forward</mi>
                </mbutton>
              </a>
            </div>

          <?php else :

            if (
              $RepairOrder->type === "bike" && $Customer?->bikes->count()
              || $RepairOrder->type === "sewing" && $Customer?->sewing_machines->count()
            ) :

              /**
               * Include all existing leasings and give the
               * employee the option to choose from one or create
               * a new one.
               */
              include __DIR__ . "/object/_from_existing.php";
            else :

              /**
               * Include the partial for creating a new leasing
               * including adding a customer object.
               */
              include __DIR__ . "/object/_new.php";
            endif; ?>

            <?php

            /**
             * Include further and back buttons.
             */
            include dirname(__DIR__) . "/_progress_options.php"; ?>

          <?php endif; ?>
        </div>

      </section>
    </timeline-option>
  </div>

  <?php

  /**
   * Include all steps previews for upcoming ones.
   */
  include dirname(__DIR__) . "/_progress_next.php"; ?>

</timeline>