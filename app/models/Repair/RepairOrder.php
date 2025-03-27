<?php

namespace Bruder\Model\Repair;

use Bruder\Application\Cookie;
use Bruder\Bruder;
use Bruder\Application\Logger;
use Bruder\Model\Repair\RepairOrderItem;
use Bruder\Model\Leasing\LeasingCompany;
use Bruder\Model\Customer;
use Bruder\Model\Employee;
use Bruder\Model\Bookmark;
use Bruder\Model\Brand;
use Bruder\Model\Customer\CustomerObject;
use Bruder\Model\Leasing\Leasing;
use Bruder\Trait\HasObjectType;
use Bruder\Utils\PDF;
use Bruder\Utils\Utils;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Exception;

class RepairOrder extends Bruder
{
  use HasObjectType;

  /**
   * @var array
   */
  protected $fillable = [
    "status",
    "reference_id",
    "customer_id",
    "employee_id",
    "type",
    "customer_object_id",
    "is_leasing",
    "leasing_id",
    "leasing_inspection_id",
  ];

  /**
   * @var array
   */
  public static $status = [
    "STARTED", // The process has begun.
    "CUSTOMER", // Customer has been added.
    "REPAIR", // Object is in repair.
    "DONE", // All done!
  ];

  /**
   * @var array
   */
  public static $types = ["bike", "sewing"];

  /**
   * @var int
   */
  public static $ust_percent = 19;

  /**
   * @var int
   */
  protected static $leasing_surcharge_percent = 10;

  /**
   * @param object $params
   * @return string
   */
  public function new(object $params)
  {
    /**
     * @var self
     */
    $Order = self::make();

    /**
     * Type is invalid?
     */
    if (!in_array($params->type, self::$types)) {
      return $this->error(
        "<strong>Wähle einen gültigen Reparatur-Typ aus!</strong>"
      );
    }

    /**
     * Set the type.
     */
    $Order->type = $params->type;

    /**
     * @var string
     */
    $Order->reference_id = Utils::random_numeric_token(6);
    while (
      RepairOrder::where("reference_id", $Order->reference_id)->exists()
    ) {
      $Order->reference_id = Utils::random_numeric_token(6);
    }

    try {
      /**
       * Save it!
       */
      $Order->save();

      /**
       * Commit!
       */
      $this->db_commit();
    } catch (\Exception $e) {
      /**
       * Log error to file.
       */
      Logger::to_file($e, "model_interaction_errors.log");

      /**
       * Rollback all database transaction changes.
       */
      $this->db_rollback();

      return $this->error(
        "<strong>Ein Fehler ist aufgetreten.</strong> Frag Justin was los ist."
      );
    }

    /**
     * Set a cookie for the current repair order, so we can give the
     * user the option to come back to it as long as it is not done yet.
     */
    Cookie::set(
      "_current_order_id",
      $Order->id,
      "+1 week",
      secure: false,
      samesite: "Lax"
    );

    return $this->success(
      "<strong>Auftrag mit der Nummer #$Order->reference_id erstellt!</strong>",
      ["order_id" => $Order->id]
    );
  }

