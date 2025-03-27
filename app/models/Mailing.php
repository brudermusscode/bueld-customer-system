<?php

namespace Bruder\Model;

use Bruder\Bruder;
use Bruder\Model\Customer;

class Mailing extends Bruder
{

  /**
   * @var array
   */
  protected $fillable = [
    "customer_id",
    "mail",
    "token",
    "subject",
    "template",
  ];

  /**
   * @return ?Customer
   */
  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }
}
