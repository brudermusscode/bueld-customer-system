<?php

namespace Bruder\Model;

use Bruder\Application\Logger;
use Bruder\Bruder;
use Bruder\Mail\Mail;
use Bruder\Model\Bookmark;
use Bruder\Model\Customer\CustomerObject;
use Bruder\Model\Leasing\Leasing;
use Bruder\Model\Repair\RepairOrder;
use Bruder\Validate\GermanAddress;
use Bruder\Time\Time;
use Bruder\Utils\Str;
use Exception;

class Customer extends Bruder
{
	/**
	 * @var array
	 */
	protected $fillable = [
		"company_name",
		"firstname",
		"lastname",
		"sex",
		"phone",
		"mail",
		"address_line_1",
		"address_line_2",
		"postcode",
		"city",
		"country_code",
		"receive_marketing",
		"is_leasing",
		"is_asshole",
	];

	/**
	 * @param object $params
	 * @return Customer
	 */
	public function new(object $params)
	{

		/**
		 * * Is Company
		 */
		if (!empty($params->is_company)) {

			/**
			 * Either firstname OR lastname is set, but not both?
			 */
			if (
				!empty($params->firstname) && empty($params->lastname)
				|| !empty($params->lastname) && empty($params->firstname)
			)
				return $this->error("<strong>Gib Vor- und Nachname für den Ansprechpartner ein.</strong> Lasse beides frei, wenn es keinen für dieses Unternehmen gibt.");

			/**
			 * Both firstname AND lastname are set BUT no gender?
			 */
			elseif (!empty($params->firstname) && !empty($params->lastname) && empty($params->sex))
				return $this->error("<strong>Wähle ein Geschlecht für den Ansprechpartner aus.</strong>");
		}

		/**
		 * No full name set?
		 */
		if (empty($params->full_name))
			return $this->error("<strong>Trage einen Vor- & Nachnamen ein.</strong>");

		/**
		 * * Sex
		 * No gender set?
		 */
		if (empty($params->sex))
			return $this->error("<strong>Trage ein Geschlecht ein.</strong>");

		/**
		 * Gender is invalid?
		 */
		if (!in_array($params->sex, ["m", "w"]))
			return $this->error("<strong>Wähle ein gültiges Geschlecht.</strong> Männlich oder weiblich.");

		/**
		 * @var Customer
		 */
		$Customer = Customer::make();

		/**
		 * * Receive Marketing
		 */
		$Customer->receive_marketing = isset($params->receive_marketing) && $params->receive_marketing > 0 ? 1 : 0;

		/**
		 * * First- & Lastname
		 */
		if (empty($params->is_company)) {
			$name_split_comma = explode(",", $params->full_name);

			/**
			 * Name contains a comma (,), so the user probably entered the
			 * lastname first. This is invalid.
			 */
			if (count($name_split_comma) > 1)
				return $this->error("<strong>Der Name des Kunden sollte mit dem Vornamen beginnen und keine Kommas enthalten.</strong>");

			/**
			 * Check if the customer name has a space in the middle
			 * somewhere to ensure a first- and lastname is set.
			 */
			if (!str_contains(trim($params->full_name), " "))
				return $this->error("<strong>Der Name vom Kunden sollte aus Vor- und Nachname bestehen!</strong>");

			/**
			 * @var array
			 */
			$name_split = explode(" ", trim($params->full_name));

			/**
			 * @var string
			 */
			$firstname = $name_split[0];

			/**
			 * Any of the first or lastname is too short?
			 */
			foreach ($name_split as $key => $name) {
				if (strlen($name) < 2)
					$this->error("<strong>Vor- und Nachname sollten jeweils mindestens aus 2 Buchstaben bestehen!</strong>");

				/**
				 * Append the name to the firstname if there are more than 2
				 * names in the array of full name split.
				 */
				if (count($name_split) > 2 && $key > 0 && $key < (count($name_split) - 1))
					$firstname .= " " . $name;
			}

			/**
			 * @var string
			 */
			$lastname = $name_split[count($name_split) - 1];

			/**
			 * Set name for the customer.
			 */
			$Customer->firstname = ucwords(strtolower($firstname));
			$Customer->lastname = ucwords(strtolower($lastname));
		}

		/**
		 * * Company Name
		 */
		else {

			// TODO: Some kind of name validation

			$Customer->company_name = $params->full_name;
			$Customer->firstname = ucwords(strtolower($params->firstname));
			$Customer->lastname = ucwords(strtolower($params->lastname));
		}

		/**
		 * * Mail
		 */
		if (!empty($params->mail)) {

			/**
			 * We want to remove all whitespace from the mail before
			 * validating it so we can alidate the real thing. Workers
			 * might add a space by accident and should not get
			 * confused by it.
			 */
			$params->mail = preg_replace('/\s+/', '', $params->mail);

			/**
			 * Mail is invalid?
			 */
			if (!filter_var(preg_replace('/\s+/', '', $params->mail), FILTER_VALIDATE_EMAIL))
				return $this->error("<strong>Die E-Mail Adresse ist ungültig.</strong> Nutze das Format example@domain.de!");

			/**
			 * Mail exists already?
			 */
			if (Customer::where("mail", $params->mail)->first())
				return $this->error("<strong>Diese E-Mail Adresse existiert bereits.</strong> Wahrscheinlich ist der Kunde schon in unserem System registriert.");
		}

		/**
		 * Set the mail free from any whitespace.
		 */
		$Customer->mail = $params->mail ? $params->mail : null;

		/**
		 * * Phone
		 */
		if (!empty($params->phone)) {

			/**
			 * @var string
			 */
			$params->phone = preg_replace('/\s+/', '', $params->phone);

			/**
			 * Phone doesn't start with 0 and only contains numeric characters?
			 */
			if (!preg_match('/^0\d+$/', $params->phone))
				return $this->error("<strong>Die Telefonnummer darf nur aus Zahlen bestehen und muss mit einer 0 beginnen (z. B. 015773602821)</strong>");

			/**
			 * Phone too long or too short?
			 */
			if (strlen($params->phone) < 4 || strlen($params->phone) > 18)
				return $this->error("<strong>Gib eine gültige Telefonnummer ein!</strong>");
		}

		/**
		 * Set the phone.
		 */
		$Customer->phone = $params->phone ? $params->phone : null;

		/**
		 * Atleast a phone or mail has to be set!
		 */
		if (!$Customer->phone && !$Customer->mail)
			return $this->error("<strong>Mindestens eine Handynummer ODER eine E-Mail Adresse muss angegeben werden.</strong>");

		/**
		 * Find the address and check if it's valid.
		 *
		 * @var stdClass
		 */
		$Addresses = (new GermanAddress)->find_by(
			street: $params->address_line_1,
			city: $params->city,
			postcode: $params->postcode
		);

		/**
		 * No address matching?
		 */
		if (!$Addresses)
			return $this->error("<strong>Die Adresse existiert nicht.</strong> Schaue noch einmal, ob du dich vertippt hast oder frage den Kunden.");

		// TODO: There could be more than one address.

		/**
		 * @var object
		 */
		$Address = $Addresses[0];

		/**
		 * @var string
		 *
		 * '/\d/' checks if atleast one digit is contained, which in
		 * this case should be the house number.
		 *
		 * '/^.*?(\d.*)$/' Matches everything from the first digit
		 * till the end of the string, which in this case should be
		 * the whole house number with possible letters.
		 */
		$hnumber = preg_match('/\d/', $params->address_line_1)
			? preg_replace('/^.*?(\d.*)$/', '$1', $params->address_line_1)
			: '';

		/**
		 * No number set?
		 */
		if (!$hnumber)
			return $this->error("<strong>Gib eine Hausnummer ein.</strong>");

		/**
		 * Remove all whitespace.
		 */
		$hnumber = Str::strip_whitespace($hnumber);

		/**
		 * Remove leading zeros.
		 */
		$hnumber = ltrim($hnumber ?: "0", "0");

		/**
		 * Check if the first char is still a number.
		 */
		if (!ctype_digit($hnumber ? $hnumber[0] : "a"))
			return $this->error("<strong>Die Hausnummer muss mit einer Zahl beginnen.</strong>");

		/**
		 * Set the good formatted address parameter.
		 */
		$Customer->address_line_1 = $Address->street . " $hnumber";
		$Customer->city = $Address->city;
		$Customer->postcode = $Address->plz;

		/**
		 * Begin a database transaction.
		 */
		$Customer->db_transaction();

		try {

			/**
			 * Save it!
			 */
			$Customer->save();

			/**
			 * Commit!
			 */
			$this->db_commit();
		} catch (\Exception $e) {

			/**
			 * Log to errors file!
			 */
			Logger::to_file($e);

			/**
			 * Rollback!
			 */
			$this->db_rollback();

			return $this->error("<strong>Es konnte kein neuer Kunde erstellt werden.</strong> Schaue in den Error-Logs für mehr Infos!");
		}

		return $Customer;
	}

