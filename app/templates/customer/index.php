<?php

use Bruder\Model\Customer;

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
  "firstname" => "Vorname",
  "lastname" => "Nachname",
  "city" => "Stadt",
  "lastactive" => "Letzte Aktivität",
];

/**
 * @var Customer
 */
$Sorted = match ($sort) {
  "lastactive" => Customer::orderBy("updated_at", "DESC"),
  "city" => Customer::orderBy("city", "ASC"),
  "lastname" => Customer::orderBy("lastname", "ASC"),
  "firstname" => Customer::orderBy("firstname", "ASC"),
  "oldest" => Customer::orderBy("created_at", "ASC"),
  default => Customer::orderBy("created_at", "DESC"),
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
  "hasnomail" => [
    "icon" => "unsubscribe",
    "name" => "Ohne Mail",
  ],
  "hasmail" => [
    "icon" => "mark_email_read",
    "name" => "Mit Mail",
  ],
];

/**
 * @var Customer
 */
$Filtered = match ($filter) {
  "bookmarked"   => $Sorted->whereHas("bookmark"),
  "hasmail"   => $Sorted->whereNotNull("mail"),
  "hasnomail"   => $Sorted->whereNull("mail"),
  default   => $Sorted,
};

/**
 * Filter by query, if one is set.
 */
if ($query) :
  $search_string = "%{$query}%";

  $Filtered = $Filtered
    ->where(function ($q) use ($search_string) {
      $q->where("firstname", "LIKE", $search_string)
        ->orWhere("lastname", "LIKE", $search_string)
        ->orWhere("id", "LIKE", $search_string)
        ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", [$search_string]);
    });
endif;

/**
 * @var int
 */
$FilteredCount = $Filtered->count();

/**
 * @var ?Customer
 */
$Customers = $Filtered
  ->limit($limit)
  ->offset($offset)
  ->get();

/**
 * @var string
 */
$base_url = "/master/customers";

?>

<div fl fldircol content-gap>

  <page-title scroll-manipulated>
    <div content-width=wider center-content>
      <div fl fldircol gap=smol>
        <h1>Kunden</h1>
        <h2>Alle Kunden an einem Platz</h2>
      </div>
    </div>
  </page-title>

  <page-with-title main-distance>
    <div content-width=wider center-content fl fldircol gap>

      <div fl gap=smol+>
        <box-model outlined background=transparent rounded=wide style=min-width:24.8%; fl alic>
          <div p38>
            <p text smoler semibold slight>Insgesamt</p>
            <p text widest bold><?= Customer::count() ?></p>
            <div fl alic gap=smol>
              <mi color=green midler>trending_up</mi>
              <p text smol semibold>1,5 %</p>
            </div>
          </div>
        </box-model>

        <box-model background=nav rounded=wide style=min-width:24.8%; fl alic>
          <div p38>
            <p text smoler semibold slight>Ohne Mail-Adresse</p>
            <p text widest bold><?= Customer::count_without_mail() ?></p>
            <div fl alic gap=smol>
              <mi color=green midler>trending_up</mi>
              <p text smol semibold>2,0 %</p>
            </div>
          </div>
        </box-model>

        <?php

        /**
         * @var array
         */
        $CustomerTopCity = Customer::count_top_city();

        ?>

        <box-model background=nav rounded=wide style=min-width:24.8%; fl alic>
          <div p38>
            <p text smoler semibold slight>Top Standort</p>
            <p text wide bold><?= $CustomerTopCity["city"] ?></p>
            <div fl alic gap=smol>
              <mi color=red midler>trending_down</mi>
              <p text smol semibold><?= $CustomerTopCity["count"] ?></p>
            </div>
          </div>
        </box-model>
      </div>

      <?php

      /**
       * @var string
       */
      $filter_search_terms = "Suche nach Kunden...";

      /**
       * Include basic filtering.
       */
      include COMPONENT . "/_filter.php"; ?>

      <div>

        <?php if (!$Customers->count()) : ?>
          <div rounded=wide filled=lighter pv62 fl alic jucc gap=smol+ slight>
            <mi mid>sentiment_very_dissatisfied</mi>
            <p text midler semibold>Keine Kunden</p>
          </div>
        <?php else : ?>
          <div p12 pv24 style="padding-right:32px;" fl alic jucsb gap=smol+ text smolplus semibold>
            <div fl alic gap=smol+>
              <p style="width:56px;">&nbsp;</p>
              <p style="width:2.4em;">#</p>
              <p style="width:280px;">Name</p>
              <p style="width:20px;">Aufträge</p>
            </div>
            <div fl alic gap=smol+>
              <p style="width:120px;">Standort</p>
              <p style="width:118px;text-align:right;" text smol>Letzte Aktivität</p>
            </div>
          </div>
        <?php endif; ?>

        <?php foreach ($Customers as $key => $Customer) :
          include __DIR__ . "/_customer.php";
        endforeach; ?>
      </div>

      <div mb=wide fl jucc>
        <?php

        /**
         * Include pagination option.
         */
        include PAGINATION;

        ?>
      </div>

    </div>
  </page-with-title>
</div>