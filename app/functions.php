<?php

use Bruder\Http\Request;
use Bruder\Model\User\User;
use Bruder\Application\Session as SessionManager;

/**
 * Return the root path.
 *
 * @return string
 */
function _root()
{
  return dirname(__DIR__);
}

/**
 * Get a specific value for a key from the .env file.
 *
 * @param string $key
 * @return ?string
 * @throws Exception
 */
function _env(?string $key = null)
{
  $env_file_path = _root() . "/.env";

  /**
   * Environment variables file is missing?
   */
  if (!file_exists($env_file_path))
    throw new Exception("❌ Environment variables file not found. Create a file named '.env' in the project root directory and add variables according to what is needed for this specific project. Usually, there is a .env.example file somewhere to copy from.");

  /**
   * @var object
   */
  $parsed = (object) parse_ini_file($env_file_path);

  return !$key ? $parsed : ($parsed->$key ?? null);
}

/**
 * @return string
 */
function current_env()
{
  return _env("ENVIRONMENT");
}

/**
 * Sanitizes the complete given html from whitespace and comments.
 *
 * @param string $html
 * @return string
 */
function sanitize_output($html)
{
  $search = array(
    '/(\n|^)(\x20+|\t)/',
    '/(\n|^)\/\/(.*?)(\n|$)/',
    '/\n/',
    '/\<\!--.*?-->/',
    '/(\x20+|\t)/', # Delete multispace (Without \n)
    '/\>\s+\</', # strip whitespaces between tags
    '/(\"|\')\s+\>/', # strip whitespaces between quotation ("') and end tags
    '/=\s+(\"|\')/'
  ); # strip whitespaces between = "'

  $replace = array(
    "\n",
    "\n",
    " ",
    "",
    " ",
    "><",
    "$1>",
    "=$1"
  );

  $html = preg_replace($search, $replace, $html);
  return $html;
}

/**
 * Print out anything in nicley formatted pattern.
 *
 * @param mixed
 * @return string
 */
function pdie(mixed ...$a)
{
  /**
   * Bad request header, so the frontend will include the
   * exception-container on any request method.
   */
  header("HTTP/1.0 400 Bad request");

  echo <<<HTML
  <exception-container>
    <div o-closer hoverable circled style="position:fixed;top:1.2em;right:1.2em;">
      <mi wide>close</mi>
    </div>
    <div p42>
      <h1 text wider bold>pdie() returned</h1>
      <pre force-word-wrap>
  HTML;

  var_dump($a);

  echo <<<HTML
      </pre>
    </div>
  </exception-container>
  HTML;

  die;
}

/**
 * Redirects to another location and dies.
 *
 * @param string $uri
 * @return die
 */
function redirect(string $uri, array $headers = [])
{
  /**
   * Set given headers.
   */
  foreach ($headers as $header) {
    header($header);
  }

  /**
   * Set header location.
   */
  header("location: $uri");

  die;
}

/**
 * Includes template files. It gets rid for the need of
 * underscores and .php endings.
 *
 * @param string $file
 * @param bool $is_partial
 * @return require The template
 */
function template(string $file, bool $is_partial = true, array $variables = [])
{
  $has_php_ending = strrpos($file, ".php");
  $slash_position = strrpos($file, "/");

  /**
   * If the file path has a / and its not a partial, replace the /
   * with /_ as all partials should have the convention of being
   * named with underscores at the beginning.
   */
  if ($slash_position !== FALSE && $is_partial) {
    $file_path = substr_replace($file, "/_", $slash_position, 1);

    /**
     * If the file path has no / but still is a partial, just
     * add a _ at the beginning.
     */
  } else if (!$slash_position && $is_partial) {
    $file_path = "_$file";

    /**
     * Just use the file path if none of the above match.
     */
  } else {
    $file_path = $file;
  }

  /**
   * Extracts variables to make them available in this functions
   * scope from outside.
   */
  extract($variables);

  include _root() . "/app/templates/$file_path" . (!$has_php_ending ? ".php" : "");
}

/**
 * @param User $resource
 * @param bool $logged
 * @param array $can
 * @param bool $die_on_error
 * @return void|die
 */
function authorize(?User $resource = null, bool $logged = true, array $can = [], bool $die_on_error = true)
{
  $Request = new Request;

  /**
   * Fallback to the current user that is globally set if there is
   * no other resource given.
   * @var User
   */
  $resource = $resource !== null ? $resource : $GLOBALS["current_user"] ?? null;

  /**
   * @var ?string
   */
  $error = null;

  /**
   * User needs to be logged in.
   */
  if ($logged && !($resource instanceof User))
    $error = "!UNLOGGED";

  /**
   * User can't be logged in
   */
  if (!$logged && ($resource instanceof User))
    $error = "!LOGGED";

  /**
   * User has enought permissions to trigger the given action?
   */
  if (count($can) < 2 || !$resource?->can($can[0], $can[1]))
    $error = "!UNAUTHORIZED";

  /**
   * If all passed, just return into the void.
   */
  if (!$error) return;

  return $die_on_error ? die($Request->error($error)) : $Request->error($error);
}
