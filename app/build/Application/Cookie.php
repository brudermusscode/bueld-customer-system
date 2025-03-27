<?php

namespace Bruder\Application;

use Bruder\Http\Domain;

class Cookie
{

  /**
   * @return boolean
   */
  public static function exists($name)
  {
    return isset($_COOKIE[$name]);
  }

  /**
   * @param string $name
   * @return ?string
   */
  public static function get(string|int $name)
  {
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
  }

  /**
   * @param string $name
   * @param string $value
   * @param string $time - A timestamp in DateTime class format
   *                       (e. g. +2 months)
   * @param string $secure
   * @param ?string $samesite - None, Lax, Strict
   * @return bool
   */
  public static function set(
    string $name,
    string $value,
    ?string $time = null,
    bool $secure = true,
    ?string $samesite = null,
    bool $httponly = true,
  ) {
    $domain_name = Domain::clean($_SERVER["HTTP_HOST"]);

    $_COOKIE[$name] = $value;

    /**
     * Set the cookie.
     */
    return setcookie(
      $name,
      $value,
      [
        "expires" => strtotime($time),
        "domain" => $domain_name,
        "path" => "/",
        "samesite" => $samesite,
        "secure" => $secure,
        "httponly" => $httponly,
      ]
    );
  }

  /**
   * @return boolean
   */
  public static function delete(string $name)
  {
    if (isset($_COOKIE[$name])) {
      $_COOKIE[$name] = '';
      unset($_COOKIE[$name]);

      self::set($name, '', '-1 minutes', current_env() !== "dev");
    }

    return true;
  }
}
