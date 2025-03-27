<?php

use Bruder\Model\Repair\RepairOrder;
use Bruder\Model\Employee;

/**
 * @var string $base_url
 * @var RepairOrder $RepairOrder
 */

/**
 * @var string
 */
$page_title = "employee";

?>

<timeline posrel>
  <timeline-line></timeline-line>

  <?php

  /**
   * Include all steps previews for passed ones.
   */
  include dirname(__DIR__) . "/_progress_previous.php"; ?>

  <div id=activeObject pt42>
    <timeline-option active>
      <timeline-dot>
        <div class="circle-loader"></div>
        <mi></mi>
      </timeline-dot>

      <section fl fldircol gap>
        <div>
          <p text bold midplus>Wer bist du?</p>
          <p text>Wähle dich als ausführenden Mitarbeiter aus</p>
        </div>

        <mradio material size=wide fl flex-wrap gap=smoler unselect=false>

          <?php

          $Employees = Employee::all();

          foreach ($Employees as $Employee) :

          ?>

            <radio-option style="flex-basis:49%;max-width:49.4%;"
              <?= $RepairOrder->employee?->is($Employee) ? "active" : "" ?>
              submit-closest data-value=<?= $Employee->id ?> flone material outlined>
              <?= $Employee->firstname . " " . $Employee->lastname; ?>
            </radio-option>

          <?php endforeach; ?>

          <input radio-input type=hidden name=employee_id value="<?= $RepairOrder->employee?->id ?>" />
        </mradio>
      </section>
    </timeline-option>
  </div>

  <?php

  /**
   * Include all steps previews for upcoming ones.
   */
  include dirname(__DIR__) . "/_progress_next.php"; ?>

</timeline>