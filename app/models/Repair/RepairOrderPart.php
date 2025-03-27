<?php

namespace Bruder\Model\Repair;

use Bruder\Bruder;
use Bruder\Model\Repair\RepairOrder;
use Bruder\Model\Repair\RepairPart;

class RepairOrderPart extends Bruder
{
  /**
   * @var array
   */
  protected $fillable = [
    "repair_order_id",
    "repair_part_id",
    "custom_field",
    "amount",
  ];

  /**
   * @return RepairOrder
   */
  public function order()
  {
    return $this->belongsTo(RepairOrder::class, "repair_order_id");
  }

  /**
   * @return RepairPart
   */
  public function part()
  {
    return $this->belongsTo(RepairPart::class, "repair_part_id");
  }

  /**
   * @return string
   */
  public function name()
  {
    return $this->custom_field ?? $this->part->name ?? "Kein Name verfügbar";
  }

  /**
   * @return float
   */
  public function base_price()
  {
    return ($this->part?->price ?? 0.00) * $this->amount;
  }

  /**
   * @return string
   */
  public function formatted_price()
  {
    return number_format($this->base_price(), 2, ",", ".");
  }
}
