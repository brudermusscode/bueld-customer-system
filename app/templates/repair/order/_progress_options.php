<?php

/**
 * @var string
 */
$back_uri ??= "$base_url/employee";

?>

<div fl alic gap>
  <a href="<?= $back_uri ?>" no-scroll-top>
    <mbutton size="mid" material background=slighter>
      Zurück
    </mbutton>
  </a>
  <div background=slight rounded=wide flone style=height:2px;></div>
  <mbutton
    <?= $tabindex ? "tabindex=$tabindex" : "" ?>
    submit-closest size="mid" has-icon="right" material background="green" color="light-green">
    Weiter
    <mi>arrow_forward</mi>
  </mbutton>
</div>