<?php

/**
 * This file includes the main.bundle.js which can be used around
 * the website as a script file. I bet it's loading faster if it
 * is directly included in the DOM instead of imported through a file.
 */

if (DEV) {
  echo '<script type="text/javascript" src="' . _env("NODE_BUNDLE_OUTPUT_PUBLIC_PATH") . 'main.bundle.js"></script>';
} else {
  echo "<script defer>";
  include ASSETS . "/js/main.production.bundle.js";
  echo "</script>";
}
