<?php

namespace Bruder\Model;

use Bruder\Bruder;
use Bruder\Model\Customer\CustomerObject;
use Bruder\Model\Repair\RepairOrder;

class Brand extends Bruder
{
  /**
   * @var array
   */
  protected $fillable = [
    "type",
    "name",
  ];

  /**
   * @return Brand
   */
  public function orders()
  {
    return $this->hasMany(RepairOrder::class);
  }

  /**
   * @return ?CustomerObject
   */
  public function customer_objects()
  {
    return $this->hasMany(CustomerObject::class);
  }
}
