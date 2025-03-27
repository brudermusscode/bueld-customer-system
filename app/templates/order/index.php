<?php

use Bruder\Model\Repair\RepairOrder;

/**
 * Include basic pagination params.
 */
include PAGINATION_PARAMS;

/**
 * @var array
 */
$sort_map = [
  "newest" => "Neueste",
  "oldest" => "Älteste",
  "employee" => "Mitarbeiter",
  "customer" => "Kunde",
  "status" => "Status",
  "update" => "Letztes Update",
];

/**
 * @var RepairOrder
 */
$Sorted = match ($sort) {
  "update" => RepairOrder::orderBy("updated_at", "DESC"),
  "status" => RepairOrder::orderBy("status", "ASC"),
  "customer" => RepairOrder::with("customer")
    ->selectRaw("*, repair_orders.updated_at AS updated_at")
    ->join("customers", "customers.id", "=", "repair_orders.customer_id")
    ->orderBy("customers.lastname", "ASC"),
  "employee" => RepairOrder::with("employee")
    ->selectRaw("*, repair_orders.updated_at AS updated_at")
    ->join("employees", "employees.id", "=", "repair_orders.employee_id")
    ->orderByRaw("CONCAT(employees.firstname, ' ', employees.lastname) ASC"),
  "oldest" => RepairOrder::orderBy("created_at", "ASC"),
  default => RepairOrder::orderBy("created_at", "DESC"),
};

/**
 * @var array
 */
$filter_map = [
  "all" => [
    "icon" => "all_inclusive",
    "name" => "Alle",
  ],
  "bookmarked" => [
    "icon" => "bookmark",
    "name" => "Gemerkt",
  ],
  "done" => [
    "icon" => "done_all",
    "name" => "Fertig",
  ],
  "open" => [
    "icon" => "trip_origin",
    "name" => "Offen",
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
  "bookmarked"   => $Sorted->whereHas("bookmark"),
  "done"   => $Sorted->where("status", "DONE"),
  "open"   => $Sorted->whereNot("status", "DONE"),
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
    ->where(function ($q) use ($search_string) {
      $q->whereHas("customer", function ($q2) use ($search_string) {
        $q2->where("firstname", "LIKE", $search_string)
          ->orWhere("lastname", "LIKE", $search_string)
          ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", [$search_string]);
      })
        ->orWhereHas("employee", function ($q3) use ($search_string) {
          $q3->where("firstname", "LIKE", $search_string)
            ->orWhere("lastname", "LIKE", $search_string)
            ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", [$search_string]);
        });
    })
    ->orWhere("status", "LIKE", $search_string)
    ->orWhere("reference_id", str_replace("%", "", $search_string));
endif;

/**
 * @var int
 */
$FilteredCount = $Filtered->count();

/**
 * @var ?RepairOrder
 */
$Orders = $Filtered
  ->where("status", "!=", "CUSTOMER")
  ->limit($limit)
  ->offset($offset)
  ->get();

/**
 * @var string
 */
$base_url = "/orders";

?>

<div fl fldircol content-gap>
  <page-title scroll-manipulated>
    <div content-width="wider" center-content>
      <div fl fldircol gap="smol">
        <h1>Aufträge</h1>
        <h2>Aufgegebene Aufträge verwalten</h2>
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
        <?php if (!$Orders->count()) : ?>
          <div rounded=wide filled=lighter pv62 fl alic jucc gap=smol+ slight>
            <mi mid>child_care</mi>
            <p text midler semibold>Keine Aufträge</p>
          </div>
        <?php else : ?>
          <div p12 pv24 style="padding-right:32px;" fl alic jucsb gap=smol+ text smolplus semibold>
            <div fl alic gap=smol+>
              <p style="width:56px;">&nbsp;</p>
              <p style="width:188px;">Typ
              </p>
              <!-- <p style="width:88px;">#</p> -->
              <p style="width:188px;">Kunde</p>
              <p style="width:188px;">Mitarbeiter</p>
            </div>
            <div fl alic gap=smol>
              <p style="width:180px;">Status</p>
              <p style="width:118px;text-align:right;" text smol>Letztes Update</p>
            </div>
          </div>
        <?php endif; ?>

        <?php foreach ($Orders as $key => $Order) :
          include __DIR__ . "/_order.php";
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