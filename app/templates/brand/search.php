<?php

include dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Http\Request;
use Bruder\Model\Brand;
use Bruder\Model\Customer;

/**
 * @var Request $Request
 */

/**
 * @var ?string
 */
$query = filter_input(INPUT_GET, "query", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Query is empty?
 */
if (!trim($query))
  exit($Request->success("<strong>Keine Fahrrad-Marken gefunden!</strong>"));

/**
 * @var string
 */
$search_string = "%{$query}%";

/**
 * @var Customer
 */
$Brands = Brand::where("name", "LIKE", $search_string)
  ->limit(8)
  ->get();

/**
 * Begin output buffer.
 */
ob_start();

?>


<?php if ($Brands->count()) : ?>
  <?php foreach ($Brands as $Brand) : ?>
    <div search-element data-id="<?= $Brand->id ?>" data-value="<?= $Brand->name ?>" rounded fl alic jucsb>
      <div fl gap=smol+ alic>
        <mi midler>brand_family</mi>
        <p text semibold><?= $Brand->name ?></p>
      </div>
      <mi midler>open_in_new</mi>
    </div>
  <?php endforeach; ?>
<?php else : ?>
  <div mt=smol search-element new data-value="<?= $query; ?>" data-id="0" rounded background=light-green>
    <mi midler>add</mi>
    <p text>
      <strong><?= $query ?></strong> als Marke hinzufügen
    </p>
  </div>
<?php endif; ?>

<?php

/**
 * * Success
 */
exit($Request->success(message: "", data: ob_get_clean()));
