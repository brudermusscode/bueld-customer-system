<?php

namespace Bruder\Enum;

enum UserPermission: int
{
  case MASTER = 1 << 15;
  case EMPLOYEE = 1 << 1;
  case RESTRICTED = 1 << 0;
  case GUEST = 0 << 0;

  /**
   * Get the permission as an array with name and icon.
   *
   * @return object
   */
  public function get_display()
  {
    return (object) match ($this) {
      UserPermission::MASTER => [
        "name"  => "Developer",
        "icon" => "php",
      ],
      UserPermission::EMPLOYEE => [
        "name"  => "Member",
        "icon" => "egg",
      ],
      UserPermission::RESTRICTED => [
        "name"  => "Applied",
        "icon" => "egg",
      ],
      UserPermission::GUEST => [
        "name"  => "Guest",
        "icon" => "",
      ],
    };
  }

  /**
   * Evaluates the Privileges from a given int.
   *
   * @param int $bits
   * @return ?object
   */
  public static function by_bits(int $bits)
  {
    $privileges = [];

    foreach (UserPermission::cases() as $privilegeName => $privilegeFlag) {
      $display = $privilegeFlag->get_display();

      if (($bits & $privilegeFlag->value) !== 0) {
        $privileges[] = (object) [
          "privilege" => $privilegeFlag,
          "bits" => $privilegeName,
          "name" => $display->name,
          "icon" => $display->icon,
        ];
      }
    }

    return $privileges ? $privileges : null;
  }
}
