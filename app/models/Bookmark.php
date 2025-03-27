<?php

namespace Bruder\Model;

use Bruder\Bruder;
use Bruder\Model\Customer;
use Bruder\Model\Repair\RepairOrder;

class Bookmark extends Bruder
{
  /**
   * @var array
   */
  protected $fillable = [
    "type",
    "reference_id",
  ];

  /**
   * @return Customer
   */
  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }

  /**
   * @return Customer
   */
  public function order()
  {
    return $this->belongsTo(RepairOrder::class);
  }

  /**
   * @param string $type
   * @param int $id
   * @return null|Customer|RepairOrder
   */
  public static function find_reference(string $type, int $id)
  {
    return match ($type) {
      "customer" => Customer::find($id),
      "order" => RepairOrder::find($id),
      default => null,
    };
  }
}
