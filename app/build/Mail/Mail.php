<?php

namespace Bruder\Mail;

use Bruder\Application\Logger;
use PHPMailer\PHPMailer\PHPMailer;

class Mail
{
  /**
   * Creates a new object of an email and try/catches the sending
   *
   * @param string $address E-Mail Address to send to
   * @param string $subject What subject is it?
   * @param string $body The Mail body. Can be HTML file too!
   * @return boolean
   */
  public function create($address, $subject, $body, ?string $from_mail = null, ?string $from_name = null, bool $debug = false)
  {
    $private_key = _root() . "/config/keys/DKIM.key";
    $mail = new PHPMailer(true);

    if (!file_exists($private_key) && current_env() !== "dev")
      return false;

    /**
     * Important to set for PHPMailer
     */
    date_default_timezone_set('Europe/Berlin');

    try {

      /**
       * Mail-Server configuration
       */
      $mail->isSMTP();
      $mail->Host = _env("MAIL_HOST");
      $mail->Port = _env("MAIL_PORT");
      $mail->Priority = 1;
      $mail->SMTPAuth = _env("MAIL_ENABLE_AUTH");
      $mail->Username = _env("MAIL_USERNAME");
      $mail->Password = _env("MAIL_PASSWORD");

      /**
       * Debug settings.
       */
      if ($debug) {
        $mail->Debugoutput = "echo";
        $mail->SMTPDebug = 4; // Enable full debug output
      }

      /**
       * Appearance and receipient
       */
      $mail->setFrom(
        $from_mail ?? _env("MAIL_FROM_MAIL"),
        $from_name ?? _env("MAIL_FROM_NAME")
      );
      $mail->Subject = $subject;
      $mail->addAddress($address);

      /**
       * Set encoding & charset for proper displaying.
       */
      $mail->CharSet = "UTF-8";
      $mail->Encoding = "base64";

      if (current_env() !== "dev") {
        /**
         * Encoding
         */
        $mail->AddCustomHeader("X-MSMail-Priority: High");
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        /**
         * DKIM
         */
        $mail->DKIM_domain = _env("DOMAIN");
        $mail->DKIM_selector = _env("MAIL_DKIM_SELECTOR");
        $mail->DKIM_private = $private_key;
        // $mail->DKIM_passphrase = _env("MAIL_DKIM_PASSPHRASE");
        $mail->DKIM_identity = $mail->From;
      }

      /**
       * Content
       */
      $mail->isHTML(true);
      $mail->Body = $body;
      // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

      $mail->send();

      return true;
    } catch (\Exception $e) {

      /**
       * Log to mail.log
       */
      Logger::to_file($e, "mail.log");

      return false;
    }
  }
}
