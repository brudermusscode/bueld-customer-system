<?php

namespace Bruder\Controller\Repair;

use Bruder\Controller\Controller;
use Bruder\Model\Repair\RepairOrder;
use Bruder\Model\Repair\RepairOrderItem;

class OrderItemsController extends Controller
{
  /**
   * @return string
   */
  public function create()
  {
    return $this->success("<strong>Erstellt!</strong>");
  }

  /**
   * @return string
   */
  public function update()
  {

    /**
     * Validate post parameter.
     */
    $this->validate_params(
      ["id"],
      ["name", "price", "amount"]
    );

    /**
     * @var ?RepairOrder
     */
    $RepairOrder = RepairOrderItem::findOrReturn($this->params->id, "<strong>Dieses Item existiert nicht mehr!</strong>");

    /**
     * Order is already marked as done?
     */
    if ($RepairOrder->is_done())
      return $this->error("<strong>Dieser Auftrag ist bereits abgeschlossen.</strong> Es können keine Änderungen mehr vorgenommen werden.");

    return $RepairOrder->edit($this->params);
  }

  /**
   * @return string
   */
  public function delete()
  {
    return $this->success("<strong>Gelöscht!</strong>");
  }
}
