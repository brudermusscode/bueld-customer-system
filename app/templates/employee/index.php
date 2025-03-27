<?php

use Bruder\Model\Employee;

if (!CURRENT_USER?->can("manage", "employees")) :
  include TEMPLATE . "/error/404.php";
else :

?>

  <div content-gap fl fldircol>
    <page-title scroll-manipulated="">
      <div content-width="wider" center-content="">
        <div fl="" fldircol="" gap="smol">
          <h1>Mitarbeiter</h1>
          <h2>Aktuelle und vergangene Mitarbeiter verwalten</h2>
        </div>
      </div>
    </page-title>

    <page-with-title main-distance>
      <div content-width=smol center-content fl fldircol gap=smol>

        <?php foreach (Employee::all() as $Employee) : ?>
          <div fl alic gap p24 rounded outlined>
            <p text bold><?= $Employee->full_name() ?></p>
          </div>
        <?php endforeach; ?>

      </div>
    </page-with-title>
  </div>

<?php endif;
