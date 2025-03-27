<?php

/**
 * @var Customer $Customer
 * @var string $base_url
 * @var string $object_type => bike
 */

?>

<div fl fldircol gap=smol+ mt=mid mb>
  <div flex-truncate>
    <p text bold style=font-size:4.8em;margin-bottom:-.1em; trimt><?= $Customer->full_name() ?></p>
    <p text><?= $Customer->mail ?: "" ?></p>
  </div>

  <div fl jucsb alic>
    <div fl alic gap=smoler>
      <?php if ($Customer->is_leasing) : ?>
        <mchip outlined tag has-icon=left color=blue>
          <mi midler>pedal_bike</mi>
          Leasing-Kunde
        </mchip>
      <?php endif; ?>

      <?php if ($Customer->is_asshole) : ?>
        <mchip outlined tag has-icon=left color=red>
          <mi midler>sentiment_very_dissatisfied</mi>
          Arschloch
        </mchip>
      <?php endif; ?>
    </div>

    <mchip outlined has-icon=left has-tooltip=bottom>
      <mi>share_location</mi>
      <?= $Customer->city ?>
      <div ttooltip>
        Wohnort
      </div>
    </mchip>
  </div>
</div>