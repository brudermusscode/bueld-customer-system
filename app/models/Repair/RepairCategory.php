<?php

namespace Bruder\Model\Repair;

use Bruder\Bruder;
use Bruder\Model\Repair\RepairType;

class RepairCategory extends Bruder
{
  /**
   * @var array
   */
  protected $fillable = [
    "initial_id",
    "description",
  ];

  /**
   * @return RepairType
   */
  public function repair_types()
  {
    return $this->hasMany(RepairType::class);
  }
}
