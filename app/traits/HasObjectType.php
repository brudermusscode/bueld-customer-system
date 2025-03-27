<?php

namespace Bruder\Trait;

trait HasObjectType
{

  /**
   * @return string
   */
  public function display_type()
  {
    return match ($this->type) {
      "sewing" => "Nähmaschine",
      "bike" => "Fahrrad",
      default => "Fahrrad",
    };
  }

  /**
   * @return string
   */
  public function display_type_icon()
  {
    return match ($this->type) {
      "sewing" => "communities",
      "bike" => "pedal_bike",
      default => "pedal_bike",
    };
  }
}
