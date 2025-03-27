<?php

use Bruder\Model\Leasing\LeasingCompany;
use Bruder\Model\Repair\RepairOrder;

/**
 * @var string $base_url
 * @var RepairOrder $RepairOrder
 */

/**
 * @var string
 */
$page_title = "customer";

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
        <section customer fl fldircol gap flex-wrap
          <?= $RepairOrder->customer_id ? "done" : "" ?>>
          <div fl fldircol gap>
            <div>
              <p text mid bold>Wer ist der Kunde?</p>
              <p text>Suche einen Kunden oder erstelle einen neuen</p>
            </div>

            <input type="hidden" name="customer_id" value="<?= $RepairOrder->customer_id ?? "" ?>" />



            <!--- FULL NAME --->
            <div fl fldircol flexone gap=smol z>
              <div fl alic gap=smol>
                <div input material has-icon w100 fl fldircol gap=smol+>
                  <mi>location_away</mi>
                  <input
                    <?= $RepairOrder->customer ? "not-editable" : "autofocus" ?>
                    data-action="customer:search"
                    data-type="<?= $RepairOrder->type ?>"
                    autocomplete="off"
                    type="text"
                    name="full_name"
                    tabindex=1
                    placeholder="Vor- und Nachname oder Firma"
                    value="<?=
                            $RepairOrder->customer?->company_name
                              ?? $RepairOrder->customer?->full_name()
                              ?? ""
                            ?>" />
                </div>

                <mbutton
                  data-action="customer:edit-selection"
                  size=mid icon-only show-done material background=light-green color=dark-green>
                  <mi>edit</mi>
                </mbutton>
              </div>

              <div search-results hide-done hide-active fl fldircol gap=smoler>
                <tipp-box rounded background=light-orange color=dark-orange fl alic>
                  <mi>emergency_home</mi>
                  <p text>Bitte gib immer <strong>erst den Vornamen</strong> ein!</p>
                </tipp-box>
              </div>
            </div>
          </div>



          <!--- SWITCH: CUSTOMER IS COMPANY --->
          <div show-active=flex fl alic gap>
            <toggle-switch data-toggle="order:customer:full-name" data-toggle-closest="section[customer]" toggled="false">
              <div class="toggle_switch__inr">
                <div class="toggle_switch__switcher"></div>
                <div fl fldirrow justify-content="center">
                  <div fl fldirrow justify-content="space-between" align-items="center"></div>
                </div>

                <input name="is_company" type="hidden" value="0">
              </div>
            </toggle-switch>

            <p text bold>Kunde ist ein Unternehmen</p>
          </div>



          <!--- FIST- & LASTNAME --->
          <div data-field=order:customer:full-name disn fl fldircol gap=smol+ filled p32 rounded=mid>
            <div>
              <p text bold>Ansprechpartner</p>
              <p text smolplus slight>Daten der Person, die mit der Reparatur in den Laden gekommen ist</p>
            </div>
            <div fl gap=smol>
              <div fl fldircol flexone gap=smol>
                <div fl alic gap=smoler>
                  <div input material w100>
                    <input
                      name="firstname"
                      type="text" autocomplete="off" placeholder="Vorname" tabindex=2
                      value="<?= $RepairOrder->customer?->firstname ?? "" ?>" enter-submitable />
                  </div>
                </div>
              </div>

              <div fl fldircol flexone gap=smol>
                <div fl alic gap=smoler>
                  <div input material w100>
                    <input
                      name="lastname"
                      type="text" autocomplete="off" placeholder="Nachname" tabindex=3
                      value="<?= $RepairOrder->customer?->lastname ?? "" ?>" enter-submitable />
                  </div>
                </div>
              </div>
            </div>
          </div>



          <!--- GENDER --->
          <div show-active=flex fldircol gap=smol>
            <p text bold><strong color=red>*</strong>Geschlecht</p>
            <mradio material size=wide fl alic gap=smol unselect=false>
              <radio-option outlined rounded data-value="m" flone>Mann</radio-option>
              <radio-option outlined rounded data-value="w" flone>Frau</radio-option>

              <input name=sex type=hidden radio-input />
            </mradio>
          </div>



          <!--- MAIL & PHONE --->
          <div
            show-done=flex
            show-active=flex fl fldircol gap=smol+>
            <div fl fldircol gap=smol>
              <div fl gap=smol>
                <div fl fldircol flexone gap=smol>
                  <p text std bold>E-Mail Adresse</p>
                  <div fl alic gap=smoler>
                    <div input material has-icon w100>
                      <mi>mail</mi>
                      <input
                        name="mail"
                        type="text" autocomplete="off" placeholder="example@domain.de" tabindex=4
                        value="<?= $RepairOrder->customer?->mail ?? "" ?>" enter-submitable />
                    </div>
                  </div>
                </div>

                <div fl fldircol style="width:14em;" gap=smol>
                  <p text std bold>Telefon/Handy</p>
                  <div fl alic gap=smoler>
                    <div input material has-icon w100>
                      <mi>phone_iphone</mi>
                      <input
                        name="phone"
                        type="text" autocomplete="off" placeholder="015773602821" tabindex=5
                        value="<?= $RepairOrder->customer?->phone ?? "" ?>" enter-submitable />
                    </div>
                  </div>
                </div>
              </div>

              <div text smol bold slight fl gap=smol>
                <mi>info</mi>
                <p>Mindestens eines muss für Rückfragen angegeben werden.</p>
              </div>
            </div>
          </div>



          <!--- ADDRESS --->
          <div show-active=flex fldircol flexone gap>

            <!--- Will only be filled when an existing customer has been chosen --->
            <input
              name="address"
              type="hidden" value='<?= $RepairOrder->customer?->full_address_json() ?? "" ?>' />
            <!--- cool, isn't it? --->

            <div fl fldircol gap=smol flexone>
              <p text std bold><strong color=red>*</strong>Straße & Hausnummer</p>
              <div input material has-icon>
                <mi>location_on</mi>
                <input
                  name="address_line_1"
                  type="text" autocomplete="off" enter-submitable placeholder="Mozartstr. 12" tabindex=6 />
              </div>
            </div>

            <div fl fldircol gap=smol flexone>
              <p text std bold><strong color=red>*</strong>Postleitzahl & Stadt</p>
              <div input material>
                <input
                  name="postcode_or_city"
                  type="text" autocomplete="off" placeholder="49377 Vechta" tabindex=7 enter-submitable />
              </div>

              <div text smol bold slight fl gap=smol>
                <mi>info</mi>
                <p>Es kann nur die Postleitzahl angegeben werden.</p>
              </div>
            </div>

            <div show-done fldircol gap=smol>
              <p text std bold>Addresse</p>

              <tipp-box filled rounded>
                <mi>done_all</mi>
                <p text std>Die <strong>Adresse wird automatisch ausgefüllt</strong>, da der Kunde schon mal hier war.</p>
              </tipp-box>
            </div>
          </div>
        </section>

        <?php

        /**
         * @var int
         */
        $tabindex = 8;

        /**
         * Include further and back buttons.
         */
        include dirname(__DIR__) . "/_progress_options.php"; ?>

      </div>
    </timeline-option>
  </div>

  <?php

  /**
   * Include all steps previews for upcoming ones.
   */
  include dirname(__DIR__) . "/_progress_next.php"; ?>

</timeline>