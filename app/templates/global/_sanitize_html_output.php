<?php

/**
 * Sanitizes the output for non DEV environments. I don't know if
 * this actually has some good to it, but I like the look of all
 * HTML elements clutched together inside the source code.
 */
if (PROD)
  ob_start("sanitize_output");
