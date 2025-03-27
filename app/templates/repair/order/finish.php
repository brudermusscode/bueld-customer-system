<?php

include dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Application\Logger;
use Bruder\Controller\Repair\OrdersController;
use Bruder\Http\Request;
use Bruder\Model\Repair\RepairOrder;

/**
 * @var Request $Request
 */
$update = json_decode(
  (new OrdersController($_POST))
    ->update()
);

/**
 * Update failed?
 */
if (!$update->status)
  exit(json_encode($update));

/**
 * @var RepairOrder
 */
$Order = RepairOrder::find($update->data->repair_order_id);

/**
 * Generate invoice.
 */
$generate_paper = $Order->generate_invoice();

/**
 * Paper generation failed?
 */
if (!$generate_paper->status)
  exit($generate_paper);

/**
 * Read out the generated file.
 */
$file = @file_get_contents($generate_paper->data["file_path"]);
$file_base64_encoded = base64_encode($file);

/**
 * File not found?
 */
if (!$file) {

  /**
   * Log it!
   */
  Logger::to_file(new Exception("Could not find generated invoice with file_get_contents for id $Order->id"), "order_errors.log");

  exit($Request->error("<strong>Es konnte keine Rechnung gefunden werden.</strong> Eventuell ist etwas bei der Generierung schief gelaufen."));
}

/**
 * ? Success
 */
exit($Request->success(
  "<strong>Auftrag abgeschlossen!</strong> Du kannst nun die Rechnung für den Kunden herunterladen und drucken.",
  data: $file_base64_encoded
));
