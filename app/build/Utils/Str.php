<?php

namespace Bruder\Utils;

class Str
{
  /**
   * Checks if all of the gioven $characters are existent in $string.
   *
   * @param string $string
   * @param string $characters
   * @return bool
   */
  public static function contains_any(string $string, string $characters)
  {
    foreach (str_split($characters) as $char)
      if (strpos($string, $char) === false)
        return false;

    return true;
  }

  /**
   * Escape multiple strings using htmlentities.
   *
   * @param string ...$strings The strings to be escaped.
   * @return array The array of escaped strings.
   */
  public static function escape(string ...$strings)
  {
    $escapedStrings = [];

    foreach ($strings as $string) {
      $escapedString = htmlentities($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
      $escapedStrings[] = $escapedString;
    }

    return $escapedStrings;
  }

  /**
   * Strips all possible whitespaces from a given string and returns
   * the string without these
   *
   * @param string The string to be stripped
   * @return string
   */
  public static function strip_whitespace(string $str): string
  {
    return preg_replace('/\s+/', '', $str);
  }

  /**
   *  Validates the length of a string
   *
   * @param string $str The string to be validated
   * @param array $range The range from to
   * @return boolean Whether or not it's in range
   */
  public static function validate_length(string $str, array $range = [0, 32])
  {
    return (strlen($str) >= $range[0] && strlen($str) <= $range[1]);
  }

  /**
   * The theme name comes with dashes, remove those and replace them
   * with spaces and uppercase all first letters.
   *
   * @return string
   */
  public static function format_theme_name($str): string
  {
    $formatted_theme_name = explode("-", $str);

    foreach ($formatted_theme_name as $key => $a)
      $formatted_theme_name[$key] = ucfirst($a);

    return implode(" ", $formatted_theme_name);
  }

  /**
   * @return string
   */
  public static function truncate(string $inputString, int $maxLength = 100)
  {
    if (strlen($inputString) > $maxLength) {
      $truncatedString = substr($inputString, 0, $maxLength) . "&hellip;";
      return $truncatedString;
    } else {
      return $inputString;
    }
  }
}
