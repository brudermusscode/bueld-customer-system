<?php

/**
 * @var ?string
 */
$sub = filter_var($GLOBALS["route_param_sub"] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var string
 */
$file_path = __DIR__ . "/new/_$sub.php";

/**
 * Include the existing file or fallback to the index.
 */
include file_exists($file_path) ? $file_path : __dir__ . "/new/_index.php";
