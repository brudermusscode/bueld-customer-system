<?php

namespace Bruder\Model\Repair;

use Bruder\Bruder;
use Bruder\Model\Repair\RepairOrder;
use Bruder\Model\Repair\RepairType;

class RepairOrderItem extends Bruder
{
  /**
   * @var array
   */
  protected $fillable = [
    "repair_order_id",
    "repair_type_id",
    "custom_field",
  ];

  /**
   * @return RepairOrder
   */
  public function repair_order()
  {
    return $this->belongsTo(RepairOrder::class);
  }

  /**
   * @return RepairType
   */
  public function repair_type()
  {
    return $this->belongsTo(RepairType::class);
  }

  /**
   * @return RepairType
   */
  public function type()
  {
    return $this->belongsTo(RepairType::class, "repair_type_id", "id");
  }

  /**
   * @return string
   */
  public function name()
  {
    return $this->custom_field ?? $this->type->description ?? "Keine Beschreibung verfügbar";
  }

  /**
   * Adds loan increase.
   *
   * @return string
   */
  public function formatted_price()
  {
    $price = ($this->type->price ?? 0.00) * $this->repair_order->loan_increase_factor();

    return number_format($price, 2, ",", ".") ?? "0.00";
  }
}
