<?php

namespace Bruder\Model\Repair;

use Bruder\Bruder;
use Bruder\Model\Repair\RepairOrderItem;

class RepairPart extends Bruder
{
  /**
   * @var array
   */
  protected $fillable = [
    "type",
    "initial_id",
    "article_id",
    "name",
    "price",
  ];

  /**
   * @return Repair
   */
  public function repair()
  {
    return $this->belongsTo(RepairOrderItem::class);
  }

  /**
   * @return string
   */
  public function formatted_price()
  {
    return number_format($this->price * $this->amount ?? 0.00, 2, ",", ".");
  }
}
