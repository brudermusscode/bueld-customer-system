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
 * @var string
 */
$type = filter_input(INPUT_GET, "type", FILTER_DEFAULT);

/**
 * @var ?int
 */
$order_id = filter_input(INPUT_GET, "order_id", FILTER_VALIDATE_INT);

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

    <form request="customer:object:create" responder reload>
      <box-model filled=lighter rounded elevated=smol fl fldircol gap=mid p42 animation=open>
        <div fl alic fldircol gap=smol+>
          <mi midplus color=company4><?= $type === "bike" ? "pedal_bike" : "communities" ?></mi>
          <p text midler semibold><?= $type === "bike" ? "Fahrrad" : "Nähmaschine" ?> hinzufügen</p>
        </div>

        <div fl fldircol gap=smoler>
          <div fl alic gap=smol+ rounded=mid outlined p24>
            <mi wide>face</mi>
            <div>
              <p text bold><?= $Customer->full_name() ?></p>
              <p text smolplus color=company3><?= $Customer->full_address() ?></p>
            </div>
          </div>
        </div>

        <div fl fldircol gap>
          <?php

          /**
           * Include basic customer object creation form.
           */
          include TEMPLATE . "/customer/object/_form.php"; ?>


          <?php if ($order_id) : ?>
            <tipp-box rounded=mid background=light-green color=dark-green>
              <mi>info</mi>
              <p>Das neue Gerät wird automatisch für diesen Auftrag gespeichert</p>
            </tipp-box>
          <?php endif; ?>
        </div>

        <input type=hidden name=customer_id value="<?= $id ?>" />
        <input type=hidden name=type value="<?= $type ?>" />
        <input type=hidden name=order_id value="<?= $order_id ?>" />

        <div fl jucsb>
          <mbutton o-closer tabindex=104 size=mid material clean hoverable>
            <p text smolplus>Abbrechen</p>
          </mbutton>
          <mbutton tabindex=103 submit-closest size=mid material background=company color=company3>
            <p text semibold>Hinzufügen</p>
          </mbutton>
        </div>
      </box-model>
    </form>
  </div>
</prompt>

<?php

exit($Request->success(data: ob_get_clean()));
