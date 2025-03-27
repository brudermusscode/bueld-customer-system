<?php

namespace Bruder\Authorization;

use Bruder\Enum\UserPermission;

interface PermissionGuardInterface
{
  /**
   * Evaluates, if the resource that this function was called on
   * "can" execute certain functionalities, which are defined by
   * "Guards" in the parent folder of this file.
   *
   * @param string $interaction
   * @param string $section
   * @param mixed $Permission
   * @return bool
   */
  public static function can(string $interaction, string $section, UserPermission $Permission);
}
