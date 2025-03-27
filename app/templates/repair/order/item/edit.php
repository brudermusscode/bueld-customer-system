<?php

include dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Model\Repair\RepairType;
use Bruder\Model\Repair\RepairOrder;
use Bruder\Model\Repair\RepairOrderItem;

/**
 * @var Request $Request
 */

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

/**
 * @var ?RepairOrderItem
 */
$Item = RepairOrderItem::findOrReturn($id, "<strong>Dieses Item existiert nicht mehr!</strong>");

/**
 * Begin output buffer.
 */
ob_start();

?>

<prompt fl alic jucc>
  <div close-overlay></div>

  <div content-width=smoler center-content>
    <form request="repair:type:update" responder reload>
      <box-model filled=lighter rounded elevated=mid fl fldircol gap=mid p42 animation=open>
        <div fl alic fldircol gap=smol+>
          <mi midplus slight>build</mi>
          <p text midler semibold>Reparatur bearbeiten</p>
        </div>

        <div fl fldircol gap>
          <div fl fldircol gap=smol>
            <p text bold>Name</p>
            <div input material flexone>
              <input
                name=name
                tabindex=100
                type="text"
                autofocus
                required
                enter-submitable
                value="<?= htmlspecialchars($Item->type?->name ?? $Item->custom_field ?? "") ?>"
                placeholder="<?= htmlspecialchars($Item->type?->name ?? $Item->custom_field ?? "") ?>" />
            </div>
          </div>

          <div fl alic jucsb gap>
            <div fl fldircol>
              <p text bold>Preis</p>
              <p text smolplus slight>Muss ausgefüllt sein</p>
            </div>
            <div input material has-icon=right style=width:11.4em;>
              <input
                name=price
                tabindex=102
                type="text"
                enter-submitable
                placeholder="0,00"
                value="<?=
                        $Type->price
                          ? number_format($Item->type?->price ?? $Item->price ?? "", 2, ",", ".")
                          : ""
                        ?>" />
              <mi color=green bold>euro</mi>
            </div>
          </div>
        </div>

        <input type=hidden name=id value="<?= $id ?>" />

        <div fl jucsb>
          <mbutton o-closer tabindex=104 size=mid material clean hoverable>
            <p text smolplus>Abbrechen</p>
          </mbutton>
          <mbutton tabindex=103 submit-closest size=mid material background=slighter color=company hoverable>
            <p text semibold>Speichern</p>
          </mbutton>
        </div>
      </box-model>
    </form>
  </div>
</prompt>

<?php

exit($Request->success(data: ob_get_clean()));
