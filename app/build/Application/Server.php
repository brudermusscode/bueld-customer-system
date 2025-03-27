<?php

namespace Bruder\Application;

class Server
{
  /**
   *
   * Gets a specific server's key value and checks if it exists before
   * @return mixed
   */
  public static function get(string $key)
  {
    $key = strtoupper($key);

    return (isset($_SERVER[$key]) ? $_SERVER[$key] : null);
  }

  /**
   *
   * Returns all entries for super global server
   * @return array
   */
  public static function get_all()
  {
    return (isset($_SERVER) ? $_SERVER : null);
  }
}
