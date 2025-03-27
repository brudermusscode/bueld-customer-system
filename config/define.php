<?php

use Bruder\Application\Application;

define("ENV", _env());
define("DEV", current_env() === "dev");
define("STAGE", current_env() === "stage");
define('PROD', current_env() === "prod");

/**
 * Define Application constants to use around the website.
 */
define("CURRENT_TIMESTAMP",  date("Y-m-d H:i:s", time()));
define('APP', new Application);
define("ROOT", _root());
define("PREROOT", dirname(ROOT));
define("VENDOR", ROOT . '/vendor');
define("CONFIG", ROOT . '/config');
define("ROUTE", ROOT . '/config/routes');
define("MODEL", ROOT . '/app/models');
define("BUILD", ROOT . '/app/build');
define("LOG", _env("LOG_PATH"));
define("JSON_RESPONSE", 'Content-type: application/json');
define("HTML_RESPONSE", 'Content-type: text/html');
define(false, 0);
define(true, 1);

/**
 * Template directory
 */
define("TEMPLATE", ROOT . '/app/templates');
define("COMPONENT", ROOT . '/app/templates/components');
define("UNAVAILABLE", ROOT . '/app/templates/global/unavailable.php');

/**
 * Single template files reoccuring.
 */
define("PAGINATION", COMPONENT . "/_pagination.php");
define("PAGINATION_PARAMS", TEMPLATE . "/global/_pagination_params.php");

/**
 * Base definitions
 */
define("APP_NAME", _env("APP_NAME"));
define('APP_VERSION', _env("APP_VERSION"));
define('MAINTENANCE', _env("MAINTENANCE"));
define("HOME_URL", _env("SERVER_ADDRESS"));
define("IMAGE", _env("SERVER_ADDRESS") . "/assets/images");
define("VIDEO", _env("SERVER_ADDRESS") . "/assets/videos");
define("SCRIPT", _env("SERVER_ADDRESS") . "/assets/javascript");
define("STYLE", _env("SERVER_ADDRESS") . "/assets/stylesheets");
define("ICON", _env("SERVER_ADDRESS") . "/assets/icons");

/**
 * PATHS
 */
define("ASSETS", ROOT . "/public/assets");


/**
 * Maintenance
 */
define("IS_MAINTENANCE", false);
