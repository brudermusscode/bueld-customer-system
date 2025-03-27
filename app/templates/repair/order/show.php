<?php

use Bruder\Model\Repair\RepairOrder;
use Bruder\Model\Customer;

/**
 * @var int
 */
$id = filter_var($GLOBALS["route_param_id"], FILTER_VALIDATE_INT);

/**
 * @var ?string
 */
$ref = filter_input(INPUT_GET, "ref", FILTER_DEFAULT);

/**
 * @var RepairOrder
 */
$RepairOrder = RepairOrder::find($id);

if (!$RepairOrder) :
  include UNAVAILABLE;
else :

  /**
   * @var Customer
   */
  $Customer = $RepairOrder->customer;

  /**
   * @var ?Brand
   */
  $Brand = $RepairOrder->brand;

  /**
   * @var ?CustomerObject
   */
  $Object = $RepairOrder->customer_object;

  /**
   * @var ?Leasing
   */
  $Leasing = $RepairOrder->leasing;

  /**
   * @var bool
   */
  $editable = $RepairOrder->status === "REPAIR";

?>

  <div content-width=smol+ fl fldircol content-gap center-content flex-wrap pv42>

    <div fl fldircol gap=smol+>
      <div fl fldircol gap=smol+ background=nav rounded=wide pv42 ph52>
        <div fl fldircol gap=smol+>
          <div>
            <p text bold wide>Reparatur-Auftrag</p>
            <p text>Referenznr. <?= $RepairOrder->reference_id ?></p>
          </div>

          <div fl alic jucsb gap=smoler>
            <mchip outlined tag has-icon=left color=dynamic>
              <mi midler><?= $RepairOrder->display_type_icon() ?></mi>
              <?= $RepairOrder->display_type() ?>
            </mchip>
            <mchip outlined tag has-icon=left <?= $RepairOrder->display_status_color() ?>>
              <mi midler><?= $RepairOrder->display_status_icon() ?></mi>
              <?= $RepairOrder->display_status() ?>
            </mchip>
          </div>
        </div>
      </div>

      <div fl jucsb alic>
        <div></div>

        <div fl gap=smol>
          <form data-form="document:get">
            <input type=hidden name=id value=<?= $RepairOrder->id ?> />
            <input type=hidden name=type value=confirmation />
            <mbutton
              data-action="document:get:order-confirmation" submit-closest size=mid outlined material has-icon=left>
              <mi>file_save</mi>
              Auftrag anzeigen
            </mbutton>
          </form>

          <?php if ($RepairOrder->is_done()) : ?>
            <form data-form="document:get">
              <input type=hidden name=id value=<?= $RepairOrder->id ?> />
              <input type=hidden name=type value=invoice />
              <mbutton submit-closest size=mid outlined material has-icon=left>
                <mi>file_save</mi>
                Rechnung anzeigen
              </mbutton>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <?php

    /**
     * @var string
     */
    $base_url = "/repair/order/edit/$RepairOrder->id";

    /**
     * @var bool
     */
    $is_overview = true;

    ?>


    <?php if ($RepairOrder->status === "REPAIR") : ?>
      <tipp-box rounded background=light-green color=dark-green>
        <mi>lightbulb</mi>
        Klicke auf eine Box unten um die Daten darin zu bearbeiten. Der Auftrag kann so lange bearbeitet werden, wie er in Reparatur ist.
      </tipp-box>
    <?php endif; ?>

    <timeline fl fldircol gap=mid mb62>
      <timeline-line></timeline-line>

      <?php include __DIR__ . "/_progress_previous.php"; ?>
    </timeline>

  </div>

  <?php if ($ref === "startedRepairing") : ?>
    <script type="text/javascript" defer>
      $(function() {
        $(document).find('[data-action="document:get:order-confirmation"]')
          .click();
      });
    </script>
  <?php endif; ?>

<?php

endif;