  /**
   * @param object $params
   * @return string
   */
  public function edit(object $params)
  {
    // TODO: Orders w/o object brand?
    // TODO: Orders w/o any items?

    /**
     * @var ?Leasing
     */
    $Leasing = null;

    /**
     * * Status
     *
     * This will finish the order and exit after saving. Nothing else
     * will happen if the status is set.
     */
    if (isset($params->status)) {

      /**
       * Invalid status set?
       */
      if (!in_array($params->status, self::$status)) {
        return $this->error(
          "<strong>Ungültiger Status für das Update des Auftrags angefragt.</strong>"
        );
      }

      /**
       * Status => REPAIR - this will generate the order paper
       * for the customer.
       */
      if ($params->status === "REPAIR" && $this->status !== "REPAIR") {

        /**
         * Anything missing before submission?
         */
        if ($this->submit_error())
          return $this->error(
            "<strong>Der Auftrag kann erst aufgegeben werden, wenn alle Felder ausgefüllt sind.</strong> Gehe zu den einzelnen Schritten und überprüfe deine Eingaben."
          );

        /**
         * Set the status.
         */
        $this->status = $params->status;

        /**
         * Save it!
         */
        $this->save();

        /**
         * Generate the paper for order confirmation.
         */
        $generate_paper = $this->generate_confirmation();

        /**
         * Generation failed?
         */
        if (!$generate_paper->status)
          return $generate_paper;

        /**
         * Send out customer confirmation mail.
         */
        // TODO: Attach the order confirmation to the email.
        $this->customer->send_order_confirmation($this);

        /**
         * Delete the cookie for this order id.
         */
        Cookie::delete("_current_order_id");

        /**
         * ? Success
         */
        return $this->success(
          "<strong>Der Auftrag wurde erfolgreich entgegengenommen!</strong>"
        );
      }

      /**
       * Status => DONE - this will generate the invoice for the customer.
       */
      elseif ($params->status === "DONE") {

        /**
         * Anything missing before submission?
         */
        if ($this->submit_error())
          return $this->error(
            "<strong>Der Auftrag kann erst abgeschlossen werden, wenn alle Felder ausgefüllt sind.</strong> Gehe zu den einzelnen Schritten und überprüfe deine Eingaben."
          );

        /**
         * Set the status.
         */
        $this->status = $params->status;

        /**
         * Generate the paper for order confirmation.
         */
        $generate_paper = $this->generate_invoice();

        /**
         * Generation failed?
         */
        if (!$generate_paper->status)
          return $generate_paper;

        /**
         * Send out customer mail with invoice attached.
         */
        // TODO: Send order invoice mail for customer on order finish.

      } else
        return $this->success("<strong>Auftrag bearbeitet!</strong>", data: ["status" => "REPAIR"]);
    }

    /**
     * * Employee
     */
    if (isset($params->employee_id)) {
      /**
       * @var ?Employee
       */
      $Employee = Employee::find($params->employee_id);

      /**
       * Employee does't exist?
       */
      if (!$Employee) {
        return $this->error(
          "<strong>Dieser Mitarbeiter existiert nicht.</strong> Wähle einen anderen aus!"
        );
      }

      /**
       * Set the id!
       */
      $this->employee_id = $params->employee_id;
    }

    /**
     * * Customer
     *
     * This will only fire if the customer_id is set which is in
     * any case of an existing customer and a new one.
     */
    if (isset($params->customer_id)) {

      /**
       * Nothing is set by now?
       */
      if (
        empty($params->customer_id) &&
        empty($params->sex) &&
        empty($params->full_name)
      )
        return $this->error(
          "<strong>Wähle einen Kunden oder erstelle einen neuen!</strong>"
        );

      /**
       * Customer id is not empty, so a customer should have been chosen.
       */
      if (!empty($params->customer_id)) {

        /**
         * @var ?Customer
         */
        $Customer = Customer::find($params->customer_id);

        /**
         * Customer doesn't exist?
         */
        if (!$Customer) {
          return $this->error(
            "<strong>Dieser Kunde existiert nicht.</strong> Erstelle einen neuen."
          );
        }

        /**
         * Update the customer.
         * @var object
         */
        $UpdateCustomer = json_decode(
          $Customer->edit(
            (object) [
              "mail" => $params->mail ?? null,
              "phone" => $params->phone ?? null,
            ]
          )
        );

        /**
         * Updateing failed?
         */
        if (!$UpdateCustomer->status) {
          return json_encode($UpdateCustomer);
        }

        /**
         * It's a new customer.
         */
      } else {

        /**
         * @var Customer|string
         */
        $Customer = (new Customer())->new($params);

        /**
         * Creation failed?
         */
        if (!($Customer instanceof Customer))
          exit($Customer);

        /**
         * Set the param to the newly created customer's id.
         */
        $params->customer_id = $Customer->id;
      }

      /**
       * Set the customer_id!
       */
      $this->customer_id = $params->customer_id;

      /**
       * Remove any previously added object and leasing if the
       * customer changed.
       */
      $this->reset();
    }

    /**
     * * Object
     *
     * In the best case, there is an customer_object_id set which
     * references a chosen object of a customer, that has been
     * added through a previously submitted order.
     */
    if (isset($params->customer_object_id)) {
      /**
       * No customer has been chosen by now?
       */
      if (!$this->customer) {
        return $this->error(
          "<strong>Wähle zuerst einen Kunden aus.</strong>"
        );
      }

      /**
       * @var ?CustomerObject
       */
      $Object = CustomerObject::findOrReturn(
        $params->customer_object_id,
        "<strong>Dieses Objekt existiert nicht, wähle ein anderes oder erstelle ein neues.</strong>"
      );

      /**
       * Set the object to this order!
       */
      $this->customer_object_id = $Object->id;

      /**
       * @var ?Leasing
       */
      $Leasing = $Object->leasing;

      /**
       * If a leasing exists, set all params automatically.
       *
       * Inspection ID will be set afterwards when accessing the
       * step for Leasing.
       */
      $this->leasing_id = $Leasing?->id;
      $this->is_leasing = $Leasing ? !false : null;
      $this->leasing_inspection_id = null;
    }

    /**
     * * Brand
     *
     * If no customer object id was sent, we want to check for a
     * brand that has been chosen to be added to this order. There
     * has to be either a cusomter_object or a brand set.
     * Otherwise it's incomplete.
     */
    elseif (isset($params->object_brand_visible)) {
      /**
       * No customer has been chosen by now?
       */
      if (!$this->customer) {
        return $this->error(
          "<strong>Wähle zuerst einen Kunden aus.</strong>"
        );
      }

      /**
       * object brand visible is empty?
       */
      if (strlen(trim($params->object_brand_visible)) < 1) {
        return $this->error(
          "<strong>Wähle eine Marke & Modell aus.</strong>"
        );
      }

      /**
       * Check for the object_brand_id to be set.
       */
      if (isset($params->object_brand_id)) {
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
        $Brand = Brand::where(
          "name",
          $params->object_brand_visible
        )->first();
      }

      /**
       * If we are here and no brand has been found, we should
       * create a new one with the given visible value.
       * @var Brand
       */
      $Brand ??= Brand::create([
        "name" => $params->object_brand_visible,
        "type" => $this->type,
      ]);

      // pdie($Brand->id, $this->customer_id, $params->object_unique_identifier);

      /**
       * @var ?CustomerObject
       */
      $CustomerObject = $this->customer
        ->objects()

        /**
         * Search customer_objects for the object_unique_identifier
         * in combination with the type first.
         */
        ->when(
          isset($params->object_unique_identifier),
          function ($q) use ($Brand, $params) {
            $q->where([
              "type" => $Brand->type,
              "brand_id" => $Brand->id,
              "object_unique_identifier" =>
              $params->object_unique_identifier,
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
       * @var ?CustomerObject
       */
      $OtherCustomerObject = CustomerObject::where([
        "object_unique_identifier" => $params->object_unique_identifier,
        "brand_id" => $Brand->id,
      ])
        ->whereNot("customer_id", $this->customer->id)
        ->first();

      /**
       * Return an error, if the same unique id is set for
       * another customer with the same brand and object id.
       */
      if ($OtherCustomerObject) {
        return $this->error(
          "<strong>Diese Gerätenummer ist schon für einen anderen Kunden registriert.</strong> Vielleicht hast du dich vertippt oder lasse sie einfach frei."
        );
      }

      /**
       * Create a new customer object if no one was found before
       * but the unique identifier actually is set. This means,
       * it's probably a new object the customer came in the store with.
       */
      if (!$CustomerObject && !empty($params->object_unique_identifier)) {
        $CustomerObject = $this->customer->objects()->create([
          "type" => $this->type,
          "object_unique_identifier" => $params->object_unique_identifier
            ? $params->object_unique_identifier
            : null,
          "brand_id" => $Brand->id,
        ]);
      }

      /**
       * Set the customer object if one exists!
       */
      $this->customer_object_id = $CustomerObject?->id;

      /**
       * If no CustomerObject is yet to be found on this order,
       * set the brand since either a brand or a customer object
       * but never both should be set.
       */
      if (!$CustomerObject) {
        $this->brand_id = $Brand?->id;
      }
    }
    /**
     * Brand is set but it's empty.
     */
    elseif (
      isset($params->object_brand_visible) &&
      strlen(trim($params->object_brand_visible)) < 1
    ) {
      /**
       * Set brand and customer object to nothing.
       */
      $this->reset();
    }

    /**
     * * Leasing
     */
    if (isset($params->is_leasing) && $params->is_leasing === "yes") {
      /**
       * No customer has been chosen by now?
       */
      if (!$this->customer) {
        return $this->error(
          "<strong>Wähle zuerst einen Kunden aus.</strong>"
        );
      }

      /**
       * Invalid type for leasing?
       */
      if ($this->type !== "bike") {
        return $this->error(
          "<strong>Leasing kann für dieses Gerät nicht hinzugefügt werden.</strong>"
        );
      }

      /**
       * No object is set by now?
       */
      if (!$this->customer_object) {
        return $this->error(
          "<strong>Wähle ein Fahrrad, bevor du Leasing hinzufügst.</strong>"
        );
      }

      /**
       * Leasing id set - A previously added leasing has been
       * chosen and the employee tries to add it to the order.
       */
      if (isset($params->leasing_id)) {
        /**
         * @var ?Leasing
         */
        $Leasing = $this->customer->leasings->find($params->leasing_id);

        /**
         * No leasing found?
         */
        if (!$Leasing) {
          return $this->error(
            "<strong>Wähle einen Leasing-Vertrag aus oder erstelle einen neuen.</strong>"
          );
        }

        /**
         * @var LeasingCompany
         */
        $LeasingCompany = $Leasing->company;

        /**
         * No leasing id was set so no previously added leasing
         * contract has been chosen. We want to create a new one.
         */
      } else {
        /**
         * no contract id set?
         */
        if (empty($params->leasing_contract_id)) {
          return $this->error("<strong>Du musst eine Vertragsnummer angeben.</strong>");
        }

        /**
         * No company set?
         */
        if (empty($params->leasing_company_id)) {
          return $this->error(
            "<strong>Du musst ein Leasing-Unternehmen auswählen.</strong>"
          );
        }

        /**
         * @var ?LeasingCompany
         */
        $LeasingCompany = LeasingCompany::findOrReturn(
          $params->leasing_company_id,
          "<strong>Das Leasing-Unternehmen existiert nicht.</strong> Wähle ein anderes aus!"
        );

        /**
         * @var ?Leasing
         */
        $Leasing = Leasing::where([
          "contract_id" => $params->leasing_contract_id,
          "leasing_company_id" => $LeasingCompany->id,
        ])->first();

        /**
         * Leasing with same id and company exist already and
         * doesn't belong to this customer?
         */
        if ($Leasing) {
          return $this->error(
            "<strong>Dieser Leasingvertrag ist bereits bei einem Kunden hinterlegt.</strong>"
          );
        }
        /**
         * Leasing doesn't exist, create a new one.
         */
        else {
          $Leasing = Leasing::make([
            "customer_id" => $this->customer?->id,
            "contract_id" => $params->leasing_contract_id,
            "leasing_company_id" => $LeasingCompany->id,
            "customer_object_id" => $this->customer_object->id,
          ]);
        }
      }

      /**
       * No leasing company set by now? (should actually never
       * happen D:)
       */
      if (!$LeasingCompany || !$Leasing->company)
        return $this->error("<strong>Du musst ein Leasing-Unternehmen aus wählen.</strong>");

      /**
       * An inspection id is required for the company to bill the
       * customer. So we need to make sure it is set.
       */
      if ($LeasingCompany?->requires_inspection_id && empty($params->leasing_inspection_id)) {
        return $this->error(
          "<strong>Du musst eine Inspektions-Nummer hinzufügen.</strong>"
        );
      }

      /**
       * Set it to be a leasing order.
       */
      $this->is_leasing = 1;
      $this->leasing_inspection_id = $params->leasing_inspection_id;

      /**
       * Save the leasing to the database.
       */
      $Leasing?->save();

      /**
       * Set the leasing id now, because it won't have an id when
       * just being 'mad[k]e'.
       */
      $this->leasing_id = $Leasing->id;

      /**
       * Purposefully not setting all the variables for leasing
       * to null again as an employee could by accident select "no"
       * for leasing and save it. When they come back to fix it, all
       * the previously filled information will still be there. Kuss
       */
    } elseif (isset($params->is_leasing) && $params->is_leasing === "no") {
      $this->is_leasing = 0;
    } else {
      $this->is_leasing ??= null;
    }

    /**
     * * Customer Addons
     */
    if (
      isset($params->customer_addons) &&
      is_array($params->customer_addons)
    ) {
      /**
       * Any given addon is not an int?
       */
      foreach ($params->customer_addons as $addon) {
        if (!is_int($addon)) {
          return $this->error(
            "<strong>Ungültige Anfrage an den Server</strong>"
          );
        }
      }

      /**
       * Set the customer addons! Laravel lets us just pass an
       * array and will transform it for us. So cool!
       */
      $this->customer_addons = $params->customer_addons;
    }

    /**
     * * Items
     */
    if (
      isset($params->repair_ids) &&
      is_array($params->repair_ids) &&
      isset($params->repair_ids_visible) &&
      is_array($params->repair_ids_visible) &&
      count($params->repair_ids) > 0
    ) {
      /**
       * @var array
       */
      $repairs = [];

      /**
       * Map all visible values to their corresponding repair id.
       */
      foreach ($params->repair_ids as $key => $rid) {
        if ($params->repair_ids_visible[$key] === "") {
          continue;
        }

        $repairs[is_numeric($rid) ? "id:$rid" : Utils::random_alpha_token(42)] = $params->repair_ids_visible[$key];
      }

      /**
       * Delete all existing repairs.
       */
      $this->repair_order_items()->delete();

      /**
       * Iterate through all selected repairs and check if they are still available.
       */
      foreach ($repairs as $repair_id => $repair) {
        /**
         * repair_id => id:98 || hfd8f7sfsdf98sf8dfdf7fdf87
         * repair    => Bremsflüssigkeit nachfüllen
         */

        /**
         * If nothing is set, it's an empty field. Continue then friend.
         */
        if (empty($repair) || empty($repair_id)) {
          continue;
        }

        /**
         * @var RepairOrderItem
         */
        $OrderItem = $this->items()->make();

        /**
         * Check if it's not a chosen initial id.
         */
        if (str_starts_with($repair_id, "id:")) {
          // pdie($repair_id);

          /**
           * @var array
           */
          $initial_id_explode = explode(":", $repair_id);

          /**
           * @var int
           */
          $initial_id = $initial_id_explode[1];

          /**
           * @var RepairType
           */
          $RepairType = RepairType::find($initial_id);

          /**
           * If the RepairType doesn't exist, something went
           * terribly wrong. This case should just be matching, if
           * someone messed around with the console. Let's hope not!
           */
          if (!$RepairType) {
            continue;
          }

          /**
           * Set values!
           */
          $OrderItem->custom_field = null;
          $OrderItem->repair_type_id = $RepairType->id;

          /**
           * Check if the visible value is a numeric value and match
           * it to a RepairType existing in the master data. This
           * case should match, if someone missed to press enter
           * on id input for selecting a repair from the master data.
           */
        } elseif (is_numeric($repair)) {
          /**
           * @var int
           */
          $repair = (int) $repair;

          /**
           * @var ?RepairType
           */
          $RepairType = RepairType::where([
            "type" => $this->type,
            "initial_id" => $repair,
          ])->first();

          /**
           * It doesn't exist?
           */
          if ($RepairType) {
            $OrderItem->custom_field = null;
            $OrderItem->repair_type_id = $RepairType->id;
          } else {
            $OrderItem->custom_field = $repair;
          }

          /**
           * In any other case it's just a custom value that has
           * been input.
           */
        } else {
          $OrderItem->custom_field = $repair;
        }

        /**
         * Save it!
         */
        $OrderItem->save();
      }
    }

    $this->db_transaction();

    try {
      $Leasing?->save();

      /**
       * Save the Order!
       */
      $this->save();

      /**
       * Commit it!
       */
      $this->db_commit();
    } catch (Exception $e) {
      /**
       * Log the Exception
       */
      Logger::to_file($e, "model_interaction_errors.log");

      /**
       * Rollback all changes.
       */
      $this->db_rollback();

      return $this->error(
        "<strong>Der Auftrag konnte nicht gespeichert werden.</strong> Schaue in den Error-Logs für mehr Informationen."
      );
    }

    /**
     * ? Success
     */
    return $this->success(
      "<strong>Gespeichert!</strong>",
      data: [
        "repair_order_id" => $this->id,
      ]
    );
  }

  /**
   * @return Repair
   */
  public function repair_order_items()
  {
    return $this->hasMany(RepairOrderItem::class);
  }

  /**
   * @return RepairOrderItem
   */
  public function items()
  {
    return $this->hasMany(RepairOrderItem::class);
  }

  /**
   * @return RepairOrderPart
   */
  public function parts()
  {
    return $this->hasMany(RepairOrderPart::class);
  }

  /**
   * @return Employee
   */
  public function employee()
  {
    return $this->belongsTo(Employee::class);
  }

  /**
   * @return Brand
   */
  public function brand()
  {
    return $this->belongsTo(Brand::class);
  }

  /**
   * @return Customer
   */
  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }

  /**
   * @return CustomerObject
   */
  public function customer_object()
  {
    return $this->belongsTo(CustomerObject::class);
  }

  /**
   * @return Bookmark
   */
  public function bookmark()
  {
    return $this->hasOne(Bookmark::class, "reference_id", "id")->where(
      "type",
      "order"
    );
  }

  /**
   * @return ?Leasing
   */
  public function leasing()
  {
    return $this->belongsTo(Leasing::class);
  }

  /**
   * Resets all values to null.
   *
   * @return void
   */
  public function reset()
  {
    // TODO: Remove is_leasing for just using leasing_id
    $this->is_leasing = null;
    $this->leasing_id = null;
    $this->leasing_inspection_id = null;
    $this->brand_id = null;
    $this->customer_object_id = null;
  }

  /**
   * @return string|false
   */
  public function submit_error()
  {
    return match (true) {
      !$this => "no_order_created",
      !$this->employee => "no_employee",
      !$this->customer => "no_customer",
      !$this->type => "no_type",
      (!$this->customer_object && !$this->brand) => "no_object",
      ($this->customer_object?->leasing && (
        !$this->leasing
        || $this->leasing?->company->requires_inspection_id && !$this->leasing_inspection_id
      )) => "invalid_leasing_combination",
      default => false,
    };
  }

  /**
   * @return bool
   */
  public function in_repair()
  {
    return $this->status === "REPAIR";
  }

  /**
   * @return bool
   */
  public function is_done()
  {
    return $this->status === "DONE";
  }

  /**
   * @return string
   */
  public function next_step_uri()
  {
    /**
     * @var string
     */
    $next_step_uri =
      "/repair/order/edit/$this->id/" .
      match (true) {
        !$this->employee => "employee",
        !$this->customer => "customer",
        !$this->customer_object && !$this->brand_id => "object",
        $this->type === "bike" && $this->is_leasing === null
        => "leasing",
        !$this->items->count() && !$this->parts->count() => "items",
        default => "overview",
      };

    /**
     * Add smooth animation.
     */
    $next_step_uri .= "#activeObject";

    return $next_step_uri;
  }

  /**
   * Increase for
   * + leasing
   *
   * @return int
   */
  public function loan_increase_factor()
  {
    return $this->is_leasing
      ? self::$leasing_surcharge_percent / 100 + 1
      : 1;
  }

  /**
   * Adds loan increase for each item.
   *
   * @return float
   */
  public function items_price()
  {
    $costs = 0.0;

    /**
     * Iterate through all items and add the prices together.
     */
    foreach ($this->items as $Item) {
      $costs +=
        ($Item->type->price ?? 0.0) * $this->loan_increase_factor();
    }

    return $costs;
  }

  /**
   * @return float
   */
  public function parts_price()
  {
    $costs = 0.0;

    /**
     * Iterate through all items and add the prices together.
     */
    foreach ($this->parts as $OrderPart) {
      $costs += $OrderPart->base_price();
    }

    return $costs;
  }

  /**
   * Base price includes
   * + items
   * + parts
   * + leasing [optional]
   *
   * @return float
   */
  public function base_price()
  {
    return $this->items_price() + $this->parts_price();
  }

  /**
   * Prepared price includes
   * + items
   * + parts
   * + up costs
   * + leasing [optional]
   *
   * @return bool
   */
  public function prepared_price()
  {
    /**
     * @var int|float
     */
    $base_price = $this->base_price();

    /**
     * 20 % extra for unpredictable repairs or replacements.
     */
    $up_costs = $base_price * 0.2;

    return $base_price + $up_costs;
  }

  /**
   * @return string
   */
  public function prepared_price_formatted()
  {
    return number_format($this->prepared_price(), 2, ",", ".");
  }

  /**
   * @return float
   */
  public function real_price()
  {
    return $this->base_price();
  }

  /**
   * @return float
   */
  public function real_price_formatted()
  {
    return number_format($this->real_price(), 2, ",", ".");
  }

  /**
   * @return float
   */
  public function real_price_ust_formatted()
  {
    return number_format(
      $this->real_price() * (self::$ust_percent / 100),
      2,
      ",",
      "."
    );
  }

  /**
   * @return string
   */
  public function display_status()
  {
    return match ($this->status) {
      "DONE" => "Fertig",
      "REPAIR" => "Wird repariert",
      default => "Offen",
    };
  }

  /**
   * @return string
   */
  public function display_status_icon()
  {
    return match ($this->status) {
      "DONE" => "done_all",
      "REPAIR" => "build",
      default => "trip_origin",
    };
  }

  /**
   * @return string
   */
  public function display_status_color()
  {
    return $this->status === "DONE"
      ? "color=green"
      : ($this->status === "REPAIR"
        ? "color=orange"
        : "slighter");
  }

  /**
   * @return object
   */
  public function generate_confirmation()
  {
    /**
     * @var string
     */
    $file_name = "{$this->reference_id}_auftrag.pdf";
    $file_path = _root() . "/documents/orders/$this->type/$file_name";
    $return_data = [
      "file_path" => $file_path,
      "file_name" => $file_name,
    ];

    /**
     * @var bool
     */
    $file_exists = file_exists($file_path);

    /**
     * Invoice exists already?
     */
    if ($file_exists) {
      return $this->success(
        "<strong>Auftrag existiert bereits.</strong> Du kannst ihn sofort herunterladen.",
        data: $return_data,
        json_encoded: false
      );
    }

    /**
     * @var Options
     */
    $Options = new Options();
    $Options->set("isRemoteEnabled", true);
    $Options->set("tempDir", _root() . "/documents/tmp");

    /**
     * @var Dompdf
     */
    $pdf = new Dompdf($Options);

    /**
     * @var string
     */
    $logo_path =
      "https://bueld.bruder.business/assets/images/logo_upright.png";

    /**
     * @var Customer
     */
    $Customer = $this->customer;

    /**
     * @var Employee
     */
    $Employee = $this->employee;

    /**
     * HTML.
     *
     * {LOGO_PATH}
     * {CUSTOMER_ID}
     * {CUSTOMER}
     * {CUSTOMER_FULL_NAME}
     * {LEASING}
     * {ACTIVE_EMPLOYEE_NAME}
     * {ORDER_TYPE}
     * {ORDER_DATE}
     * {ORDER_REFERENCE_ID}
     * {ORDER_REPAIRS}
     * {ORDER_PARTS}
     * {ORDER_TERMS}
     * {ORDER_TYPE_BRAND}
     * {ORDER_REPAIR_TYPE}
     * {ORDER_REPAIRS_WORKER}
     * {ORDER_PARTS_WORKER}
     * {FULL_PRICE}
     * {ORDER_FINISH_QR}
     * {ORDER_CUSTOMER_ADDONS}
     * {ORDER_OBJECT_BRAND}
     * {ORDER_OBJECT_UNIQUE_IDENTIFIER}
     */
    $HTML = file_get_contents(TEMPLATE . "/document/repair-request.html");

    /**
     * * Order & Customer
     */
    $HTML = str_replace("{LOGO_PATH}", $logo_path, $HTML);
    $HTML = str_replace("{CUSTOMER_ID}", $Customer->id, $HTML);
    $HTML = str_replace("{CUSTOMER_FULL_NAME}", $Customer->full_name(), $HTML);
    $HTML = str_replace("{CUSTOMER_CONTACT_OPTION}", $Customer->phone ?? $Customer->mail ?? "Nichts angegeben", $HTML);
    $HTML = str_replace(
      "{ACTIVE_EMPLOYEE_NAME}",
      $Employee->full_name(),
      $HTML
    );
    // $HTML = str_replace("{ORDER_TYPE}", $this->display_type(), $HTML);
    $HTML = str_replace("{ORDER_DATE}", date("d.m.Y"), $HTML);
    $HTML = str_replace("{ORDER_REFERENCE_ID}", $this->reference_id, $HTML);
    $HTML = str_replace(
      "{ORDER_REPAIR_TYPE}",
      $this->display_type(),
      $HTML
    );
    $HTML = str_replace(
      "{FULL_PRICE}",
      $this->prepared_price_formatted() . " €",
      $HTML
    );

    /**
     * * Customer
     */
    $HTML = str_replace(
      "{CUSTOMER}",
      <<<HTML
      <div lt style=margin-right:32px;margin-top:24px;>
          <div>
          <p bold style="font-size: 16px">Kunde</p>
          <p>{$Customer->full_name()}</p>
          <p>$Customer->address_line_1</p>
          <p>$Customer->postcode $Customer->city</p>
          </div>
      </div>
  HTML,
      $HTML
    );

    /**
     * * Leasing
     */
    if ($this->is_leasing) {
      $leasing_id = $this->leasing->contract_id;
      $leasing_company_name = $this->leasing->company->name;

      $HTML = str_replace(
        "{LEASING}",
        <<<HTML
          <div lt style="margin-top:24px;">
            <div>
              <p bold style="font-size: 16px">Leasing</p>
              <p>Vertrag: $leasing_id</p>
              <p>Inspektion: $this->leasing_inspection_id</p>
              <p>Bei <strong bold>$leasing_company_name</strong></p>
            </div>
          </div>
        HTML,
        $HTML
      );
    } else {
      $HTML = str_replace("{LEASING}", "", $HTML);
    }

    /**
     * * Customer addons
     */
    if ($this->type === "sewing") {
      /**
       * @var array
       */
      $display_addons = [
        "others" => "Sonstige",
        "suitcase" => "Koffer",
        "bobbin_case" => "Spulenkasten",
        "starter" => "Anlasser",
      ];

      /**
       * @var string
       */
      $customer_addon_string = "";

      /**
       * Iterate through all set addons and add the activated ones
       * to the string for the actual HTML.
       */
      foreach (
        json_decode($this->customer_addons ?? "{}")
        as $addon => $status
      ) {
        if ($status) {
          $customer_addon_string .= <<<HTML
            <p style="
            background: rgba(183, 192, 241, .58);
            border-radius:8px;
            padding-left:8px;
            padding-right:8px;
            margin-right:0px;
            padding-bottom:4px;
            line-height: 22px;
            display:inline-block;
            ">{$display_addons[$addon]}</p>
          HTML;
        }
      }

      /**
       * Set to "Keine", if no addons are set.
       */
      if ($customer_addon_string) {
        $HTML = str_replace(
          "{ORDER_CUSTOMER_ADDONS}",
          <<<HTML
            <div lt style="width:calc(100% - (228px + 24px));">
              <div style=margin-bottom:12px;>
                <div>
                  <p bold>Mitgebracht:</p>
                  <div style="padding-top:5px;">
                  $customer_addon_string
                  </div>
                </div>
              </div>
            </div>
          HTML,
          $HTML
        );
      } else {
        $HTML = str_replace("{ORDER_CUSTOMER_ADDONS}", "", $HTML);
      }

      /**
       * No addons for any other type than sewing.
       */
    } else {
      $HTML = str_replace("{ORDER_CUSTOMER_ADDONS}", "", $HTML);
    }

    /**
     * @var string
     */
    $brand = $this->customer_object?->brand->name ?? "Nichts angegeben";

    /**
     * * OBJECT & BRAND
     */
    $HTML = str_replace(
      "{ORDER_OBJECT_BRAND}",
      <<<HTML
        <div lt style="width:calc(228px + 24px);">
          <p bold>Objekt & Marke</p>
          <p lt style="
          background: rgba(214, 241, 183, 0.58);
          border-radius:8px;
          padding-left:8px;
          padding-right:8px;
          margin-right:4px;
          padding-bottom:4px;
          line-height: 22px;
          ">{$brand}</p>
        </div>
      HTML,
      $HTML
    );

    /**
     * This is in the confirmation paper that the worker will get.
     */
    $HTML = str_replace(
      "{ORDER_TYPE_BRAND}",
      $this->customer_object?->brand->name ?? "k. A.",
      $HTML
    );

    /**
     * * CUSTOMER OBJECT UNIQUE IDENTIFIER
     */
    $HTML = str_replace(
      "{ORDER_OBJECT_ID}",
      $this->customer_object?->object_unique_identifier ?? "Keine",
      $HTML
    );

    /**
     * * Repairs
     */
    if ($this->items->count()):
      $HTML_REPAIRS = "";
      $HTML_REPAIRS_WORKER = "";

      foreach ($this->items as $Item):
        /**
         * @var RepairOrderItem $Item
         */

        /**
         * @var string
         */
        $item_name = $Item->name();

        /**
         * @var string
         */
        $price = $Item->formatted_price();

        $HTML_REPAIRS .= <<<HTML
          <div style="border-bottom: 1px solid rgba(0, 0, 0, 0.04);padding-top: 4px;padding-bottom: 4px;">
            <div inline style="width: calc(100% - 13% - 21%)">$item_name</div>
            <p inline style="width: 12%">1</p>
            <p inline style="width: calc(20% + 4px); text-align: right">$price €</p>
          </div>
        HTML;

        $HTML_REPAIRS_WORKER .= <<<HTML
        <div style="padding-top: 8px;padding-bottom:8px;border-bottom: 1px solid rgba(0, 0, 0, 0.24);line-height: 1;">
          <div lt style="border-radius: 50%;border: 1px solid rgba(0, 0, 0, 0.62);height: 24px;min-width: 24px;"></div>
          <p lt text style="padding-top:7px;padding-left:12px;">$item_name</p>
          <div cl></div>
        </div>
        HTML;
      endforeach;

      $HTML = str_replace("{ORDER_REPAIRS}", $HTML_REPAIRS, $HTML);
      $HTML = str_replace(
        "{ORDER_REPAIRS_WORKER}",
        $HTML_REPAIRS_WORKER,
        $HTML
      );
    else:
      $HTML = str_replace(
        "{ORDER_REPAIRS}",
        "<p style='padding-top:4px;padding-bottom:4px;opacity:.8;'>Keine Reparaturen angegeben</p>",
        $HTML
      );
      $HTML = str_replace(
        "{ORDER_REPAIRS_WORKER}",
        "<p style='padding-top:8px;padding-bottom:8px;opacity:.8;'>Keine Reparaturen angegeben</p>",
        $HTML
      );
    endif;

    /**
     * * Parts
     */
    if ($this->parts->count()):
      $HTML_PARTS = "";
      $HTML_PARTS_WORKER = "";

      foreach ($this->parts as $OrderPart):
        /**
         * @var string
         */
        $item_name = $OrderPart->name();

        /**
         * @var string
         */
        $price = $OrderPart->formatted_price();

        $HTML_PARTS .= <<<HTML
          <div style="border-bottom: 1px solid rgba(0, 0, 0, 0.04);padding-top: 4px;padding-bottom: 4px;">
            <div inline style="width: calc(100% - 13% - 21%)">$item_name</div>
            <p inline style="width: 12%">$OrderPart->amount</p>
            <p inline style="width: calc(20% + 4px); text-align: right">$price €</p>
          </div>
        HTML;

        $HTML_PARTS_WORKER .= <<<HTML
          <div style="padding-top: 8px;padding-bottom:8px;border-bottom: 1px solid rgba(0, 0, 0, 0.24);line-height: 1;">
            <div lt style="border-radius: 50%;border: 1px solid rgba(0, 0, 0, 0.62);height: 24px;min-width: 24px;"></div>
            <p lt text style="padding-top:7px;padding-left:12px;">$item_name</p>
            <div cl></div>
          </div>
        HTML;
      endforeach;

      $HTML = str_replace("{ORDER_PARTS}", $HTML_PARTS, $HTML);
      $HTML = str_replace(
        "{ORDER_PARTS_WORKER}",
        $HTML_PARTS_WORKER,
        $HTML
      );
    else:
      $HTML = str_replace(
        "{ORDER_PARTS}",
        "<p style='padding-top:4px;padding-bottom:4px;opacity:.8;'>Keine Ersatzteile beigefügt</p>",
        $HTML
      );
      $HTML = str_replace(
        "{ORDER_PARTS_WORKER}",
        "<p style='padding-top:8px;padding-bottom:8px;opacity:.8;'>Keine Ersatzteile beigefügt</p>",
        $HTML
      );
    endif;

    /**
     * * Terms
     */
    $HTML_LEGAL_NOTICE =
      $this->type === "bike"
      ? <<<HTML
          <p style="font-size: 12px">
            Die Endkontrolle und Probefahrt ist nach dem Gerätesicherungsgesetz
            von 1997 zwingend erforderlich. Hier werden alle sicherheitsrelevanten
            Teile geprüft wie Lenkung, Bremsen, Licht, etc.
            <br><br>
            Wird der Auftragsgegenstand nicht innerhalb 4 Wochen nach
            Fertigstellung und Abholungsaufforderung abgeholt, sind wir berechtigt
            mit Ablauf dieser Frist ein angemessenes Lagergeld von 30€ pro
            angefangenen Monat zu berechnen. Erfolgt nicht spätestens 6 Monate
            nach der Abholaufforderung die Abholung, entfällt die Verpflichtung
            zur weiteren Aufbewahrung und jede Haftung für leicht fahrlässige
            Beschädigungen oder Umgang. Wir sind berechtigt, den
            Auftragsgegenstand nach Ablauf dieser Frist zur Deckung unserer
            Forderungen zum Verkehrswert zu veräußern oder zu entsorgen. Die
            angefallenen Kosten trägt der Auftraggeber.
          </p>
        HTML
      : <<<HTML
          <p style="font-size: 12px">
            Wird der Auftragsgegenstand nicht innerhalb 4 Wochen nach Fertigstellung und Abholungsaufforderung abgeholt, sind wir berechtigt mit Ablauf dieser Frist ein angemessenes Lagergeld von 30€ pro angefangenen Monat zu berechnen. Erfolgt nicht spätestens 6 Monate nach der Abholaufforderung die Abholung, entfällt die Verpflichtung zur weiteren Aufbewahrung und jede Haftung für leicht fahrlässige Beschädigungen oder Umgang. Wir sind berechtigt, den Auftragsgegenstand nach Ablauf dieser Frist zur Deckung unserer Forderungen zum Verkehrswert zu veräußern oder zu entsorgen. Die angefallenen Kosten trägt der Auftraggeber.
          </p>
        HTML;

    $HTML = str_replace("{ORDER_TERMS}", $HTML_LEGAL_NOTICE, $HTML);

    try {
      /**
       * Write the output to PDF.
       */
      $pdf->loadHtml($HTML);
      $pdf->render();

      /**
       * Get the output.
       */
      $Output = $pdf->output();

      /**
       * Save the Output to the invoices path so the user can
       * access it whenever they wish to.
       */
      file_put_contents($file_path, $Output);
    } catch (\Dompdf\Exception $e) {
      /**
       * Log the error to a corresponding file.
       */
      Logger::to_file($e, "order_errors.log");

      return $this->error(
        "<strong>Es konnte keine Auftragsbestätigung generiert werden.</strong> Checke die Error-Logs für mehr Informationen."
      );
    }

    return $this->success(
      "<strong>Auftrag generiert!</strong> Du kannst ihn nun herunterladen.",
      data: $return_data,
      json_encoded: false
    );
  }

  /**
   * @return object
   */
  public function generate_invoice()
  {
    /**
     * @var string
     */
    $file_name = "{$this->reference_id}_rechnung.pdf";
    $file_path = _root() . "/documents/orders/$this->type/$file_name";
    $return_data = [
      "file_path" => $file_path,
      "file_name" => $file_name,
    ];

    /**
     * @var bool
     */
    $file_exists = file_exists($file_path);

    /**
     * Invoice exists already?
     */
    if ($file_exists) {
      return $this->success(
        "<strong>Rechnung existiert bereits.</strong> Du kannst sie sofort herunterladen.",
        data: $return_data,
        json_encoded: false
      );
    }

    /**
     * @var Options
     */
    $Options = new Options();
    $Options->set("isRemoteEnabled", true);
    $Options->set("tempDir", _root() . "/documents/tmp");

    /**
     * @var Dompdf
     */
    $pdf = new Dompdf($Options);

    /**
     * @var string
     */
    $logo_path =
      "https://bueld.bruder.business/assets/images/logo_upright.png";

    /**
     * @var Customer
     */
    $Customer = $this->customer;

    /**
     * @var Employee
     */
    $Employee = $this->employee;

    /**
     * HTML.
     *
     * {ORDER_REFERENCE_ID}
     * {LOGO_PATH}
     * {CUSTOMER_ID}
     * {CUSTOMER_NAME}
     * {CUSTOMER_ADDRESS}
     * {CUSTOMER_POSTCODE_CITY}
     * {ACTIVE_EMPLOYEE_NAME}
     * {ORDER_INVOICE_DATE}
     * {ORDER_REPAIR_TYPE}
     * {ORDER_REPAIRS}
     * {ORDER_PARTS}
     * {FULL_PRICE}
     * {FULL_PRICE_UST}
     * {ORDER_TERMS}
     */
    $HTML = file_get_contents(
      TEMPLATE . "/document/repair-request-invoice.html"
    );

    /**
     * * Order & Customer
     */
    $HTML = str_replace("{ORDER_REFERENCE_ID}", $this->reference_id, $HTML);
    $HTML = str_replace("{LOGO_PATH}", $logo_path, $HTML);
    $HTML = str_replace("{CUSTOMER_ID}", $Customer->id, $HTML);
    $HTML = str_replace("{ACTIVE_EMPLOYEE_NAME}", $Employee->full_name(), $HTML);
    $HTML = str_replace("{ORDER_INVOICE_DATE}", date("d.m.Y"), $HTML);
    $HTML = str_replace("{ORDER_REPAIR_TYPE}", $this->display_type(), $HTML);
    $HTML = str_replace("{FULL_PRICE}", $this->real_price_formatted() . " €", $HTML);
    $HTML = str_replace("{FULL_PRICE_UST}", $this->real_price_ust_formatted() . " €", $HTML);

    /**
     * @var string
     */
    $customer_contact_option = "<p>" . ($Customer->phone ?? $Customer->mail) . "</p>";

    /**
     * * Customer
     */
    $HTML = str_replace(
      "{CUSTOMER}",
      <<<HTML
        <div lt style=margin-right:32px;margin-top:24px;>
          <div>
            <p bold style="font-size: 16px">Kunde</p>
            <p>{$Customer->full_name()}</p>
            <p>$Customer->address_line_1</p>
            <p>$Customer->postcode $Customer->city</p>
            $customer_contact_option
          </div>
        </div>
      HTML,
      $HTML
    );

    /**
     * * Leasing
     */
    if ($this->is_leasing) {
      $leasing_id = $this->leasing->contract_id;
      $leasing_company_name = $this->leasing->company->name;

      $HTML = str_replace(
        "{LEASING}",
        <<<HTML
          <div lt style="margin-top:24px;">
            <div>
              <p bold style="font-size: 16px">Leasing</p>
              <p>Vertrag: $leasing_id</p>
              <p>Inspektion: $this->leasing_inspection_id</p>
              <p>Bei <strong bold>$leasing_company_name</strong></p>
            </div>
          </div>
        HTML,
        $HTML
      );
    } else {
      $HTML = str_replace("{LEASING}", "", $HTML);
    }

    /**
     * * Customer addons
     */
    if ($this->type === "sewing") {
      /**
       * @var array
       */
      $display_addons = [
        "others" => "Sonstige",
        "suitcase" => "Koffer",
        "bobbin_case" => "Spulenkasten",
        "starter" => "Anlasser",
      ];

      /**
       * @var string
       */
      $customer_addon_string = "";

      /**
       * Iterate through all set addons and add the activated ones
       * to the string for the actual HTML.
       */
      foreach (
        json_decode($this->customer_addons ?? "{}")
        as $addon => $status
      ) {
        if ($status) {
          $customer_addon_string .= <<<HTML
            <p style="
            background: rgba(183, 192, 241, .58);
            border-radius:8px;
            padding-left:8px;
            padding-right:8px;
            margin-right:0px;
            padding-bottom:4px;
            line-height: 22px;
            display:inline-block;
            ">{$display_addons[$addon]}</p>
          HTML;
        }
      }

      /**
       * Set to "Keine", if no addons are set.
       */
      if ($customer_addon_string) {
        $HTML = str_replace(
          "{ORDER_CUSTOMER_ADDONS}",
          <<<HTML
            <div lt style="width:calc(100% - (228px + 24px));">
              <div style=margin-bottom:12px;>
                <div>
                  <p bold>Mitgebracht:</p>
                  <div style="padding-top:5px;">
                  $customer_addon_string
                  </div>
                </div>
              </div>
            </div>
          HTML,
          $HTML
        );
      } else {
        $HTML = str_replace("{ORDER_CUSTOMER_ADDONS}", "", $HTML);
      }

      /**
       * No addons for any other type than sewing.
       */
    } else {
      $HTML = str_replace("{ORDER_CUSTOMER_ADDONS}", "", $HTML);
    }

    /**
     * @var string
     */
    $brand = $this->customer_object?->brand->name ?? "Nichts angegeben";

    /**
     * * OBJECT & BRAND
     */
    $HTML = str_replace(
      "{ORDER_OBJECT_BRAND}",
      <<<HTML
        <div lt style="width:calc(228px + 24px);">
          <p bold>Objekt & Marke</p>
          <p lt style="
          background: rgba(214, 241, 183, 0.58);
          border-radius:8px;
          padding-left:8px;
          padding-right:8px;
          margin-right:4px;
          padding-bottom:4px;
          line-height: 22px;
          ">{$brand}</p>
        </div>
      HTML,
      $HTML
    );

    /**
     * Create a QR code with an url to finish the order.
     */
    $QRBuilder = new Builder(
      writer: new PngWriter(),
      writerOptions: [],
      validateResult: false,
      data: _env("SERVER_ADDRESS") .
        "/repair/order/" .
        $this->id .
        "/finish?token=test",
      encoding: new Encoding("UTF-8"),
      errorCorrectionLevel: ErrorCorrectionLevel::High,
      size: 300,
      margin: 12,
      roundBlockSizeMode: RoundBlockSizeMode::Margin
    );

    $QRCode = $QRBuilder->build();

    $HTML = str_replace("{ORDER_FINISH_QR}", $QRCode->getDataUri(), $HTML);

    /**
     * * Repairs
     */
    if ($this->items->count()):
      $HTML_REPAIRS = "";

      foreach ($this->items as $Item):
        /**
         * @var string
         */
        $item_name = $Item->name();

        /**
         * @var string
         */
        $price = $Item->formatted_price();

        $HTML_REPAIRS .= <<<HTML
          <div style="border-bottom: 1px solid rgba(0, 0, 0, 0.04);padding-top: 4px;padding-bottom: 4px;">
            <div inline style="width: calc(100% - 13% - 21%)">$item_name</div>
            <p inline style="width: 12%">1</p>
            <p inline style="width: calc(20% + 4px); text-align: right">$price €</p>
          </div>
        HTML;
      endforeach;

      $HTML = str_replace("{ORDER_REPAIRS}", $HTML_REPAIRS, $HTML);
    else:
      $HTML = str_replace(
        "{ORDER_REPAIRS}",
        "<p style='padding-top:4px;padding-bottom:4px;opacity:.8;'>Keine Reparaturen angegeben</p>",
        $HTML
      );
    endif;

    /**
     * * Parts
     */
    if ($this->parts->count()):
      $HTML_PARTS = "";

      foreach ($this->parts as $OrderPart):
        /**
         * @var string
         */
        $item_name = $OrderPart->name();

        /**
         * @var string
         */
        $price = $OrderPart->formatted_price();

        $HTML_PARTS .= <<<HTML
          <div style="border-bottom: 1px solid rgba(0, 0, 0, 0.04);padding-top: 4px;padding-bottom: 4px;">
            <div inline style="width: calc(100% - 13% - 21%)">$item_name</div>
            <p inline style="width: 12%">$OrderPart->amount</p>
            <p inline style="width: calc(20% + 4px); text-align: right">$price €</p>
          </div>
        HTML;
      endforeach;

      $HTML = str_replace("{ORDER_PARTS}", $HTML_PARTS, $HTML);
    else:
      $HTML = str_replace(
        "{ORDER_PARTS}",
        "<p style='padding-top:4px;padding-bottom:4px;opacity:.8;'>Keine Ersatzteile beigefügt</p>",
        $HTML
      );
    endif;

    /**
     * * Terms
     */
    $HTML_LEGAL_NOTICE = <<<HTML
      <p style="font-size:12px;">
        Auf Reparaturen aller Art gewähren wir Ihnen 12 Monate Gewährleistung. Für Verschleißschäden, die sich aus der Nutzung des Produktes ergeben, besteht keine Gewährleistung. Insbesondere dann nicht, wenn diese als gebrauchsüblich anzusehen sind.
      </p>
    HTML;

    $HTML = str_replace("{ORDER_TERMS}", $HTML_LEGAL_NOTICE, $HTML);

    try {
      /**
       * Write the output to PDF.
       */
      $pdf->loadHtml($HTML);
      $pdf->render();

      /**
       * Get the output.
       */
      $Output = $pdf->output();

      /**
       * Save the Output to the invoices path so the user can
       * access it whenever they wish to.
       */
      file_put_contents($file_path, $Output);
    } catch (\Dompdf\Exception $e) {
      /**
       * Log the error to a corresponding file.
       */
      Logger::to_file($e, "order_errors.log");

      return $this->error(
        "<strong>Es konnte keine Rechnung für diesen Auftrag generiert werden.</strong> Checke die Error-Logs für mehr Informationen."
      );
    }

    return $this->success(
      "<strong>Rechnung generiert!</strong> Du kannst ihn nun herunterladen.",
      data: $return_data,
      json_encoded: false
    );
  }
}
