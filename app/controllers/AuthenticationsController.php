<?php

namespace Bruder\Controller;

use Bruder\Controller\Controller;
use Bruder\Model\User\User;
use Bruder\Model\Session;
use Exception;

class AuthenticationsController extends Controller
{
  /**
   * @return string
   */
  public function create()
  {
    /**
     * Validate POST params.
     */
    $this->validate_params(
      ["username", "password"],
      ["permissions"]
    );

    /**
     * No User exist? This is for the first login. Here we want to
     * create the master user.
     */
    if (!User::latest()->first()) {

      /**
       * Add master-permissions to the params object.
       */
      $this->params->permissions = 1 << 15;

      /**
       * @var ?User
       */
      $User = (new User)->new($this->params);

      /**
       * Creation failed?
       */
      if (!($User instanceof User))
        return $this->error("<strong>Es konnte kein Master-User erstellt werden.</strong>");

      /**
       * Create a new session.
       */
      (new Session)->new((object) [
        "user_id" => $User->id
      ]);

      return $this->success("Master-User <strong>" . $this->params->username . "</strong> erstellt!");
    }

    /**
     * Verify login credetials.
     */
    return (new Session)->new($this->params);
  }
}
