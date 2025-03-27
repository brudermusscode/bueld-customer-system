<?php

namespace Bruder\Exception;

use Exception;
use Bruder\Application;

class AuthorizationException extends Exception
{
  public function __construct($message = "Authorization Error", $code = 0, ?Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
    Application\Logger::to_file($this, "authorization_errors.log");
  }
}
