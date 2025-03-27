<?php

/**
 * BUILD
 * VALIDATION > GERMAN ADDRESS
 *
 * PHP class to validate german address with www.postdirekt.de/plzserver
 * Calls https://www.postdirekt.de/plzserver/ to validate address
 *
 * Author: ojooss
 * Copyright 2019
 * Refers to https://github.com/ojooss/german-address-validation/blob/master/GermanAddressValidation.php
 */

namespace Bruder\Validate;

class GermanAddress
{

  const URL = 'https://www.postdirekt.de/plzserver/PlzAjaxServlet';

  /**
   * @var string
   */
  public $lastResult;

  /**
   * Builds a request by calling the URL constant and passing
   * params based on the function called.
   *
   * @param array $postfields
   * @return object
   * @throws Exception
   */
  protected function request(array $postfields)
  {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, self::URL);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postfields));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    if (current_env() === "dev") {
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }

    $this->lastResult = curl_exec($curl);

    if ($this->lastResult === false)
      return 'Invalid server response';

    $errno = curl_errno($curl);
    $error = curl_error($curl);

    curl_close($curl);

    if ($errno > 0)
      return "Some error: " . $error;

    $json = json_decode($this->lastResult);

    if (is_null($json))
      return "Nothing found";

    if (!$json->success)
      return "Unsuccessful request";

    return $json;
  }

  /**
   * @param string $city
   * @param string $street
   * @param string
   * @return stdClass[]
   * @throws Exception
   */
  public function find_by(string $street, string $city = "", string $postcode = "")
  {

    /**
     * @var string
     */
    $finda = empty($city) || empty($postcode) ? "adv" : "plz";

    /**
     * Using finda => 'adv' requests the advanced search of the
     * PLZ API which allows to search addresses by only postcode
     * or city name.
     */
    $postFields = [
      'finda' => $finda,
      $finda . '_city' => $city,
      $finda . '_plz' => $postcode,
      $finda . '_street' => $street,
      'lang' => 'de_DE'
    ];

    $result = $this->request($postFields);

    if (isset($result->rows))
      return $result->rows;
    else
      return [];
  }
}
