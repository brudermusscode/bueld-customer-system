<?php

use Bruder\Application\Server;

/**
 * Require the main initialization file.
 */
require_once dirname(__DIR__) . "/config/init.php";

/**
 * Make website look cool when in source code.
 */
include TEMPLATE . "/global/_sanitize_html_output.php";

?>

<!DOCTYPE html>
<html lang=en>

<head>
  <link rel="canonical" href="<?php echo HOME_URL . Server::get("REQUEST_URI"); ?>" />
  <link rel="home" href="<?php echo HOME_URL; ?>" />
  <link rel="icon" type="image/x-icon" href="/favicon.ico" />
  <link rel="apple-touch-icon" href="/favicon.ico" />

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf_token" content="<?php echo $csrf_token; ?>" />


  <!--- Tell IE to render webpage for edge --->
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="application-name" content="<?php echo APP_NAME; ?>">

  <?php

  /**
   * Include SEO stuff from the environment only if activated.
   */
  if (_env("SEO_ACTIVE")) : ?>
    <meta name="keywords" content="<?php echo _env("SEO_KEYWORDS"); ?>" />
    <meta name="description" content="<?php echo $og->desc ?? _env("SEO_DESCRIPTION"); ?>" />
  <?php endif; ?>

  <!--- Title --->
  <title><?= CURRENT_PAGE_TITLE ?></title>

  <?php

  /**
   * Any js and css file.
   */
  include TEMPLATE . "/global/_yield_requirements.php";

  ?>
</head>

<body toggled=false initialized=true>
  <page-loader visible=false loading>
    <div material-bar-loader="" class="linear-progress-material" style="position:absolute;top:0;left:0;width:100vw;">
      <div class="bar bar1"></div>
      <div class="bar bar2"></div>
    </div>
  </page-loader>

  <?php

  /**
   * The main header.
   */
  template("global/header"); ?>

  <main>
    <?php

    /**
     * Include the template coming from the current route.
     */
    echo $_INCLUDE_TEMPLATE;

    ?>
  </main>
</body>

</html>

<?php

include TEMPLATE . "/global/_yield_end.php";
