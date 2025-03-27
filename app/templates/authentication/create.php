<?php

include dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Controller\AuthenticationsController;
use Bruder\Http\Request;

/**
 * @var Request $Request
 */

$Controller = new AuthenticationsController($_POST);

exit($Controller->create());
