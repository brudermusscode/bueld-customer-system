<?php

namespace Bruder\Controller\Repair;

use Bruder\Application\Cookie;
use Bruder\Application\Logger;
use Bruder\Controller\Controller;
use Bruder\Model\Repair\RepairOrder;
use Bruder\Model\Repair\RepairType;
use Exception;

class TypesController extends Controller
{
  /**
   * @return string
   */
  public function update()
  {

    /**
     * Validate post parameter.
     */
    $this->validate_params(
      ["id", "name", "price"],
      ["initial_id"]
    );

    /**
     * User is authenticated?
     */
    $this->authorize(true, ["manage", "masterData"]);

    /**
     * @var ?RepairType
     */
    $Type = RepairType::findOrReturn($this->params->id, "<strong>Dieser Reparatur-Typ existiert nicht.</strong>");

    return $Type->edit($this->params);
  }

  /**
   * @return string
   */
  public function delete()
  {
    /**
     * Validate post parameter.
     */
    $this->validate_params(
      ["id"],
      []
    );

    /**
     * User is authenticated?
     */
    $this->authorize(true, ["manage", "masterData"]);

    /**
     * @var RepairType
     */
    $Type = RepairType::find($this->params->id);

    /**
     * Remove it!
     */
    $Type?->delete();

    return $this->success("<strong>Gelöscht!</strong>");
  }
}
