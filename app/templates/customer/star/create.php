<?php

include dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Http\Request;
use Bruder\Model\Customer;

/**
 * @var Request $Request
 */

// var_dump($_POST);

/**
 * @var int
 */
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

/**
 * @var ?Customer
 */
$Customer = Customer::find($id);

/**
 * Customer doesn't exist?
 */
if (!$Customer)
  exit($Request->error());

/**
 * Has been starred already? Delete it then.
 */
if ($Customer->star)
  $Customer->star->delete();

/**
 * Create a new one.
 */
else
  $Customer->star()
    ->create();

/**
 * ? Success
 */
exit($Request->success("<strong>Kunde wurde gemerkt!</strong>"));
