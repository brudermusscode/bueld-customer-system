<?php

include dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Http\Request;
use Bruder\Controller\Repair\OrdersController;

/**
 * @var Request $Request
 */

$Controller = new OrdersController($_POST);

exit($Controller->update());
