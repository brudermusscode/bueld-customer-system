<?php

include dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Http\Request;
use Bruder\Model\Repair\RepairOrderPart;

/**
 * @var Request $Request
 */

/**
 * @var int
 */
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

/**
 * @var RepairOrderPart
 */
$OrderPart = RepairOrderPart::find($id);

/**
 * Delete only when not done yet.
 */
if ($OrderPart && !$OrderPart->order->is_done())
  $OrderPart->delete();
else
  exit($Request->error("<strong>Dieser Auftrag ist bereits abgeschlossen.</strong>"));

exit($Request->success("<strong>Gelöscht!</strong>"));
