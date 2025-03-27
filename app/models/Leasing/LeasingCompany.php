<?php

namespace Bruder\Model\Leasing;

use Bruder\Bruder;

class LeasingCompany extends Bruder
{
  /**
   * @var array
   */
  protected $fillable = [
    "name",
    "logo_path",
    "requires_inspection_id",
  ];

  /**
   * @return Leasing
   */
  public function leasings()
  {
    return $this->hasMany(Leasing::class);
  }
}
