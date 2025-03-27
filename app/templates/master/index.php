<?php

use Bruder\Model\Customer;
use Bruder\Time\Time;

?>

<div content-width=smol center-content fl fldircol gap=wide style="padding-block:8.2em;">
  <div fl fldircol gap=smol+>
    <p text wide bold>Stammdaten</p>

    <div fl fldircol gap=smol>
      <a href="/master/customers">
        <div typer rounded clickable>
          <div fl alic gap>
            <mi icon>face</mi>
            <div fl fldircol gap=smoler>
              <p text std bold>Kunden</p>
              <p text smol color=company3>Letzter <?= Time::ago(Customer::latest()->first()->created_at); ?> hinzugefügt</p>
            </div>
          </div>
          <mi mid>arrow_forward</mi>
        </div>
      </a>
      <a href="/master/repair/types">
        <div typer rounded clickable>
          <div fl alic gap>
            <mi icon>build</mi>
            <div fl fldircol gap=smoler>
              <p text std bold>Reparaturen</p>
            </div>
          </div>
          <mi mid>arrow_forward</mi>
        </div>
      </a>
    </div>
  </div>
</div>