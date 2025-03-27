<?php

namespace Bruder\Application;

class Application
{
  /**
   * Holds default dialogues for UI responsiveness.
   *
   * @var object
   */
  protected static $default_dialogues;

  /**
   * Applications default text responses with neat keys.
   *
   * @return array
   */
  public static function get_default_dialogues()
  {
    return [
      "INVALID_REQUEST" =>
      "<strong>Invalid request!</strong> Try again and if the error persists, open a support ticket on our discord server.",

      "UNAUTHORIZED" => "<strong>Du bist nicht authorisiert, um diese Aktion auszuführen.</strong>",

      /**
       * Interactions.
       */
      "UPDATED" => "<strong>Updated!</strong>",
      "CREATED" => "<strong>Created!</strong>",
      "DELETED" => "<strong>Removed!</strong>",
    ];
  }

  /**
   * Builds the URI for the current environment.
   *
   * @return string The URI.
   */
  public static function host_URI()
  {
    return $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"];
  }
}
