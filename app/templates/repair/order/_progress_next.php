<?php

use Bruder\Model\Repair\RepairOrder;

/**
 * @var RepairOrder $RepairOrder
 */

/**
 * * Repair type
 */

if (in_array($page_title, ["type"])) : ?>
  <timeline-option>
    <timeline-dot>
      <div class="circle-loader"></div>
      <mi></mi>
    </timeline-dot>

    <a href="<?= "$base_url/employee#activeObject" ?>" no-scroll-top>
      <div slight outlined rounded=mid p24 fl alic gap=smol+ clickable>
        <mi wide>assignment_ind</mi>
        <div>
          <p text bold>Mitarbeiter</p>
          <p text>Wähle dich als ausführenden Mitarbeiter aus</p>
        </div>
      </div>
    </a>
  </timeline-option>
<?php endif; ?>

<?php

/**
 * * Customer
 */

if (in_array($page_title, ["type", "employee"])) : ?>
  <timeline-option <?= $RepairOrder->customer ? "done" : "" ?>>
    <timeline-dot>
      <div class="circle-loader"></div>
      <mi></mi>
    </timeline-dot>

    <a href="<?= "$base_url/customer#activeObject" ?>" no-scroll-top>
      <div slight outlined rounded=mid p24 fl alic gap=smol+ clickable>
        <mi wide>face</mi>
        <div>
          <p text bold><?= $RepairOrder->customer?->full_name() ?? "Kunde" ?></p>
          <p text>
            <?= $RepairOrder->customer?->full_address() ?? "Wähle den Kunden aus, oder erstelle einen neuen" ?>
          </p>
        </div>
      </div>
    </a>
  </timeline-option>
<?php endif; ?>

<?php

/**
 * * Object
 */

if (in_array($page_title, ["type", "employee", "customer"])) : ?>
  <timeline-option <?= $Object || $Brand ? "done" : "" ?>>
    <timeline-dot>
      <div class="circle-loader"></div>
      <mi></mi>
    </timeline-dot>

    <a href="<?= "$base_url/object#activeObject" ?>" no-scroll-top>
      <div slight outlined rounded=mid p24 fl alic gap=smol+ clickable>
        <mi wide><?= $RepairOrder->display_type_icon() ?></mi>
        <div>
          <p text bold>
            <?=
            $Object ? $Object->brand->name : (
              $Brand
              ? $Brand->name
              : "Marke & Modell"
            );
            ?>
          </p>
          <p text smolplus>
            <?=
            $Object
              ? (
                isset($Object->object_unique_identifier)
                ? "Gerätenummer &middot; <span color=company2>" . $Object?->object_unique_identifier . "</span>"
                : "Zu reparierendes Objekt"
              )
              : (
                $Brand ? "Marke & Modell" : "Füge ein Objekt hinzu, das repariert werden soll"
              );
            ?>
          </p>
        </div>
      </div>
    </a>
  </timeline-option>
<?php endif; ?>

<?php

/**
 * * Leasing
 */

if ($RepairOrder->type === "bike" && in_array($page_title, ["type", "employee", "customer", "object"])) : ?>
  <timeline-option
    <?= $Leasing && $RepairOrder->leasing_inspection_id ? "done" : "" ?>>
    <timeline-dot>
      <div class="circle-loader"></div>
      <mi></mi>
    </timeline-dot>

    <a href="<?= "$base_url/leasing#activeObject" ?>" no-scroll-top>
      <div slight outlined rounded=mid p24 fl alic gap=smol+ clickable>
        <mi wide>token</mi>
        <div>
          <p text bold><?= !$RepairOrder->is_leasing ? "Leasing" : $Leasing->company->name ?></p>
          <p text>
            <?= !$RepairOrder->is_leasing
              ? "Füge dem Fahrrad einen Leasing-Vertrag hinzu"
              : (
                $RepairOrder->leasing_inspection_id
                ? "Inspektionsnummer &middot; <span color=company2>$RepairOrder->leasing_inspection_id</span>"
                : (
                  $Leasing->company->requires_inspection_id
                  ? "<span color=red>Füge eine Inspektionsnnummer hinzu</span>"
                  : "Vertragsnummer &middot; <span color=company2>$Leasing->contract_id</span>"
                )
              );
            ?>
          </p>
        </div>
      </div>
    </a>
  </timeline-option>
<?php endif; ?>

<?php

/**
 * * Items
 */

if (in_array($page_title, ["type", "employee", "customer", "object", "leasing"])) : ?>

  <?php

  /**
   * @var int
   */
  $items_count = $RepairOrder->items->count();

  /**
   * @var string
   */
  $items_count_text = $items_count && $items_count === 1
    ? "$items_count Reparatur"
    : (
      $items_count && $items_count > 1
      ? "$items_count Reparaturen"
      : "Keine Reparaturen"
    );

  /**
   * @var int
   */
  $parts_count = $RepairOrder->parts->count();


  /**
   * @var string
   */
  $parts_count_text = $parts_count && $parts_count === 1
    ? "$parts_count Ersatzteil"
    : (
      $parts_count && $parts_count > 1
      ? "$parts_count Ersatzteile"
      : "keine Ersatzteile"
    );

  ?>

  <timeline-option
    <?= $items_count || $parts_count ? "done" : "" ?>>
    <timeline-dot>
      <div class="circle-loader"></div>
      <mi></mi>
    </timeline-dot>

    <a href="<?= "$base_url/items#activeObject" ?>" no-scroll-top>
      <div slight outlined rounded=mid p24 fl alic gap=smol+ clickable>
        <mi wide>build</mi>
        <div>
          <p text bold>
            <?=
            $items_count || $parts_count
              ? $items_count_text . ", " . $parts_count_text . " ausgewählt"
              : "Reparaturen & Ersatzteile";
            ?></p>
          <p text>
            <span text smolplus>Erstelle eine Liste mit benötigten Ersatzteilen & Reparaturen</span>
          </p>
        </div>
      </div>
    </a>
  </timeline-option>
<?php endif; ?>

<?php

/**
 * * Overview
 */

if (in_array($page_title, ["type", "employee", "customer", "object", "leasing", "items"])) : ?>
  <timeline-option
    <?=
    $RepairOrder?->employee
      && $RepairOrder?->customer
      && $RepairOrder?->brand
      || $RepairOrder->is_done()
      || $RepairOrder->in_repair()
      ? "done" : ""
    ?>>
    <timeline-dot>
      <div class="circle-loader"></div>
      <mi></mi>
    </timeline-dot>

    <a href="<?= "$base_url/overview#activeObject" ?>" no-scroll-top>
      <div slight outlined rounded=mid p24 fl alic gap=smol+ clickable>
        <mi wide>credit_card</mi>
        <div>
          <p text bold>Kosten & Übersicht</p>
          <p text>Überprüfe alle eingaben und gebe den Auftrag auf</p>
        </div>
      </div>
    </a>
  </timeline-option>
<?php endif; ?>