<?php

use Bruder\Model\Repair\RepairOrder;

/**
 * @var string $base_url
 * @var RepairOrder $RepairOrder
 * @var bool $editable
 */

/**
 * @var RepairOrder
 */
$Order = $RepairOrder;

/**
 * @var string
 */
$page_title = "items";

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

      <div fl fldircol gap>
        <div>
          <p text bold midplus>Was soll repariert/ersetzt werden?</p>
          <p text>Erstelle eine Liste aller vorab benötigten Ersatzteile & Reparaturen</p>
        </div>

        <?php if (!$RepairOrder->customer) : ?>

          <tipp-box rounded filled background=light-orange color=dark-orange>
            <mi>warning</mi>
            <p>
              <strong>Wähle erst einen Kunden aus</strong> oder erstelle einen neuen, um das Leasing zu bearbeiten.
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

          /**
           * Customer addons.
           */
          if ($RepairOrder->type === "sewing") :

            /**
             * @var object
             */
            $addons = json_decode($RepairOrder->customer_addons ?? "{}");

          ?>
            <div mt=smol fl fldircol gap outlined rounded=mid>
              <div p24 style=padding-bottom:0;padding-right:24px;>
                <p text midler bold>Vom Kunden mitgebracht</p>
                <p text slight>Kreuze an, was der Kunde zu seiner Maschine mitgebracht hat</p>
              </div>

              <div fl fldircol gap=smol p24 style=padding-bottom:0;padding-top:2px;padding-right:32px;>
                <div fl alic gap=smol+>
                  <toggle-switch
                    toggled="<?= isset($addons->starter) && $addons->starter ? "true" : "false" ?>">
                    <div class="toggle_switch__inr">
                      <div class="toggle_switch__switcher"></div>
                      <input type="hidden"
                        name="customer_addons[starter]"
                        value="<?= $addons->starter ?? 0 ?>">
                      <div fl fldirrow justify-content="center">
                        <div fl fldirrow justify-content="space-between" align-items="center"></div>
                      </div>
                    </div>
                  </toggle-switch>
                  <div>
                    <p text bold>Anlasser</p>
                  </div>
                </div>
                <div fl alic gap=smol+>
                  <toggle-switch
                    toggled="<?= isset($addons->suitcase) && $addons->suitcase ? "true" : "false" ?>">
                    <div class="toggle_switch__inr">
                      <div class="toggle_switch__switcher"></div>
                      <input type="hidden"
                        name="customer_addons[suitcase]"
                        value="<?= $addons->suitcase ?? 0 ?>">
                      <div fl fldirrow justify-content="center">
                        <div fl fldirrow justify-content="space-between" align-items="center"></div>
                      </div>
                    </div>
                  </toggle-switch>
                  <div>
                    <p text bold>Koffer</p>
                  </div>
                </div>
                <div fl alic gap=smol+>
                  <toggle-switch
                    toggled="<?= isset($addons->bobbin_case) && $addons->bobbin_case ? "true" : "false" ?>">
                    <div class="toggle_switch__inr">
                      <div class="toggle_switch__switcher"></div>
                      <input type="hidden"
                        name="customer_addons[bobbin_case]"
                        value="<?= $addons->bobbin_case ?? 0 ?>">
                      <div fl fldirrow justify-content="center">
                        <div fl fldirrow justify-content="space-between" align-items="center"></div>
                      </div>
                    </div>
                  </toggle-switch>
                  <div>
                    <p text bold>Spulenkapsel</p>
                  </div>
                </div>
              </div>

              <div fl gap=smol+ alic rounded filled=light p24 style=padding-right:32px;>
                <toggle-switch
                  toggled="<?= isset($addons->others) && $addons->others ? "true" : "false" ?>">
                  <div class="toggle_switch__inr">
                    <div class="toggle_switch__switcher"></div>
                    <input type="hidden"
                      name="customer_addons[others]"
                      value="<?= $addons->others ?? 0 ?>">
                    <div fl fldirrow justify-content="center">
                      <div fl fldirrow justify-content="space-between" align-items="center"></div>
                    </div>
                  </div>
                </toggle-switch>
                <div>
                  <p text bold>Sonstiges</p>
                  <p text slight smolplus>Es wurden mehr als die obigen Teile mitgebracht</p>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <div fl fldircol content-gap>
            <!--- REPAIR TYPES --->
            <div fl fldircol gap=smol flex-wrap>
              <p text midler bold>Reparaturen</p>

              <?php include __DIR__ . "/items/_types.php"; ?>
            </div>

            <!--- REPAIR PARTS --->
            <div fl fldircol gap=smol flex-wrap>
              <div fl alic jucsb>
                <p text midler bold>Ersatzteile</p>
              </div>

              <?php include __DIR__ . "/items/_parts.php"; ?>
            </div>
          </div>

          <input type=hidden name=type value=<?= $RepairOrder->type ?> />

          <?php $tabindex++ ?>

          <?php

          /**
           * Include further and back buttons.
           */
          include dirname(__DIR__) . "/_progress_options.php"; ?>

        <?php endif; // no customer
        ?>

      </div>
    </timeline-option>
  </div>

  <?php

  /**
   * Include all steps previews for upcoming ones.
   */
  include dirname(__DIR__) . "/_progress_next.php"; ?>

</timeline>