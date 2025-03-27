<?php

include dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Http\Request;
use Bruder\Model\Customer;

/**
 * @var Request $Request
 */

/**
 * @var ?string
 */
$full_name = filter_input(INPUT_GET, "full_name", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var ?string
 */
$type = filter_input(INPUT_GET, "type", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * No name has been entered?
 * ! Error
 */
if (!trim($full_name))
  exit($Request->success("<strong>Keinen Kunden gefunden!</strong>"));

/**
 * @var string
 */
$search_string = "%{$full_name}%";

/**
 * @var Customer
 */
$Customers =

  /**
   * If only a number is set, search for a customer by their
   * customer id.
   */
  Customer::when(
    is_numeric($full_name),
    function ($q) use ($full_name) {
      $q->where("id", $full_name);
    },
    function ($q) use ($search_string) {
      $q->where("lastname", "LIKE", $search_string)
        ->orWhere("firstname", "LIKE", $search_string)
        ->orWhere("company_name", "LIKE", $search_string)
        ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", [$search_string]);
    }
  )

  /**
   * if a type is set, search for customers that have objects of
   * that specific type.
   */
  ->when(
    !empty($type) && in_array($type, ["sewing", "bike"]),
    function ($q) use ($type) {
      $q->whereHas("objects", function ($q2) use ($type) {
        $q2->where("type", $type);
      });
    }
  )

  ->limit(6)
  ->get();

/**
 * Begin output buffer.
 */
ob_start();

?>


<?php if ($Customers->count()) : ?>
  <?php

  foreach ($Customers as $Customer) :

    /**
     * @var string
     */
    $visible_name = $Customer->company_name ?? $Customer->full_name();

    /**
     * @var int
     */
    $sewing_count = $Customer->sewing_machines->count();

    /**
     * @var int
     */
    $bike_count = $Customer->bikes->count();

  ?>

    <div
      data-action=customer:find data-id="<?= $Customer->id ?>"
      data-value="<?= $visible_name ?>"
      outlined hoverable p24 rounded>
      <div fl alic jucsb gap=smol>
        <p text semibold><?= $visible_name ?></p>
        <?php if ($Customer->company_name) : ?>
          <div background=yellow rounded ph6 pv2>
            <p text smol ttup bold>Unternehmen</p>
          </div>
        <?php endif; ?>
      </div>
      <p text><?= $Customer->full_address(); ?></p>
      <div fl alic gap=smol mt=smol>
        <?php if ($type === "bike") : ?>
          <div>
            <p text smol ttup bold>Fahrräder &middot; <?= $bike_count ?: "Keine" ?> </p>
          </div>
        <?php elseif ($type === "sewing") : ?>
          <div slight>
            <p text smol ttup bold><?= $sewing_count ?: "Keine" ?> Nähmaschinen</p>
          </div>
        <?php else : ?>

          <?php if ($bike_count) : ?>
            <p><?= $bike_count ?> Fahrräder</p>
          <?php endif; ?>

          <?php if ($sewing_count) : ?>
            <?= $bike_count ? "<p>&middot;</p>" : "" ?>
            <p><?= $sewing_count ?> Nähmaschinen</p>
          <?php endif; ?>

        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php if (!is_numeric($full_name)) : ?>

  <?php if ($Customers->count()) : ?>
    <div posrel background="slight" rounded="wide" style="height:1px;" mt mb>
      <div style="z-index:1;position:absolute;top:50%;left:50%;translate:-50% -50%;" background=bg ph24 rounded=wide>
        <p text ttup smol bold slight>oder</p>
      </div>
    </div>
  <?php endif; ?>

  <div data-action="customer:new" data-value="<?= $full_name; ?>" data-id="0" rounded background=light-green color=dark-green fl alic jucc gap=smol+ p24 clickable>
    <mi midler slight>person_add</mi>
    <p text><strong><?= $full_name ?></strong> als Kunden hinzufügen</p>
  </div>
<?php endif; ?>

<?php

/**
 * * Success
 */
exit($Request->success(message: "", data: ob_get_clean()));
