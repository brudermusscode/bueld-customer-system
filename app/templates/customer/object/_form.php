<div fl fldircol gap=smol>
  <div>
    <p text bold>Marke & Modell</p>
    <p text smolplus slight>Muss ausgefüllt sein</p>
  </div>
  <div input material has-icon has-search>
    <mi input-type-icon>brand_family</mi>
    <input
      name=object_brand_visible
      tabindex=1
      data-request="brand:search"
      type="text"
      required
      autofocus
      autocomplete="off"
      enter-submitable
      value="<?= $Brand?->name ?? $Object?->brand->name ?? "" ?>"
      placeholder="<?= ($RepairOrder?->type ?? $type ?? "bike") === "sewing" ? "Brother" : "Gazelle Aroyo C8" ?>" />
    <input type=hidden name="object_brand_id" value="<?= $Brand?->id ?? "" ?>" />
    <div search></div>
  </div>
</div>

<div fl fldircol gap=smol>
  <div>
    <p text bold>
      <?= ($RepairOrder?->type ?? $type ?? "bike") === "bike"
        ? "Rahmen-Nummer"
        : "Maschinen-Nummer"
      ?>
    </p>
    <p text slight smolplus>Kann frei gelassen werden</p>
  </div>
  <div input material has-icon>
    <mi input-type-icon>tag</mi>
    <input
      name=object_unique_identifier
      tabindex=2
      type="text"
      autocomplete="off"
      enter-submitable
      value="<?= $Object?->object_unique_identifier ?? "" ?>"
      placeholder="123456789" />
  </div>
</div>