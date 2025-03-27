<?php

/**
 * @var string
 */
$document = $GLOBALS["route_param_document"] ?? "";

/**
 * @var string
 */
$file_path = __DIR__ . "/$document.html";

/**
 * Include requested file if exists or fallback to unavailable.
 */
include file_exists($file_path) ? $file_path : TEMPLATE . "/global/unavailable.php";
