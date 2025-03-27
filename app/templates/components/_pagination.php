<?php

/**
 * The count of all filtered objects.
 *
 * @var int
 */
$pcount = $FilteredCount ?? 0;

/**
 * @var ?string
 */
$append_query_string ??= null;

/**
 * @var int
 */
$ppage ??= 1;

/**
 * @var string
 */
$base_url ??= "";

/**
 * @var int
 */
$limit ??= 6;

/**
 * @var string
 */
$url_filter = ($sort ? "/$sort" : "") . ($filter ? "/$filter" : "") . $append_query_string;

/**
 * All pages count.
 */
$ppages = ceil($pcount / $limit);

/**
 * Count of pages to show left and right.
 */
$show_pages = 3;
$startPage = max(1, $ppage - $show_pages);
$endPage = min($ppage + $show_pages, $ppages);

?>

<pagination>

  <?php

  /**
   * * Back to the very first page.
   */
  if ($ppage > $show_pages + 1) : ?>
    <a href="<?php echo "$base_url/1" . $url_filter; ?>">
      <div has-tooltip=left>
        <mbutton material size=std icon-only hoverable rounded=smol outlined text midler>
          <p text mid bold>
            <i class=mi>first_page</i>
          </p>
        </mbutton>
        <div ttooltip>
          <p text std bold>First page</p>
        </div>
      </div>
    </a>
  <?php endif;

  /**
   * * Go one page back.
   */
  if ($ppage > 1) :

    /**
     * @var int
     */
    $previousPage = $ppage - 1; ?>

    <a href="<?php echo "$base_url/$previousPage" . $url_filter; ?>">
      <mbutton material size=std icon-only hoverable rounded=smol outlined text midler>
        <p text mid bold>
          <i class=mi>chevron_left</i>
        </p>
      </mbutton>
    </a>

  <?php endif;

  /**
   * * Pagination options.
   */
  for ($i = $startPage; $i <= $endPage; $i++) : ?>

    <?php if ($ppage === $i) : ?>
      <pagination-option active>
        <p><?php echo $i; ?></p>
      </pagination-option>
    <?php else : ?>
      <a href="<?php echo "$base_url/$i" . $url_filter; ?>">
        <pagination-option>
          <p><?php echo $i; ?></p>
        </pagination-option>
      </a>
    <?php endif; ?>

  <?php

  endfor;

  /**
   * * Go to next page.
   */
  if ($ppage < $ppages) :

    /**
     * @var int
     */
    $nextPage = $ppage + 1; ?>

    <a href="<?php echo "$base_url/$nextPage" . $url_filter; ?>">
      <mbutton material size=std icon-only hoverable rounded=smol outlined text midler>
        <p text mid bold>
          <i class=mi>chevron_right</i>
        </p>
      </mbutton>
    </a>

  <?php endif;

  /**
   * * Go to very last page.
   */
  if ($ppage < $ppages - $show_pages) : ?>
    <a href="<?php echo "$base_url/$ppages" . $url_filter; ?>">
      <div has-tooltip=right>
        <mbutton material size=std icon-only hoverable rounded=smol outlined text midler>
          <p text mid bold>
            <i class=mi>last_page</i>
          </p>
        </mbutton>
        <div ttooltip>
          <p text std bold>Last page</p>
        </div>
      </div>
    </a>
  <?php endif; ?>

</pagination>