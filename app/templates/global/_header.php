<sub-menu shrinked fl fldircol jucsb alic style=padding-bottom:42px;>
  <div class="sub_menu__inr">
    <a href="/">
      <div logo fl alic fldircol jucc gap=smoler>
        <div logo-icon fl alic jucc circled background=company3>
          <mi wide bold color=nav>directions_bike</mi>
        </div>
        <p logo-text text std bold color=company3>Büld</p>
      </div>
    </a>

    <mbutton request-get="action:new" material color=light size=wide icon-only background=company2>
      <mi wide>add</mi>
    </mbutton>

    <!--- ACCOUNT --->
    <div class="sm__button_row">
      <a page=home href="/" <?= CURRENT_PAGE === "home" || !CURRENT_PAGE ? "active" : "" ?>>
        <div class="sm__button">
          <p class="b__icon">
            <mi>window</mi>
          </p>
          <p class="b__name" text std>Dashboard</p>
        </div>
      </a>

      <a page=orders href="/orders" <?= CURRENT_PAGE === "orders" ? "active" : "" ?>>
        <div class="sm__button">
          <p class="b__icon">
            <mi>contract</mi>
          </p>
          <p class="b__name" text std>Aufträge</p>
        </div>
      </a>

      <divide horiz></divide>

      <a page=master href="/master" <?= CURRENT_PAGE === "master" ? "active" : "" ?>>
        <div class="sm__button">
          <p class="b__icon">
            <mi>database</mi>
          </p>
          <p class="b__name" text std>Stamm</p>
        </div>
      </a>

      <?php

      /**
       * This tab is only visible to employees, that have either
       * MASTER permissions or are in the group of MANAGE_EMPLOYEES.
       */
      if (CURRENT_USER?->can("manage", "employees")) : ?>
        <a page=employees href="/employees" <?= CURRENT_PAGE === "employees" ? "active" : "" ?>>
          <div class="sm__button">
            <p class="b__icon">
              <mi>supervised_user_circle</mi>
            </p>
            <p class="b__name" text std>Mitarbeiter</p>
          </div>
        </a>
      <?php endif; ?>
    </div>
  </div>

  <div>
    <?php if (!LOGGED) : ?>
      <div request-get="authentication:login" fl alic jucc style="height:62px;width:62px;" circled background=slighter hoverable has-tooltip=right>
        <mi mid>passkey</mi>
        <div ttooltip>
          Als Administrator einloggen
        </div>
      </div>
    <?php else : ?>
      <div fl alic jucc style="height:62px;width:62px;" circled background=slighter hoverable has-tooltip=right>
        <p text midler bold>
          <?= implode("", array_map(fn($word) => $word[0], explode(" ", CURRENT_USER->employee->full_name()))); ?>
        </p>
        <div ttooltip>
          <?= (CURRENT_USER->employee->full_name()) ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</sub-menu>