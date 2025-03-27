<?php

namespace Bruder\Model\User;

use Bruder\Bruder;
use Bruder\Model\Employee;
use Bruder\Model\Session;
use Bruder\Trait\HasPermissions;
use Illuminate\Support\Facades\Password;

class User extends Bruder
{
  use HasPermissions;

  /**
   * @var array
   */
  protected $fillable = [
    "username",
    "password_encrypted",
    "permissions",
    "employee_id",
  ];

  /**
   * @param object $params
   * @return User
   */
  public function new(object $params)
  {

    /**
     * Create a md5 hash of the password.
     * @var string
     */
    $md5_password = md5($params->password);

    /**
     * Encrypt the md5 hash.
     * @var string
     */
    $encrypted_password = password_hash($md5_password, PASSWORD_ARGON2ID);

    /**
     * Create a new user!
     */
    return self::create([
      "permissions" => $params->permissions ?? 3,
      "password_encrypted" => $encrypted_password,
      "username" => $params->username,
    ]);
  }

  /**
   * @return Session
   */
  public function sessions()
  {
    return $this->hasMany(Session::class);
  }

  /**
   * @return ?Employee
   */
  public function employee()
  {
    return $this->belongsTo(Employee::class);
  }

  /**
   * @return ?User
   */
  public static function verify_login(string $username, string $password)
  {
    /**
     * @var ?User
     */
    $User = User::where("username", $username)
      ->first();

    /**
     * @var bool
     */
    $password_verify = password_verify(md5($password), $User?->password_encrypted);

    return $password_verify ? $User : null;
  }
}
