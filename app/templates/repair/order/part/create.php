<?php

include dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Http\Request;
use Bruder\Model\Repair\RepairOrder;
use Bruder\Model\Repair\RepairOrderPart;
use Bruder\Model\Repair\RepairPart;
use Bruder\Utils\Str;

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
  exit($Request->error());

/**
 * @var string
 */
$type = filter_input(INPUT_POST, "type", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Type not set or invalid?
 */
if (!$type || !in_array($type, RepairOrder::$types))
  exit($Request->error());

/**
 * * Article ID
 *
 * @var string
 */
$article_id = filter_input(INPUT_POST, "article_id", FILTER_DEFAULT);

/**
 * @var ?string
 */
$article_id = $article_id ? $article_id : null;

/**
 * * Name
 *
 * @var string
 */
$name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * No name set?
 */
if (!$name)
  exit($Request->error("<strong>Gibt einen Namen ein!</strong>"));

/**
 * @var string
 */
$price = filter_input(INPUT_POST, "price", FILTER_DEFAULT);

/**
 * * Price
 */
if ($price) {

  /**
   * Remove all whitespace.
   */
  $price = Str::strip_whitespace($price);

  /**
   * Replace all commas with dots.
   */
  $price = str_replace(",", ".", $price);

  /**
   * Price is not a float?
   */
  if (!is_float($price) && !is_numeric($price))
    exit($Request->error("<strong>Der Preis sollte so aussehen: 5 oder 5.00 oder 5,00</strong>"));
}

/**
 * * Amount/Quantity
 *
 * @var int
 */
$amount = filter_input(INPUT_POST, "amount", FILTER_VALIDATE_INT);
$amount = $amount ? (int) $amount : 1;

/**
 * @var float
 */
$price = $price ? $price : 0.00;

/**
 * @var RepairPart
 */
$Part = !$article_id
  ?
  RepairPart::create([
    "type" => $type,
    "name" => $name,
    "price" => $price,
  ])
  :
  RepairPart::where("article_id", $article_id)
  ->first();

/**
 * Create a new RepairPart if none has been found nor created
 * before and set the article_id for it.
 */
if (!$Part)
  /**
   * @var RepairPart
   */
  $Part = RepairPart::create([
    "type" => $type,
    "article_id" => $article_id ? $article_id : null,
    "name" => $name,
    "price" => $price,
  ]);

/**
 * @var RepairOrderPart
 */
$OrderPart = $Order->parts()
  ->create([
    "repair_part_id" => $Part->id,
    "amount" => $amount,
  ]);

/**
 * Begin output buffer.
 */
ob_start();

/**
 * Include the input partial which will be appended to the list of
 * parts in the frontend.
 */
template("repair/order/part/input", variables: ["OrderPart" => $OrderPart]);

exit($Request->success("<strong>Hinzugefügt!</strong>", data: ob_get_clean()));
