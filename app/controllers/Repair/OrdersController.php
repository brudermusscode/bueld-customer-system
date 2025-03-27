<?php

namespace Bruder\Controller\Repair;

use Bruder\Application\Cookie;
use Bruder\Application\Logger;
use Bruder\Controller\Controller;
use Bruder\Model\Repair\RepairOrder;
use Exception;

class OrdersController extends Controller
{
  /**
   * @return string
   */
  public function create()
  {
    /**
     * Validate post parameter.
     */
    $this->validate_params(["type"], []);

    return (new RepairOrder)->new($this->params);
  }

  /**
   * @return string
   */
  public function update()
  {

    // pdie($_POST);

    /**
     * Validate post parameter.
     */
    $this->validate_params(
      ["id"],
      [
        "employee_id",
        "type",
        "status",
        "repair_ids",
        "repair_ids_visible",
        "object_unique_identifier",
        "customer_object_id",
        "object_brand_id",
        "object_brand_visible",
        "customer_id",
        "full_name",
        "is_company",
        "firstname",
        "lastname",
        "sex",
        "mail",
        "phone",
        "address",
        "address_line_1",
        "postcode_or_city",
        "is_leasing",
        "leasing_id",
        "leasing_company_id",
        "leasing_contract_id",
        "leasing_inspection_id",
        "customer_addons"
      ]
    );

    /**
     * @var ?RepairOrder
     */
    $RepairOrder = RepairOrder::find($this->params->id);

    /**
     * No Order found?
     */
    if (!$RepairOrder)
      return $this->error("<strong>Dieser Auftrag existiert nicht mehr!</strong>");

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
    /**
     * Validate post parameter.
     */
    $this->validate_params(["id"], []);

    /**
     * @var RepairOrder
     */
    $Order = RepairOrder::find($this->params->id);

    /**
     * Order doens't exist?
     */
    if (!$Order)
      return $this->error("<strong>Dieser Auftrag scheint nicht mehr zu existieren.</strong>");

    /**
     * Order is done or in repair already?
     */
    if ($Order->in_repair() || $Order->is_done())
      return $this->error(
        "<strong>Dieser Auftrag ist bereits aufgegeben.</strong> Du kannst ihn nicht mehr löschen."
      );

    try {
      $Order->delete();
    } catch (Exception $e) {
      /**
       * Log an error.
       */
      Logger::to_file($e, "model_interaction_errors.log");

      return $this->error("<strong>Konnte nicht löschen.</strong> Schaue in die Error-Logs für mehr Informationen.");
    }

    /**
     * Delete the cookie for this order id.
     */
    Cookie::delete("_current_order_id");

    return $this->success("<strong>Gelöscht!</strong>");
  }
}
