<?php

/**
 * @var string $base_url
 * @var string $query
 * @var string $filter
 * @var array $filter_map
 * @var string $default_filter
 * @var string $sort
 * @var array $sort_map
 * @var string $default_sort
 * @var string $filter_search_terms
 */

/**
 * @var string
 */
$filter_base_url = "$base_url/1/$sort";

?>

<div fl jucsb gap=smol posrel>
  <div fl alic gap=smol flex-wrap>
    <?php foreach ($filter_map as $key => $f) : ?>
      <a href="<?= "$filter_base_url/$key" . $append_query_string ?>">
        <mchip has-icon=left outlined <?= $filter === $key || !array_key_exists($filter, $filter_map) ? "active" : "" ?>>
          <mi><?= $f["icon"] ?></mi>
          <p><?= $f["name"] ?></p>
        </mchip>
      </a>
    <?php endforeach; ?>
  </div>

  <?php

  /**
   * @var string
   */
  $sort_base_url = "$base_url/1";

  ?>

  <div fl alic gap=smol+>
    <p text no-word-wrap>Sortieren nach</p>
    <mselect outlined=darker size="mid" align="center" clickable mselect-type="input-visible" style="min-width:200px;">
      <mselect-presented>
        <div mselect-visible-value>
          <?= $sort_map[$sort] ?? $sort_map[$default_sort] ?>
        </div>
        <mi></mi>
      </mselect-presented>
      <mselect-dropdown>
        <div get-size>
          <div class="msd__inr">
            <?php foreach ($sort_map as $key => $s) : ?>
              <a href="<?= "$sort_base_url/$key/$filter" . $append_query_string ?>">
                <mselect-option mselect-input-value mselect-change-visible-value>
                  <p><?= $s ?></p>
                </mselect-option>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </mselect-dropdown>
      <input mselect-input type="hidden" name="type" value="bike">
    </mselect>

    <div fl alic gap=smoler>
      <?php if ($query) { ?>
        <a href="<?php echo "$base_url/1/$filter"; ?>" page sub has-tooltip=bottom>
          <div filled fl alic rounded=wide gap=smol style="padding-inline:24px 16px;height:60px;">
            <p text smolplus semibold><?php echo $query; ?></p>
            <mi stdplus slight>clear</mi>
          </div>
          <div ttooltip>
            Entfernen
          </div>
        </a>
      <?php } ?>

      <mbutton open-searcher material size=mid icon-only outlined=darker hoverable has-tooltip=bottom>
        <mi>search</mi>
        <div ttooltip>
          <p text std bold>Suchen</p>
        </div>
      </mbutton>
    </div>

    <div searcher>
      <form data-form="page-searcher">
        <div fl alic>
          <mi midler fl alic jucc>search</mi>
          <input std enter-submitable
            name=query
            type=text
            placeholder="<?= $filter_search_terms ?? "Suchen.." ?>"
            value="<?php echo $query; ?>" />
          <mbutton submit-closest material icon-only hoverable>
            <mi>east</mi>
          </mbutton>
        </div>
      </form>
    </div>
  </div>
</div>