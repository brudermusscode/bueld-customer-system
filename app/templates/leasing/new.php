<?php

include dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Http\Request;
use Bruder\Model\Customer;
use Bruder\Model\Leasing\LeasingCompany;

/**
 * @var Request $Request
 */

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "customer_id", FILTER_VALIDATE_INT);

/**
 * @var ?Customer
 */
$Customer = Customer::findOrReturn($id, "<strong>Dieser Kunde konnte nicht gefunden werden!</strong>");

/**
 * Begin output buffer.
 */
ob_start();

?>

<prompt>
  <div close-overlay></div>

  <div content-width=smol center-content pv62>

    <form data-form="leasing:create">
      <box-model filled=lighter rounded elevated=smol fl fldircol gap=mid p42 animation=open>
        <div fl alic fldircol gap=smol+>
          <mi midplus slight color=company>token</mi>
          <p text midler semibold>Leasing hinzufügen</p>
        </div>

        <div fl fldircol gap=smoler>
          <div fl alic gap=smol+ rounded=mid outlined p24>
            <mi wide>face</mi>
            <div>
              <p text bold><?= $Customer->full_name() ?></p>
              <p text smolplus color=company><?= $Customer->full_address() ?></p>
            </div>
          </div>
        </div>

        <div fl fldircol gap>
          <div fl alic jucsb gap>
            <div fl fldircol style=width:10em;>
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
                placeholder="123456789" />
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

                <radio-option style=flex-basis:48%;max-width:49.6%; data-value=<?= $Company->id ?> flone material outlined>
                  <?= $Company->name; ?>
                </radio-option>

              <?php endforeach; ?>

              <input radio-input type=hidden name=leasing_company_id />
            </mradio>
          </div>
        </div>

        <input type=hidden name=customer_id value="<?= $id ?>" />

        <div fl jucsb>
          <mbutton o-closer tabindex=104 size=mid material clean hoverable>
            <p text smolplus>Abbrechen</p>
          </mbutton>
          <mbutton tabindex=103 submit-closest size=mid material background=slighter color=company hoverable>
            <p text semibold>Hinzufügen</p>
          </mbutton>
        </div>
      </box-model>
    </form>
  </div>
</prompt>

<?php

exit($Request->success(data: ob_get_clean()));