	/**
	 * @return string
	 */
	public function edit(object $params)
	{

		/**
		 * * Mail
		 */
		if (!empty($params->mail)) {

			/**
			 * We want to remove all whitespace from the mail before
			 * validating it so we can alidate the real thing. Workers
			 * might add a space by accident and should not get
			 * confused by it.
			 */
			$params->mail = preg_replace('/\s+/', '', $params->mail);

			/**
			 * Mail is invalid?
			 */
			if (!filter_var(preg_replace('/\s+/', '', $params->mail), FILTER_VALIDATE_EMAIL))
				return $this->error("<strong>Die E-Mail Adresse ist ungültig.</strong> Nutze das Format example@domain.de!");

			/**
			 * Mail exists already?
			 */
			if (Customer::where("mail", $params->mail)->first() && $this->mail !== $params->mail)
				return $this->error("<strong>Diese E-Mail Adresse ist ebreits für einen anderen Kunden hinterlegt.</strong>");
		}

		/**
		 * Set the mail free from any whitespace and completly validated.
		 */
		$this->mail = $params->mail ? $params->mail : null;

		/**
		 * * Phone
		 */
		if (!empty($params->phone)) {

			/**
			 * @var string
			 */
			$params->phone = preg_replace('/\s+/', '', $params->phone);

			/**
			 * Phone doesn't start with 0 and only contains numeric characters?
			 */
			if (!preg_match('/^0\d+$/', $params->phone))
				return $this->error("<strong>Die Telefonnummer darf nur aus Zahlen bestehen und muss mit einer 0 beginnen (z. B. 015773602821)</strong>");

			/**
			 * Phone too long or too short?
			 */
			if (strlen($params->phone) < 4 || strlen($params->phone) > 18)
				return $this->error("<strong>Gib eine gültige Telefonnummer ein!</strong>");
		}

		/**
		 * Set the phone completly validated.
		 */
		$this->phone = $params->phone ? $params->phone : null;

		/**
		 * Atleast a phone or mail has to be set!
		 */
		if (!$this->phone && !$this->mail)
			return $this->error("<strong>Mindestens eine Handynummer ODER eine E-Mail Adresse muss angegeben werden.</strong>");

		/**
		 * Save it!
		 */
		try {
			/**
			 * Save & commit!
			 */
			$this->save();
			$this->db_commit();
		} catch (Exception $e) {

			/**
			 * Log & rollback.
			 */
			Logger::to_file($e, "model_interaction_errors.log");
			$this->db_rollback();

			return $this->error("<strong>Der Kunde konnte nicht gespeichert werden.</strong> Schaue in die Error-Logs für mehr Informationen.");
		}

		return $this->success("<strong>Gespeichert!</strong>");
	}

