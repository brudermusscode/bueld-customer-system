<?php

namespace Bruder\Model\Leasing;

use Bruder\Bruder;
use Bruder\Model\Customer;
use Bruder\Model\Customer\CustomerObject;
use Bruder\Model\Repair\RepairOrder;

class Leasing extends Bruder
{
  /**
   * @var array
   */
  protected $fillable = [
    "customer_id",
    "customer_object_id",
    "leasing_company_id",
    "external_customer_id",
    "contract_id",
    "duration",
    "status",
    "status_reason",
    "bought_at",
    "contract_begins_at",
    "exceptionally_ends_at",
    "ends_at",
    "adopted_at",
  ];

  /**
   * @return LeasingCompany
   */
  public function company()
  {
    return $this->belongsTo(LeasingCompany::class, "leasing_company_id", "id");
  }

  /**
   * @return Customer
   */
  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }

  /**
   * @return RepairOrder
   */
  public function orders()
  {
    return $this->hasMany(RepairOrder::class);
  }

  /**
   * @return CustomerObject
   */
  public function customer_object()
  {
    return $this->belongsTo(CustomerObject::class);
  }
}
