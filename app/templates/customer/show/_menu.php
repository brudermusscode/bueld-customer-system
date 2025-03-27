<?php

/**
 * @var Customer $Cusomter
 * @var string $base_url
 * @var string $object_type => bike, sewing
 */

?>

<!--- TYPE MENU --->
<div fl alic jucc flex-wrap gap=smol>
  <a href="<?= "$base_url/bike" ?>">
    <mbutton-type <?= $object_type === "bike" ? "active" : "" ?> material size=mid>
      <p text smol>Fahrrad <span show-active style=font-weight:300;>&middot; <?= $Customer->bikes->count() ?></span></p>
    </mbutton-type>
  </a>

  <a href="<?= "$base_url/sewing" ?>">
    <mbutton-type <?= $object_type === "sewing" ? "active" : "" ?> material size=mid>
      <p text smol>Nähmaschine <span show-active style=font-weight:300;>&middot; <?= $Customer->sewing_machines->count() ?></span></p>
    </mbutton-type>
  </a>
</div>