	/**
	 * @return ?CustomerObject
	 */
	public function objects()
	{
		return $this->hasMany(CustomerObject::class);
	}

	/**
	 * @return ?CustomerObject
	 */
	public function bikes()
	{
		return $this->hasMany(CustomerObject::class)
			->where("type", "bike");
	}

	/**
	 * @return ?CustomerObject
	 */
	public function sewing_machines()
	{
		return $this->hasMany(CustomerObject::class)
			->where("type", "sewing");
	}

	/**
	 * @return ?Leasing
	 */
	public function leasings()
	{
		return $this->hasMany(Leasing::class);
	}

	/**
	 * @return Mailing
	 */
	public function mailings()
	{
		return $this->hasMany(Mailing::class);
	}

	/**
	 * @return RepairOrder
	 */
	public function orders()
	{
		return $this->hasMany(RepairOrder::class, "customer_id", "id");
	}

	/**
	 * @return Bookmark
	 */
	public function bookmark()
	{
		return $this->hasOne(Bookmark::class, "reference_id", "id")
			->where("type", "customer");
	}

	/**
	 * @return string
	 */
	public function full_name(bool $shortened = false)
	{
		return $this->exists
			? ($shortened ? $this->firstname[0] . ". " : $this->firstname) . " " . $this->lastname
			: "Gelöscht";
	}

