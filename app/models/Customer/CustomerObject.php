<?php

namespace Bruder\Model\Customer;

use Bruder\Application\Logger;
use Bruder\Bruder;
use Bruder\Model\Brand;
use Bruder\Model\Customer;
use Bruder\Model\Leasing\Leasing;
use Bruder\Model\Repair\RepairOrder;
use Bruder\Trait\HasObjectType;
use Exception;

class CustomerObject extends Bruder
{
  use HasObjectType;

  /**
   * @var array
   */
  protected $fillable = [
    "type",
    "customer_id",
    "brand_id",
    "object_unique_identifier",
  ];

  /**
   * @param object $params
   * @return string|self
   */
  public function new(object $params)
  {

    /**
     * @var Customer
     */
    $Customer = $params->Customer;

    /**
     * @var self
     */
    $Object = $Customer->objects()
      ->make();

    /**
     * Type is invalid?
     */
    if (!in_array($params->type, ["bike", "sewing"]))
      return $this->error("<strong>Ungültiger Typ!</strong>");

    /**
     * object brand visible is empty?
     */
    if (strlen(trim($params->object_brand_visible)) < 1)
      return $this->error("<strong>Wähle eine Marke & Modell aus.</strong>");

    /**
     * @var Brand|null
     */
    $Brand = null;

    /**
     * Check for the object_brand_id to be set.
     */
    if (!empty($params->object_brand_id)) {

      /**
       * @var ?Brand
       */
      $Brand = Brand::where("id", $params->object_brand_id)
        ->whereOr("name", $params->object_brand_visible)
        ->first();
    }

    /**
     * Check if the brand previously found (if one was found) is
     * the same name as the visible value and if not, search for
     * the new visible value as the brand.
     */
    if ($Brand?->name !== $params->object_brand_visible) {

      /**
       * @var ?Brand
       */
      $Brand = Brand::where([
        "name" => $params->object_brand_visible,
        "type" => $params->type,
      ])
        ->first();
    }

    /**
     * If we are here and no brand has been found, we should
     * create a new one with the given visible value.
     * @var Brand
     */
    $Brand ??= Brand::create([
      "name" => $params->object_brand_visible,
      "type" => $params->type,
    ]);

    /**
     * @var ?CustomerObject
     */
    $CustomerObject = $Customer->objects()

      /**
       * Search customer_objects for the object_unique_identifier
       * in combination with the type first.
       */
      ->when(
        !empty($params->object_unique_identifier),
        function ($q) use ($Brand, $params) {
          $q->where([
            "type" => $params->type,
            "brand_id" => $Brand->id,
            "object_unique_identifier" => $params->object_unique_identifier,
          ]);
        },

        /**
         * Or search for any object that has no unique id set but
         * the same brand.
         */
        function ($q) use ($params, $Brand) {
          $q->where([
            "type" => $params->type,
            "brand_id" => $Brand->id,
            "object_unique_identifier" => null,
          ]);
        }

        /**
       * If not found, search for an object that has the same
       * name as the given object_brand_visible value in
       * combination with the object type.
       */
        // function ($q) use ($Brand, $params) {
        //   $q->where([
        //     "type" => $Brand->type,
        //   ])
        //     ->whereHas("brand", function ($q2) use ($params) {
        //       $q2->where("name", $params->object_brand_visible);
        //     });
        // }
      )

      ->first();

    /**
     * Already exists?
     */
    if ($CustomerObject)
      return $this->error("<strong>Dieses Gerät existiert bereits für diesen Kunden.</strong>");

    /**
     * @var ?CustomerObject
     */
    $OtherCustomerObject = self::where([
      "object_unique_identifier" => $params->object_unique_identifier ?? "",
      "brand_id" => $Brand->id,
    ])
      ->whereNot("customer_id", $Customer->id)
      ->first();

    /**
     * Return an error, if the same unique id is set for
     * another customer with the same brand and object id.
     */
    if ($OtherCustomerObject)
      return $this->error("<strong>Diese Gerätenummer ist schon für einen anderen Kunden registriert.</strong> Vielleicht hast du dich vertippt oder lasse sie einfach frei.");

    /**
     * Set all params!
     */
    $Object->type = $params->type;
    $Object->object_unique_identifier = $params->object_unique_identifier ?: null;
    $Object->brand_id = $Brand->id;

    /**
     * begin a transaction.
     */
    $this->db_transaction();

    try {

      $Object->save();

      /**
       * Set the object for the order if one is set.
       */
      if (isset($params->order_id)) {
        RepairOrder::find($params->order_id)
          ?->update([
            /**
             * Set the object.
             */
            "customer_object_id" => $Object->id,

            /**
             * Unset leasing and brand.
             */
            "leasing_id" => null,
            "leasing_inspection_id" => null,
            "is_leasing" => null,
            "brand_id" => null,
          ]);
      }

      $this->db_commit();
    } catch (Exception $e) {

      Logger::to_file($e, "model_interaction_errors.log");

      $this->db_rollback();

      return $this->error("<strong>Es konnte kein neues Gerät erstellt werden.</strong> Schaue in die Error-Logs für mehr Informationen.");
    }

    return $Object->fresh();
  }

  /**
   * @return Customer
   */
  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }

  /**
   * @return RepairOrder
   */
  public function orders()
  {
    return $this->hasMany(RepairOrder::class);
  }

  /**
   * @return Brand
   */
  public function brand()
  {
    return $this->belongsTo(Brand::class);
  }

  /**
   * @return ?Leasing
   */
  public function leasing()
  {
    return $this->hasOne(Leasing::class);
  }
}
