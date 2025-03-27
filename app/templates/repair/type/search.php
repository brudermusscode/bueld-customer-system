<?php

include dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Http\Request;
use Bruder\Model\Repair\RepairType;

/**
 * @var Request $Request
 */

/**
 * @var ?string
 */
$type = filter_input(INPUT_GET, "type", FILTER_DEFAULT);

/**
 * @var ?string
 */
$query = filter_input(INPUT_GET, "query", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var HTML
 */
$nothing = <<<HTML
  <div search-element clone new mt=smol data-id=0 data-value="$query" data-price="0,00 €" rounded background=light-green>
    <mi midler>add</mi>
    <p text><strong>$query</strong> hinzufügen</p>
  </div>
HTML;

/**
 * No name has been entered?
 * ! Error
 */
if (!trim($query))
  exit($Request->success("<strong>Nichts gefunden!</strong>", data: $nothing));

/**
 * @var string
 */
$search_string = "%{$query}%";

/**
 * @var RepairType
 */
$RepairCategories = RepairType::with("repair_category")
  ->where("type", $type)
  ->when(
    is_numeric($query),
    function ($q) use ($query) {
      return $q->where("initial_id", $query);
    },
    function ($q) use ($query, $search_string) {
      return $q->where("id", $query)
        ->orWhere("description", "LIKE", $search_string)
        ->orWhereHas("repair_category", function ($q) use ($search_string) {
          $q->where("description", "LIKE", $search_string);
        });
    }
  )
  ->limit(8)
  ->get()
  ->groupBy(fn($repairType) => $repairType->repair_category->description ?? "Unknown");

/**
 * Begin output buffer.
 */
ob_start();

?>

<?php

if (!$RepairCategories->count()) :
  echo $nothing;
else:

  foreach ($RepairCategories as $Category => $RepairTypes) : ?>

    <p bold color=company3 text smol ttup ph12 mb=smoler mt=smol><?= ($Category) ?></p>

    <?php foreach ($RepairTypes as $RepairType) :

      /**
       * @var string
       */
      $price = $RepairType->formatted_price();

    ?>
      <div
        search-element
        data-id="<?= $RepairType->id ?>"
        data-value="<?= htmlspecialchars($RepairType->description) ?>"
        data-price="<?= $price ?> €"
        rounded fl alic jucsb gap>
        <div fl alic gap=smol>
          <p text search-value><?= $RepairType->initial_id . " &nbsp;&middot;&nbsp; <strong>" . $RepairType->description . "</strong>" ?></p>
        </div>
        <div fl alic gap=smol>
          <p price text slight color=green bold><?= $price; ?> €</p>
        </div>
      </div>
    <?php endforeach; ?>

<?php endforeach;

endif;

/**
 * * Success
 */
exit($Request->success(message: "", data: ob_get_clean()));
