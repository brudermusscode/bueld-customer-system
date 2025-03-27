<?php

namespace Bruder\Utils;

use Bruder\Application\Application;
use ParagonIE\ConstantTime\Encoding;
use foroco\BrowserDetection;

class Utils
{
  /**
   * Converts upper thousands with proper ending
   *
   * @return string
   */
  public static function round_with_ending($num)
  {
    if ($num > 1000) {
      $x = round($num);
      $x_number_format = number_format($x);
      $x_array = explode(',', $x_number_format);
      $x_parts = array(' k', ' m', ' b', ' t');
      $x_count_parts = count($x_array) - 1;
      $x_display = $x;
      $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
      $x_display .= $x_parts[$x_count_parts - 1];

      return $x_display;
    }

    return $num;
  }

  /**
   * Creates a random number of given length
   *
   * @return int|string
   */
  public static function random_numeric_token($length = 12)
  {
    $ass = str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    return $ass;
  }

  /**
   * returns a unique token with a specific length
   */
  public static function create_unique_token(int $len)
  {
    $random = random_bytes($len);
    $s = Encoding::base64Encode($random);
    return $s;
  }

  public static function random_alpha_token($length = 12)
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
  }

  /**
   * @return object
   */
  public static function detect_browser()
  {
    $Browser = new BrowserDetection();
    $useragent = $_SERVER['HTTP_USER_AGENT'] ?? "";
    $result = $Browser->getAll($useragent);

    return (object) $result;
  }

  /**
   * Strips off any parenthesis and their insides for a clean string
   * and trims of the end of the string, if it's whitespace
   * @return string
   */
  public static function strip_parenthesis(string $string)
  {
    $a =  preg_replace("/\([^)]+\)/", " ", $string);
    $a =  preg_replace("/\\[[^\]]+\\]/", " ", $a);

    return rtrim($a);
  }

  /**
   * @return string
   */
  public static function get_token(string $key)
  {
    $env_path = include Application::root() . '/config/security/tokens.php';
    return $env_path[$key] ?? null;
  }
}
