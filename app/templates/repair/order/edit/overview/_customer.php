<?php

use Bruder\Model\Customer;

/**
 * @var Customer $Customer
 */

?>

<section repairs fl fldircol gap>
  <a href="/repair/order/edit/<?= $RepairOrder->id ?>/customer#activeObject" no-scroll-top>
    <div fl gap alic jucsb rounded=wide outlined p24 clickable>
      <div fl alic gap=smol+>
        <mi wide>face</mi>
        <div fl fldircol gap=smolest>
          <p text semibold><?= $Customer->full_name() ?></p>
          <p text smolplus><span slight><?= $Customer->address_line_1 . ", " . $Customer->city ?></span> <?php if ($Customer->mail) : ?>&middot; <span color=company><?= $Customer->mail ?></span><?php endif ?></p>
        </div>
      </div>
      <mi style="height:1.4em;min-width:1.4em;" mid circled fl alic jucc color=dark-green background=light-green>done</mi>
    </div>
  </a>
</section>