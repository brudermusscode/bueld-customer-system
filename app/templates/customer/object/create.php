<?php

include dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Controller\Customer\ObjectsController;
use Bruder\Http\Request;

/**
 * @var Request $Request
 */

$Controller = new ObjectsController($_POST);

exit($Controller->create());
