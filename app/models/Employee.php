<?php

namespace Bruder\Model;

use Bruder\Bruder;
use Bruder\Model\Repair\RepairOrder;
use Bruder\Model\User\User;

class Employee extends Bruder
{
  /**
   * @var array
   */
  protected $fillable = [
    "initial_id",
    "firstname",
    "lastname",
  ];

  /**
   * @return RepairOrder
   */
  public function repair_orders()
  {
    return $this->hasMany(RepairOrder::class);
  }

  /**
   * @return string
   */
  public function full_name()
  {
    return $this->firstname . " " . $this->lastname;
  }

  /**
   * @return ?User
   */
  public function user()
  {
    return $this->hasOne(User::class);
  }
}
