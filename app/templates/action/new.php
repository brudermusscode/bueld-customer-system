<?php

include dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Http\Request;
use Bruder\Model\Repair\RepairOrder;
use Bruder\Time\Time;

/**
 * @var Request $Request
 */

/**
 * Begin output buffer.
 */
ob_start();

?>

<prompt fl alic jucc>
  <div close-overlay></div>

  <div content-width=smoler center-content pv62>
    <box-model filled=lighter rounded elevated=smol fl fldircol gap=mid p42 animation=open>
      <div fl alic fldircol gap=smol+>
        <mi midplus color=company4>draw</mi>
        <p text midler semibold>Neue Aktion</p>
      </div>

      <div fl fldircol gap>
        <?php

        /**
         * @var ?RepairOrder
         */
        $RepairOrder = RepairOrder::where("status", "DONE")
          ->latest()
          ->first()

        ?>

        <div fl fldircol gap=smol>
          <a href="/repair/order/new">
            <div typer rounded clickable>
              <div fl alic gap=smol+>
                <mi icon>build</mi>
                <div fl fldircol gap=smoler>
                  <p text std bold>Reparatur-Auftrag</p>
                  <?php if ($RepairOrder) : ?>
                    <p text smol color=company3>Letzter <?= Time::ago($RepairOrder->created_at); ?> aufgegeben</p>
                  <?php endif; ?>
                </div>
              </div>
              <mi mid>arrow_forward</mi>
            </div>
          </a>
          <a disabled>
            <div typer rounded clickable>
              <div fl alic gap=smol+>
                <mi icon>bike_scooter</mi>
                <div fl fldircol gap=smoler>
                  <p text std bold>Fahrradpass</p>
                  <p text smol color=red>Noch nicht verfügbar</p>
                </div>
              </div>
              <mi mid>arrow_forward</mi>
            </div>
          </a>
        </div>
      </div>

      <div fl jucsb>
        <mbutton o-closer tabindex=104 size=mid material clean hoverable>
          <p text smolplus>Abbrechen</p>
        </mbutton>
        <div></div>
      </div>
    </box-model>
  </div>
</prompt>

<?php

exit($Request->success(data: ob_get_clean()));
