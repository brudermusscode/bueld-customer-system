<?php

use Bruder\Model\Repair\RepairOrder;

/**
 * @var RepairOrder $RepairOrder
 */

/**
 * @var bool
 */
$is_overview ??= false;

/**
 * * Repair type
 */

if (
  $is_overview
  || in_array($page_title, ["employee", "customer", "object", "leasing", "items", "overview", "success"])
) : ?>
  <timeline-option done>
    <timeline-dot>
      <div class="circle-loader"></div>
      <mi></mi>
    </timeline-dot>

    <div outlined rounded=mid p24 fl alic gap=smol+ slight>
      <mi wide><?= $RepairOrder->display_type_icon() ?></mi>
      <div>
        <p text bold><?= $RepairOrder->display_type() ?></p>
        <p text smolplus>Reparatur-Typ &middot; <span color=company4>Kann nicht geändert werden</span></p>
      </div>
    </div>
  </timeline-option>
<?php endif; ?>

<?php if (
  $is_overview
  || in_array($page_title, ["customer", "object", "leasing", "items", "overview", "success"])
) : ?>
  <timeline-option <?= $RepairOrder->employee ? "done" : "" ?>>
    <timeline-dot>
      <div class="circle-loader"></div>
      <mi></mi>
    </timeline-dot>

    <a href="<?= "$base_url/employee#activeObject" ?>" no-scroll-top>
      <div outlined rounded=mid p24 fl alic gap=smol+ clickable>
        <mi wide>assignment_ind</mi>
        <div>
          <p text bold><?= $RepairOrder->employee?->full_name() ?? "Mitarbeiter" ?></p>
          <p text><?= $RepairOrder->employee ? "Ausführender Mitarbeiter" : "Wähle dich als ausführenden Mitarbeiter aus" ?></p>
        </div>
      </div>
    </a>
  </timeline-option>
<?php endif; ?>

<?php

/**
 * * Customer
 */

if ($is_overview || in_array($page_title, ["object", "leasing", "items", "overview", "success"])) : ?>
  <timeline-option <?= $RepairOrder->customer ? "done" : "" ?>>
    <timeline-dot>
      <div class="circle-loader"></div>
      <mi></mi>
    </timeline-dot>

    <a href="<?= "$base_url/customer#activeObject" ?>" no-scroll-top>
      <div outlined rounded=mid p24 fl alic gap=smol+ clickable>
        <mi wide>face</mi>
        <div>
          <p text bold><?= $RepairOrder->customer?->full_name() ?? "Kunde" ?></p>
          <p text>
            <?php
            echo
            $RepairOrder->customer
              ? $RepairOrder->customer->address_line_1 . ", " . $RepairOrder->customer->postcode . " " . $RepairOrder->customer->city
              : "Wähle den Kunden aus, oder erstelle einen neuen"
            ?>
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

if ($is_overview || in_array($page_title, ["leasing", "items", "overview", "success"])) : ?>
  <timeline-option <?= $Object || $Brand ? "done" : "" ?>>
    <timeline-dot>
      <div class="circle-loader"></div>
      <mi></mi>
    </timeline-dot>

    <a href="<?= "$base_url/object#activeObject" ?>" no-scroll-top>
      <div outlined rounded=mid p24 fl alic gap=smol+ clickable>
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

if (
  $RepairOrder->type === "bike" && $is_overview
  || $RepairOrder->type === "bike" && in_array($page_title, ["items", "overview", "success"])
) : ?>
  <timeline-option
    <?= $Leasing
      && $RepairOrder->leasing_inspection_id
      || $Leasing ? "done" : "" ?>
    <?= $Leasing
      && $Leasing->company->requires_inspection_id
      && !$RepairOrder->leasing_inspection_id ? "error" : "" ?>>
    <timeline-dot>
      <div class="circle-loader"></div>
      <mi></mi>
    </timeline-dot>

    <a href="<?= "$base_url/leasing#activeObject" ?>" no-scroll-top>
      <div outlined rounded=mid p24 fl alic gap=smol+ clickable>
        <mi wide>token</mi>
        <div>
          <p text bold><?= !$Leasing ? "Leasing" : $Leasing->company->name ?></p>
          <p text>
            <?= !$Leasing
              ? "Nichts hinzugefügt"
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

if ($is_overview || in_array($page_title, ["overview", "success"])) :

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

  <timeline-option <?= $items_count || $parts_count ? "done" : "" ?>>
    <timeline-dot>
      <div class="circle-loader"></div>
      <mi></mi>
    </timeline-dot>

    <a href="<?= "$base_url/items#activeObject" ?>" no-scroll-top>
      <div outlined rounded=mid p24 fl alic gap=smol+ clickable>
        <mi wide>build</mi>
        <div>
          <p text bold><?= $items_count_text ?>, <?= $parts_count_text ?> ausgewählt</p>
          <p text>Klicke hier, um die Liste zu bearbeiten</p>
        </div>
      </div>
    </a>
  </timeline-option>
<?php endif; ?>

<?php

/**
 * * Pricing
 *
 * This is only shown in repair/order/show.php
 */

if ($is_overview) :
  include __DIR__ . "/show/_pricing.php";
endif; ?>