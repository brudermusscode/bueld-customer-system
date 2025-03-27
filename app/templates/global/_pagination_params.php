<?php

/**
 * @var string
 */
$default_sort ??= "newest";

/**
 * @var string
 */
$default_filter ??= "all";

/**
 * @var int
 */
$ppage  = (int) ($GLOBALS["route_param_ppage"] ?? 1);

/**
 * @var string
 */
$sort = ($GLOBALS["route_param_sort"] ?? $default_sort);

/**
 * @var string
 */
$filter = ($GLOBALS["route_param_filter"] ?? $default_filter);

/**
 * @var bool
 */
$pagination_without_query ??= false;

/**
 * @var string
 */
if (!$pagination_without_query)
  $query  = filter_input(INPUT_GET, "query", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var string
 */
$append_query_string = $query ? "?query=$query" : "";

/**
 * @var int
 */
$limit ??= 12;

/**
 * @var int
 */
$offset = ($ppage - 1) * $limit;
