<?php

include dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Http\Request;
use Bruder\Model\Repair\RepairOrder;

/**
 * @var Request $Request
 */

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

/**
 * @var ?RepairOrder
 */
$RepairOrder = RepairOrder::find($id);

/**
 * No Order found?
 */
if (!$RepairOrder)
  exit($Request->error("<strong>Keinen Auftrag mit dieser ID gefunden.</strong>"));

/**
 * Create a PDF file.
 */
$Request = $RepairOrder->generate_confirmation();

if ($Request->status) {

  header("Content-Type: application/pdf");
  header("Content-Disposition: inline; filename={$Request->data["file_name"]}");
  header("Content-Length: " . filesize($Request->data["file_path"]));

  readfile($Request->data["file_path"]);

  exit;

  // exit(file_get_contents($Request->data["file_path"]));
}

/**
 * ! Error
 */
exit(json_encode($Request));
