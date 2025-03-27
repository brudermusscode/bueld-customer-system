<timeline-heading fl alic jucsb gap=smol+>
  <p title>Reparatur-Auftrag</p>

  <?php if (isset($RepairOrder) && !$RepairOrder->in_repair() && !$RepairOrder->is_done()) : ?>
    <mbutton has-tooltip=bottom no-trans-delay data-action="repair:order:delete" data-id=<?= $RepairOrder->id ?> size=mid background=red color=light material>
      Abbrechen
      <div ttooltip>
        Löscht diesen Auftrag
      </div>
    </mbutton>
  <?php endif; ?>
</timeline-heading>