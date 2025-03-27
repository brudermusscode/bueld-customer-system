<?php

namespace Bruder\Controller;

use Bruder\Controller\Controller;
use Bruder\Model\User\User;
use Bruder\Model\Session;
use Exception;

class LeasingsController extends Controller
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
      ["customer_id", "contract_id", "company_id"],
      []
    );

    /**
     * @var ?Customer
     */

    /**
     * Verify login credetials.
     */
    return (new Session)->new($this->params);
  }
}
