<?php

namespace Bruder\Authorization;

use Bruder\Authorization\PermissionGuardInterface;
use Bruder\Exception\AuthorizationException;
use Bruder\Enum\UserPermission;

class UserGuard implements PermissionGuardInterface
{

  /**
   * Can interact with users, like restricting their access or
   * manipulate their public profile.
   *
   * @var UserPermission[]
   */
  const GROUP_MANAGE_MASTER_DATA = [
    UserPermission::MASTER,
  ];

  /**
   * Can interact with employees and see their data.
   *
   * @var UserPermission[]
   */
  const GROUP_MANAGE_EMPLOYEES = [
    UserPermission::MASTER,
  ];

  /**
   * @param string $interaction
   * @param string $section
   * @param UserPermission $Permission
   * @return bool
   */
  public static function can(string $interaction, string $resource, UserPermission $Permission)
  {
    /**
     *
     * Developer can do all. The boss.
     */
    if ($Permission === UserPermission::MASTER)
      return true;

    /**
     * Match the given Permission to the interaction  permitted groups.
     */
    return match ("{$interaction}_{$resource}") {
      "manage_masterData" => in_array($Permission, self::GROUP_MANAGE_MASTER_DATA),
      "manage_employees" => in_array($Permission, self::GROUP_MANAGE_EMPLOYEES),

      /**
       * Log new exception for illegal interaction.
       */
      default => self::return_with_error("Invalid resource '$resource' for interaction '$interaction'"),
    };
  }

  /**
   * @param string $message
   * @return false
   */
  private static function return_with_error(string $message)
  {
    new AuthorizationException($message);
    return false;
  }
}
