<?php

namespace Bruder\Time;

use DateTime;

class Time
{

  public static function ago($datetime, $full = false)
  {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $w = floor($diff->d / 7);
    $diff->d -= $w * 7;

    $timename = array(
      'y' => 'Jahr',
      'm' => 'Monat',
      'd' => 'Tag',
      'h' => 'Stunde',
      'i' => 'Minute',
      's' => 'Sekunde',
    );

    $arr_dt = (array) $diff;

    foreach ($timename as $k => &$v) {
      if ($arr_dt[$k]) {
        $v = $arr_dt[$k] . ' ' . $v . ($arr_dt[$k] > 1 ?  ($v[strlen($v) - 1] == 'e' ? 'n' : 'en') : '');
      } else {
        unset($timename[$k]);
      }
    }

    if (!$full) $timename = array_slice($timename, 0, 1);

    return $timename ? 'vor ' . implode(', ', $timename) : 'gerade jetzt';
  }

  /**
   * Converts a unix timestamp to a string saying how much time is left.
   *
   * @param int $unix_timestamp The timestamp.
   * @return strin The string.
   */
  public static function left(int $unix_timestamp)
  {
    $now = time();
    $difference = $unix_timestamp - $now;

    if ($difference <= 0)
      return null;

    $intervals = array(
      'year' => 31536000,
      'month' => 2592000,
      'week' => 604800,
      'day' => 86400,
      'hour' => 3600,
      'minute' => 60
    );

    $futureString = '';

    foreach ($intervals as $interval => $seconds) {
      $count = floor($difference / $seconds);

      if ($count > 0) {
        $futureString .= ($count == 1) ? "$count $interval" : "$count {$interval}s";
        $difference %= $seconds;

        // To show only one level of granularity (e.g., "1 day 2 hours" instead of "1 day 2 hours 30 minutes")
        break;
      }
    }

    return ($futureString === '') ? 'Less than a minute' : $futureString . ' left';
  }
}
