<?php

include dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Application\Logger;
use Bruder\Controller\Repair\OrdersController;
use Bruder\Http\Request;
use Bruder\Model\Repair\RepairOrder;

/**
 * @var Request $Request
 */

/**
 * @var int
 */
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

/**
 * @var RepairOrder
 */
$Order = RepairOrder::find($id);

/**
 * Order not found?
 */
if (!$Order)
  exit($Request->error("<strong>Auftrag nicht gefunden.</strong>"));

/**
 * @var string
 */
$type = filter_input(INPUT_POST, "type", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Type not valid?
 */
if (!in_array($type, ["confirmation", "invoice"]))
  exit($Request->error("<strong>Ungültiger Dokumenten-Typ angefragt.</strong>"));

/**
 * @var string
 */
$file_name = $Order->reference_id . "_" . (
  match ($type) {
    "confirmation" => "auftrag",
    "invoice" => "rechnung"
  }
) . ".pdf";

/**
 * @var string
 */
$file_path = _root() . "/documents/orders/$Order->type/$file_name";

/**
 * File doesn't exist?
 */
if (!file_exists($file_path)) {

  /**
   * Try to create a new document.
   */
  try {

    $File = match ($type) {
      "confirmation" => $Order->generate_confirmation(),
      default => $Order->generate_invoice(),
    };

    $file_path = $File->data["file_path"];
  } catch (Exception $e) {

    /**
     * Log an error!
     */
    Logger::to_file($e, "document_generation_errors.log");

    exit($Request->error("<strong>Es konnte kein Dokument gefunden werden.</strong>"));
  }
}

/**
 * Read out the generated file.
 */
$file = @file_get_contents($file_path);
$file_base64_encoded = base64_encode($file);

/**
 * ? Success
 */
exit($Request->success(
  "<strong>Dokument bereit!</strong> Du kannst es nun herunterladen.",
  data: $file_base64_encoded
));
