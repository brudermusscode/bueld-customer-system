<?php

use Bruder\Model\Repair\RepairOrder;
use Bruder\Model\Leasing\LeasingCompany;

/**
 * @var string $base_url
 * @var RepairOrder $RepairOrder
 */

/**
 * @var string
 */
$page_title = "leasing";

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
          <p text bold midplus>Leasing</p>
          <p text>Wähle Leasing für das ausgewählte Fahrrad</p>
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

          <?php elseif ($RepairOrder->type !== "bike") : ?>

            <tipp-box rounded filled background=light-orange color=dark-orange>
              <mi>warning</mi>
              <p>Es kann kein Leasing für dieses Gerät hinzugefügt werden</p>
            </tipp-box>

            <div fl jucend>
              <a href="<?= "$back_uri" ?>" no-scroll-top>
                <mbutton has-icon=right size=mid material background=company color=light>
                  Zurück
                  <mi>arrow_forward</mi>
                </mbutton>
              </a>
            </div>

          <?php else : ?>

            <?php

            /**
             * @var ?Leasing
             */
            $Leasing = $Object?->leasing;

            /**
             * @var bool
             */
            $is_leasing = $Leasing || $RepairOrder->is_leasing === 1;

            /**
             * @var bool
             */
            $is_not_leasing = $RepairOrder->is_leasing === 0;

            ?>

            <div fl fldircol content-gap leasing-information <?= $is_leasing && !$is_not_leasing ? "active" : "" ?>>

              <?php

              /**
               * If there is a leasing predefined for the selected
               * object, we don't want the employee to be able to
               * edit it. Leasing is and stays unless the contract
               * ends in time or abnormally.
               */
              if ($Leasing) : ?>

                <div fl fldircol gap=smol>
                  <p text bold>Ausgewählter Leasing-Vertrag</p>
                  <div background=active outlined p24 rounded=mid>
                    <div fl alic gap=smol+>
                      <mi wide>token</mi>
                      <div>
                        <p text stdplus>Leasing bei <strong><?= $Leasing->company->name ?></strong></p>
                        <p text>Vertragsnummer &middot; <span color=company3><?= $Leasing->contract_id ?></span></p>
                      </div>
                    </div>
                  </div>
                </div>

                <input type="hidden" name=leasing_id value=<?= $Leasing->id ?> />
                <input type="hidden" name=is_leasing value=yes />

              <?php

                /**
                 * The below case is shown, if no leasing has been
                 * linked to the selected object yet. It's for
                 * adding one.
                 */
              else : ?>
                <mradio material size=wide fl gap=smoler unselect=false>
                  <radio-option set-active=leasing-information data-value=yes flone outlined
                    <?= $is_leasing && !$is_not_leasing ? "active" : "" ?>>
                    Ja
                  </radio-option>
                  <radio-option unset-active=leasing-information data-value=no flone outlined
                    <?= $is_not_leasing ? "active" : "" ?>>
                    Nein
                  </radio-option>

                  <input radio-input type=hidden name=is_leasing
                    value="<?= $is_leasing && !$is_not_leasing ? "yes" : ($is_not_leasing ? "no" : "") ?>" />
                </mradio>

                <div show-active fl fldircol content-gap>
                  <div fl fldircol gap>
                    <div posrel>
                      <timeline-sub-icon circled fl alic jucc>
                        <mi mid>token</mi>
                      </timeline-sub-icon>

                      <p text midler bold>Allgemein</p>
                      <!-- <p text slight>Informationen zum Leasing-Vertrag</p> -->
                    </div>

                    <div fl alic jucsb gap>
                      <div fl fldircol style=width:18.4em;>
                        <p text bold>Vertragsnummer</p>
                        <p text smolplus slight>Oder Leasing-Nummer</p>
                      </div>
                      <div input material has-icon=right flone>
                        <mi>contract</mi>
                        <input
                          autofocus

                          name=leasing_contract_id
                          tabindex=1
                          type="text"
                          enter-submitable
                          placeholder="123456789"
                          value="<?= $RepairOrder->leasing?->contract_id ?>" />
                      </div>
                    </div>

                    <div fl fldircol gap=smol+>
                      <div>
                        <p text bold>Unternehmen</p>
                        <p text slight>Wähle das Unternehmen, wo das Leasing angemeldet wurde</p>
                      </div>

                      <mradio material size=wide fl gap=smoler flex-wrap unselect=false>

                        <?php

                        /**
                         * @var LeasingCompany
                         */
                        $LeasingCompanies = LeasingCompany::all();

                        foreach ($LeasingCompanies as $Company) :

                        ?>

                          <radio-option style=flex-basis:48%;max-width:49.6%;
                            data-action="leasing-company:select"
                            <?= $Company->requires_inspection_id ? "requires-inspection-id" : "" ?>
                            <?= $Company?->is($RepairOrder->leasing?->company) ? "active" : "" ?>
                            data-value=<?= $Company->id ?> flone material outlined>
                            <?= $Company->name; ?>
                          </radio-option>

                        <?php endforeach; ?>

                        <input radio-input type=hidden name=leasing_company_id value="<?= $RepairOrder->leasing?->leasing_company_id ?>" />
                      </mradio>
                    </div>
                  </div>
                </div>
              <?php endif; ?>

              <div leasing-inspection
                <?= $Leasing?->company->requires_inspection_id ? "" : "disn" ?>
                fl alic jucsb gap>
                <div fl fldircol style=width:18.4em; posrel>
                  <timeline-sub-icon circled fl alic jucc>
                    <mi mid>frame_inspect</mi>
                  </timeline-sub-icon>
                  <p text bold>Inspektions-Nummer</p>
                  <p text smolplus slight>Vom Leasing-Unternehmen bereitgestellt</p>
                </div>
                <div input material flone>
                  <input
                    autofocus

                    name=leasing_inspection_id
                    tabindex=2
                    type="text"
                    enter-submitable
                    placeholder="123456789"
                    value="<?= $RepairOrder->leasing_inspection_id ?>" />
                </div>
              </div>

              <?php

              /**
               * Show a link to the sales portal for the chosen
               * leasing company.
               */
              if ($Leasing?->company->web_url && 1 === 2) : ?>
                <div fl fldircol gap=smol style=margin-top:-34px;>
                  <div ml=mid style="height:24px;width:0px;border-left: 4px dotted rgba(0,0,0,.12);"></div>
                  <tipp-box rounded=mid background=light-orange color=dark-orange>
                    <mi>release_alert</mi>
                    <p text>Erstelle einen Fall über das bereitgestellte <strong>Händler-Portal von <?= $Leasing->company->name ?></strong>.</p>
                  </tipp-box>

                  <?php if ($Leasing->company->web_url) : ?>
                    <a href="<?= $Leasing->company->web_url ?>" extern target=_blank>
                      <div outlined rounded=mid p24 fl alic jucsb clickable>
                        <div>
                          <p text stdplus bold>Zum <?= $Leasing->company->name ?> Händler-Portal</p>
                          <p text smol slighter><?= $Leasing->company->web_url ?></p>
                        </div>
                        <mi midler>open_in_new</mi>
                      </div>
                    </a>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>

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