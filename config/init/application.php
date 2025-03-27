<?php

/**
 * Set the default timezone.
 */
date_default_timezone_set('Europe/Berlin');

/**
 * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
 * ,,,,,,,,,,,,,, Ensure files ,,,,,,,,,,,,,,,,,,,
 * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
 */

/**
 * ? /storage/logs/php_errors.php
 */

/**
 * Define the path to the log file.
 */
$log_file_path = _root() . "/storage/logs/php_errors.log";
$log_directory = dirname($log_file_path);

/**
 * Check if the directory exists, if not, create it.
 */
if (!is_dir($log_directory))
  mkdir($log_directory, 0777, true);

/**
 * Check if the log file exists, if not, create it.
 */
if (!file_exists($log_file_path))
  touch($log_file_path);
