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
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

/**
 * @var ?RepairOrder
 */
$RepairOrder = RepairOrder::find($id);

/**
 * No Order found?
 */
if (!$RepairOrder)
  exit($Request->error("<strong>Dieser Auftrag existiert nicht mehr!</strong>"));

/**
 * Create a PDF file.
 */
$Request = $RepairOrder->generate_paper();

if ($Request->status) {
  header("Content-Type: application/pdf");
  header("Content-Disposition: attachment; filename={$Request->data["file_name"]}");

  exit(file_get_contents($Request->data["file_path"]));
} else
  exit(json_encode($Request));
