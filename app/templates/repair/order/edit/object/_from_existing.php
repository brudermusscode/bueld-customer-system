<?php

use Bruder\Model\Repair\RepairOrder;

/**
 * @var RepairOrder $RepairOrder
 */

?>

<?php

/**
 * * Bike
 */

if ($RepairOrder->type === "bike") : ?>

  <div fl fldircol gap=smol+>
    <div>
      <p text midler bold>Registrierte Fahrräder des Kunden</p>
      <!-- <p text slight>Wähle ein bereits hinterlegtes Fahrrad oder erstelle ein neues</p> -->
    </div>

    <mradio material size=wide fl fldircol gap=smoler unselect=false>
      <?php

      foreach ($Customer->bikes ?? [] as $Bike) : ?>
        <radio-option
          <?= $Object?->is($Bike) ? "active" : "" ?>
          data-value=<?= $Bike->id ?>
          style=max-height:100px;height:100px;padding-inline:24px;
          fl alic jucstart gap=smol+ flone material outlined>
          <mi text midplus><?= $RepairOrder->display_type_icon() ?></mi>
          <div>
            <div fl alic gap=smol>
              <p text bold><?= $Bike->brand->name; ?></p>
              <?php if ($Bike->leasing) : ?>
                <p background=yellow color=dark style=height:24px;padding-inline:12px;margin-bottom:2px; rounded text smoler ttup bold fl alic>Leasing</p>
              <?php endif; ?>
            </div>
            <div fl alic gap=smolest>
              <p text smolplus style="font-weight:300 !important">
                <?=
                $Bike->object_unique_identifier ? "Rahmennummer &middot; <span color=company3>$Bike->object_unique_identifier</span>" : ""
                ?>
              </p>
            </div>
          </div>
        </radio-option>
      <?php endforeach; ?>

      <input radio-input type=hidden name=customer_object_id value="<?= $Object?->id ?>" />
    </mradio>

    <div posrel background="slight" rounded="wide" style="height:1px;" mt mb>
      <div style="z-index:1;position:absolute;top:50%;left:50%;translate:-50% -50%;" background=bg ph24 rounded=wide>
        <p text ttup smol bold slight>oder</p>
      </div>
    </div>

    <div fl jucc>
      <mbutton request-get="customer:object:new"
        data-customer-id="<?= $Customer->id ?>"
        data-type="<?= $Bike->type ?>"
        data-order-id="<?= $RepairOrder->id ?>"
        material outlined has-icon=left size=mid>
        <mi>add</mi>
        Fahrrad hinzufügen
      </mbutton>
    </div>
  </div>

  <div fl fldircol gap=smol+ dno>
    <div fl alic jucsb gap>
      <div fl fldircol style=width:18.4em;>
        <p text bold>Inspektions-Nummer</p>
        <p text smolplus slight>Vom Leasing-Unternehmen bereitgestellt</p>
      </div>
      <div input material has-icon=right flone>
        <mi>frame_inspect</mi>
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
  </div>

<?php

  /**
   * * Sewing machine
   */

else : ?>

  <div fl fldircol gap=smol+>
    <div>
      <p text midler bold>Registrierte Maschinen des Kunden</p>
      <!-- <p text slight>Wähle eine bereits hinterlegte Maschine oder erstelle eine neue</p> -->
    </div>

    <mradio material size=wide fl fldircol gap=smoler unselect=false>
      <?php foreach ($Customer->sewing_machines ?? [] as $Machine) : ?>
        <radio-option
          <?= $RepairOrder->customer_object?->is($Machine) ? "active" : "" ?>
          data-value=<?= $Machine->id ?>
          style=max-height:100px;height:100px;padding-inline:24px;
          fl alic jucstart gap=smol+ flone material outlined>
          <mi text midplus>multicooker</mi>
          <div>
            <p text bold><?= $Machine->brand->name; ?></p>
            <?php if ($Machine->object_unique_identifier) : ?>
              <div fl alic gap=smolest>
                <p text smolplus style="font-weight:300 !important">
                  Maschinennummer &middot; <span color=company2><?= $Machine->object_unique_identifier; ?></span>
                </p>
              </div>
            <?php endif; ?>
          </div>
        </radio-option>
      <?php endforeach; ?>

      <input radio-input type=hidden name=customer_object_id value="<?= $Object?->id ?>" />
    </mradio>

    <div posrel background="slight" rounded="wide" style="height:1px;" mt mb>
      <div style="z-index:1;position:absolute;top:50%;left:50%;translate:-50% -50%;" background=bg ph24 rounded=wide>
        <p text ttup smol bold slight>oder</p>
      </div>
    </div>

    <div fl jucc>
      <mbutton
        request-get="customer:object:new"
        data-customer-id="<?= $Customer->id ?>"
        data-type="sewing"
        data-order-id="<?= $RepairOrder->id ?>"
        material outlined has-icon=left size=mid>
        <mi>add</mi>
        Maschine hinzufügen
      </mbutton>
    </div>
  </div>

<?php endif; ?>