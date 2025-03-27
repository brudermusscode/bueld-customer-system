<?php

include dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Http\Request;
use Bruder\Controller\Repair\TypesController;

/**
 * @var Request $Request
 */

$Controller = new TypesController($_POST);

exit($Controller->update());
