<?php

namespace Bruder\Trait;

use Bruder\Enum\UserPermission;
use Bruder\Authorization\UserGuard;

trait HasPermissions
{
  /**
   * @param string $section - What section the permission are
   *                          needed for
   * @param string $resource - What resource/model to interact with.
   * @return bool
   */
  public function can(string $section, string $resource)
  {

    /**
     * @var UserPermission[]
     */
    $Permissions = $this->permissions();

    /**
     * Iterate through all resource's permissions and check
     * against the necessary ones coming from the Guard class.
     */
    foreach ($Permissions as $Permission)
      return UserGuard::can($section, $resource, $Permission);

    return false;
  }

  /**
   * Evaluates all permissions set to a resource.
   *
   * @return UserPermission[]
   */
  public function permissions()
  {
    $result = [];

    foreach (UserPermission::cases() as $Permission) {
      if (($this->permissions & $Permission->value) === $Permission->value)
        $result[] = $Permission;
    }

    return $result;
  }

  /**
   * @param UserPermission $Permission
   * @return bool
   */
  public function has_minimum_permission(UserPermission $Permission)
  {
    return $this->permissions >= $Permission->value;
  }

  /**
   * @return UserPermission
   */
  public function highest_permission()
  {
    return $this->permissions()[0];
  }

  /**
   * @return string
   */
  public function highest_permission_name()
  {
    return $this->highest_permission()
      ->get_display()
      ->name;
  }
}
