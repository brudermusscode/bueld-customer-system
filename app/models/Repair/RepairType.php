<?php

namespace Bruder\Model\Repair;

use Bruder\Bruder;
use Bruder\Model\Repair\RepairOrderItem;
use Bruder\Model\Repair\RepairCategory;
use Bruder\Utils\Str;

class RepairType extends Bruder
{
  /**
   * @var array
   */
  protected $fillable = [
    "type",
    "repair_category_id",
    "initial_id",
    "description",
    "time_unit",
    "price",
  ];

  /**
   * @return string
   */
  public function edit(object $params)
  {

    /**
     * * Price
     */
    if (isset($params->price)) {

      /**
       * Remove all whitespace.
       */
      $price = Str::strip_whitespace($params->price);

      /**
       * Replace all commas with dots.
       */
      $price = str_replace(",", ".", $price);

      /**
       * Price is not a float?
       */
      if (!is_float($price) && !is_numeric($price))
        return $this->error("<strong>Der Preis sollte so aussehen: 5 oder 5.00 oder 5,00</strong>");
    }

    /**
     * Update this cute boy!
     */
    $this->update([
      "description" => htmlspecialchars($params->name),
      "price" => $price ? $price : null,
    ]);

    /**
     * ? Success
     */
    return $this->success("<strong>Gespeichert!</strong>");
  }

  /**
   * @return Repair
   */
  public function repair()
  {
    return $this->belongsTo(RepairOrderItem::class);
  }

  /**
   * @return RepairOrder
   */
  public function repair_category()
  {
    return $this->belongsTo(RepairCategory::class);
  }

  /**
   * @return string
   */
  public function formatted_price()
  {
    return number_format($this->price ?? 0.00, 2, ",", ".");
  }

  /**
   * @return string
   */
  public function display_type()
  {
    return match ($this->type) {
      "sewing" => "Nähmaschine",
      "bike" => "Fahrrad",
      default => "Fahrrad",
    };
  }

  /**
   * @return string
   */
  public function display_type_icon()
  {
    return match ($this->type) {
      "sewing" => "communities",
      "bike" => "directions_bike",
      default => "directions_bike",
    };
  }
}