	/**
	 * @return string
	 */
	public function full_address()
	{
		return $this->address_line_1 . ", " . $this->postcode . " " . $this->city;
	}

	/**
	 * @return string
	 */
	public function full_address_json()
	{
		return $this->exists
			? '{"address_line_1":"' . $this->address_line_1 . '", "postcode":"' . $this->postcode . '","city":"' . $this->city . '"}'
			: null;
	}

	/**
	 * @return string
	 */
	public function last_activity()
	{
		return Time::ago($this->updated_at ?? $this->created_at);
	}

	/**
	 * @param RepairOrder $RepairOrder
	 * @return bool
	 */
	public function send_order_confirmation(RepairOrder $RepairOrder)
	{

		/**
		 * @var string
		 */
		$type_pronoun = $RepairOrder->type === "bike" ? "dein Fahrrad" : "deine Nähmaschine";

		/**
		 * @var string
		 */
		$subject = "🥳 " . ucfirst($type_pronoun) . " wurde bei uns aufgenommen!";

		/**
		 * @var string
		 */
		$template = "customer-order-confirmation.html";

		/**
		 * @var string|false
		 */
		$body = file_get_contents(_root() . "/app/templates/mail/$template");
		$body = str_replace("{REFERENCE_ID}", $RepairOrder->reference_id, $body);
		$body = str_replace("{REPAIR_TYPE}", $type_pronoun, $body);

		/**
		 * @var bool
		 */
		$Mail = $this->send_mail($subject, $body);

		/**
		 * Mail failed sending?
		 */
		if (!$Mail)
			return $this->error("<strong>Es konnte keine Auftragsbestätigung per Mail gesendet werden.</strong> Checke die Error-Logs für mehr Informationen.");

		return $this->success("<strong>Gesendet!</strong>");
	}

	/**
	 * @param string $subject
	 * @param string $body
	 * @return bool
	 */
	public function send_mail(string $subject, string $body)
	{
		/**
		 * If the customer has not yet entered a mail address, just
		 * return true for now.
		 */
		// TODO: Frontend notification if customer has no mail to add one.
		if (!$this->mail) {
			Logger::to_file(new Exception("Customer has no mail set, should be asked for."), "mail.log");

			return false;
		}

		return (new Mail)->create($this->mail, $subject, $body);
	}

	/**
	 * @return int|string
	 */
	public static function count_without_mail()
	{
		return Customer::whereNull("mail")
			->count();
	}

	/**
	 * @return int|string
	 */
	public static function count_top_city()
	{
		$C = Customer::selectRaw('COUNT(*) as count, city')
			->groupBy('city')
			->orderByDesc('count')
			->limit(1)
			->first();

		return [
			"count" => $C->count,
			"city" => $C->city,
		];
	}
}
