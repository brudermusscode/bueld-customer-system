<?php

namespace Bruder\Controller\Customer;

use Bruder\Controller\Controller;
use Bruder\Model\User\User;
use Bruder\Model\Customer;
use Bruder\Model\Customer\CustomerObject;
use Bruder\Model\Session;
use Exception;

class ObjectsController extends Controller
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
      ["customer_id", "type", "object_brand_visible", "object_brand_id"],
      ["object_unique_identifier", "order_id"]
    );

    /**
     * @var ?Customer
     */
    $this->params->Customer =
      Customer::findOrReturn($this->params->customer_id, "<strong>Dieser Kunde existiert nicht mehr.</strong>");

    /**
     * @var CustomerObject|string
     */
    $Object = (new CustomerObject)->new($this->params);

    if (($Object instanceof CustomerObject))
      return $this->success("<strong>Gerät hinzugefügt!</strong>");
    else
      return $Object;
  }
}
