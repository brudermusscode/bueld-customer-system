<?php

include dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Http\Request;
use Bruder\Model\Customer;

/**
 * @var Request $Request
 */

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

/**
 * @var ?Customer
 */
$Customer = Customer::selectRaw("
  id, company_name, firstname, lastname, sex,
  address_line_1, phone, mail, postcode, city, country_code, created_at,
  CONCAT(
  '{\"address_line_1\":', '\"', address_line_1, '\"',
  ', \"postcode\":', '\"', postcode, '\"',
  ',\"city\":', '\"',city, '\"', '}') AS address
  ")
  ->where("id", $id)
  ->first();

exit($Request->success(message: "", data: $Customer));
