<?php

/**
 * @var string $base_url
 * @var RepairOrder $RepairOrder
 */

use Bruder\Time\Time;

/**
 * @var string
 */
$page_title = "success";

?>

<div mt=wide main-distance content-width=smol+ center-content>

  <timeline>
    <timeline-line></timeline-line>

    <timeline-option done fl fldircol gap=smol+>
      <timeline-dot style=top:-.6em>
        <mi></mi>
      </timeline-dot>

      <div fl fldircol gap=smol>
        <p text midplus bold>Auftrag erstellt</p>
      </div>

      <div fl gap alic jucsb rounded=wide outlined p24>
        <div fl alic gap=smol+>
          <mi wide>receipt_long</mi>
          <div fl fldircol gap=smolest>
            <p text bold>Referenznummer &middot; <?= $RepairOrder->reference_id ?></p>
            <p text smol color=company>Erstellt <?= Time::ago($RepairOrder->updated_at) ?></p>
          </div>
        </div>
      </div>
    </timeline-option>

    <timeline-option active fl fldircol gap=smol+>
      <timeline-dot style=top:-.6em>
        <div class="circle-loader"></div>
        <mi></mi>
      </timeline-dot>

      <div>
        <p text midplus bold>Auftragsbestätigung drucken</p>
      </div>

      <form data-form="document:get">
        <input type=hidden name=id value="<?= $RepairOrder->id ?>" />
        <input type=hidden name=type value="confirmation" />
        <mbutton submit-closest data-action="repair:order:print-done" has-icon=left size=wide background=company color=light material>
          <mi>print</mi>
          <p text>Dokument öffnen</p>
        </mbutton>
      </form>

      <tipp-box rounded filled>
        <mi>brightness_alert</mi>
        <p text std>Wenn der Kunde eine E-Mail Adresse hinterlegt hat, wurde ihm eine Auftragsbestätigung per Mail zugesandt.</p>
      </tipp-box>
    </timeline-option>
  </timeline>

</div>