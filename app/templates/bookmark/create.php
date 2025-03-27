<?php

include dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Http\Request;
use Bruder\Model\Bookmark;
use Bruder\Model\Customer;
use Bruder\Model\Repair\RepairOrder;

/**
 * @var Request $Request
 */

/**
 * @var int
 */
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

/**
 * @var string
 */
$type = filter_input(INPUT_POST, "type", FILTER_DEFAULT);

/**
 * @var Customer|RepairOrder|null
 */
$Object = Bookmark::find_reference($type, $id);

/**
 * No object was found?
 */
if (!$Object)
  exit($Request->error());

/**
 * A bookmark exists?
 */
if ($Object->bookmark) {

  /**
   * Create a new bookmark
   */
  $Object->bookmark()
    ->delete();

  exit($Request->success("<strong>Nicht merken!</strong>"));
} else {

  /**
   * Delete the bookmark.
   */
  $Object->bookmark()
    ->create(["type" => $type]);

  exit($Request->success("<strong>Merken!</strong>"));
}
