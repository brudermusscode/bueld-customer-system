<?php

use Bruder\Model\Repair\RepairOrder;
use Bruder\Model\Repair\RepairType;

/**
 * @var int
 */
$limit = 100;

/**
 * @var string
 */
$default_sort = "number";

/**
 * Include basic pagination params.
 */
include PAGINATION_PARAMS;

/**
 * @var array
 */
$sort_map = [
  "number" => "Nummer",
  "newest" => "Neueste",
  "oldest" => "Älteste",
  "name" => "Name",
  "update" => "Letztes Update",
];

/**
 * @var RepairOrder
 */
$Sorted = match ($sort) {
  "update" => RepairType::orderBy("updated_at", "DESC"),
  "name" => RepairType::orderBy("description", "DESC"),
  "oldest" => RepairType::orderBy("created_at", "ASC"),
  "newest" => RepairType::orderBy("created_at", "DESC"),
  default => RepairType::orderBy("initial_id", "DESC"),
};

/**
 * @var array
 */
$filter_map = [
  "all" => [
    "icon" => "all_inclusive",
    "name" => "Alle",
  ],
  "bike" => [
    "icon" => "directions_bike",
    "name" => "Fahrrad",
  ],
  "sewing" => [
    "icon" => "communities",
    "name" => "Nähmaschine",
  ],
];

/**
 * @var RepairOrder
 */
$Filtered = match ($filter) {
  "bike"   => $Sorted->where("type", "bike"),
  "sewing"   => $Sorted->where("type", "sewing"),
  default   => $Sorted,
};

/**
 * Filter by query, if one is set.
 */
if ($query) :
  $search_string = "%{$query}%";

  $Filtered = $Filtered
    ->where("description", "LIKE", $search_string)
    ->orWhere("initial_id", "LIKE", $search_string)
    ->orWhere("type", "LIKE", $search_string);
endif;

/**
 * @var int
 */
$FilteredCount = $Filtered->count();

/**
 * @var ?RepairOrder
 */
$RepairTypes = $Filtered
  ->limit($limit)
  ->offset($offset)
  ->get();

/**
 * @var string
 */
$base_url = "/master/repair/types";

?>

<div fl fldircol content-gap>
  <page-title scroll-manipulated>
    <div content-width="wider" center-content>
      <div fl fldircol gap="smol">
        <h1>Reparaturen</h1>
        <h2>Liste aller Reparaturen</h2>
      </div>
    </div>
  </page-title>

  <page-with-title main-distance>
    <div content-width=wider center-content fl fldircol gap>

      <?php

      /**
       * @var string
       */
      $filter_search_terms = "Suche nach Kunde, Mitarbeiter, Referenz-Nummer, Typ oder Status...";

      /**
       * Include basic filtering.
       */
      include COMPONENT . "/_filter.php"; ?>


      <div fl fldircol>
        <?php if (!$RepairTypes->count()) : ?>
          <div rounded=wide filled=lighter pv62 fl alic jucc gap=smol+ slight>
            <mi mid>child_care</mi>
            <p text midler semibold>Keine Stammdaten für Reparaturen</p>
          </div>
        <?php else : ?>
          <div p12 pv24 style="padding-right:32px;" fl alic jucsb gap=smol+ text smolplus semibold>
            <div fl alic gap=smol+>
              <p style="width:0px;">&nbsp;</p>
              <p style="width:188px;">Typ</p>
              <p style="width:60px;">Nr.</p>
              <p style="width:600px;">Name</p>
            </div>

            <div fl alic jucend gap=smol+>
              <p style="width:120px;">Preis</p>

              <div tar>
                <p style="width:100px;">Aktionen</p>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <?php foreach ($RepairTypes as $key => $Type) :
          include __DIR__ . "/_type.php";
        endforeach; ?>
      </div>

      <div mb=wide fl jucc>
        <?php

        /**
         * Include pagination option.
         */
        include PAGINATION; ?>
      </div>

    </div>
  </page-with-title>
</div>