<?php

use Bruder\Env\Env;

/**
 * Standard ini values for error logging.
 */
ini_set("log_errors", 1);
ini_set("error_log", _env("LOG_PATH") . "/php_errors.log");
error_reporting(E_ALL & ~E_DEPRECATED);

/**
 * Set error displaying based on current evnironment.
 */
if (current_env() == "dev" && !_env("STAGING")) {
  ini_set("display_errors", 1);
  ini_set("display_startup_errors", 1);
} else {
  ini_set("display_errors", 0);
  ini_set("display_startup_errors", 0);
}

/**
 * Reformat the php exception message.
 */
set_exception_handler(function ($ex) {
  header("HTTP/1.0 400 Bad request");

  $type = get_class($ex);
  $time = date('d.m.Y<;>H:i:s');

  /**
   * Log to the default log file defined for the local environment.
   */
  error_log("\n$time<;>$type<;>{$ex->getMessage()}<;>{$ex->getFile()}:{$ex->getLine()}<;>{$ex->getTraceAsString()}<&>\n");

  /**
   * Only show the exceptions in dev env.
   */
  if (current_env() !== "dev" && !_env("STAGING"))
    return;

  /**
   * Style the exception screen.
   */
  echo "<style>";
  echo <<<CSS
    [flcol] {
      display: flex;
      flex-direction: column;
    }

    [flcenter] {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    [justcontcent] {
      display: flex;
      justify-content: center;
    }

    [alitcent] {
      display: flex;
      align-items: center;
    }

    [fltr],
    [flex-truncate] {
      min-width: 0;
      flex: 1;
    }

    [fl] {
      display: flex;
    }

    [fldircol] {
      flex-direction: column;
    }

    [alic] {
      align-items: center;
    }

    [aliend] {
      align-items: flex-end;
    }

    [alistart] {
      align-items: flex-start;
    }

    [alistretch] {
      align-items: stretch;
    }

    [flbetween] {
      justify-content: space-between;
    }

    [jucc] {
      justify-content: center;
    }

    [jucend] {
      justify-content: flex-end;
    }

    [jucstart] {
      justify-content: flex-start;
    }

    [jucstretch] {
      justify-content: stretch;
    }

    [jucsb] {
      justify-content: space-between;
    }

    [jucse] {
      justify-content: space-evenly;
    }

    [jucsa] {
      justify-content: space-around;
    }

    [flex-wrap="wrap"] {
      flex-wrap: wrap;
    }

    [flex-wrap="nowrap"] {
      flex-wrap: nowrap;
    }

    [flexone],
    [flone] {
      flex: 1;
    }

    [text][smoler] {
      font-size: 0.48em;
    }

    [text][smol] {
      font-size: 0.72em;
    }

    [text][std] {
      font-size: 0.9em;
    }

    [text][midler] {
      font-size: 1.2em;
    }

    [text][midlerer] {
      font-size: 1.4em;
    }

    [text][mid] {
      font-size: 1.6em;
    }

    [text][wide] {
      font-size: 2.4em;
    }

    [text][wider] {
      font-size: 3.2em;
    }

    [text][widerer] {
      font-size: 4.2em;
    }

    [text][max] {
      font-size: 5.2em;
    }

    [text][medium] {
      font-weight: 500;
    }

    [text][semi-bold] {
      font-weight: 700;
    }

    [text][bold] {
      font-weight: 800;
    }

    [text][no-text-shadow] {
      text-shadow: none;
    }

    [text][uppercase] {
      text-transform: uppercase;
    }

    [slight] {
      opacity:.6;
    }

    * {
      margin:0;
      padding:0;
    }

    exception-container {
      display:block;
      content: "";
      position:fixed;
      top:0;
      left:0;
      height:100svh;
      width:100vw;
      z-index:200000010;

      background: #362929;
      color: #FFDAD9;
      font-size: 100%;
      font-family: 'Segoe UI', sans-serif;
    }
  CSS;
  echo "</style>";

  /**
   * @var string
   */
  $stacktrace = str_replace("#", "<br>#", $ex->getTraceAsString());

  /**
   * Return the nice newly formatted exception screen!
   */
  echo <<<HTML

    <exception-container fl fldircol jucc alic pv24>
      <div o-closer hoverable circled style="position:fixed;top:1.2em;right:1.2em;">
        <mi wide>close</mi>
      </div>
      <div style="min-width:600px;max-width:1200px;">
        <p text wider semi-bold style=margin-bottom:24px;text-align:center;>{$type}</p>
        <div style="background:#333333;padding:42px 42px 32px 42px;border-radius:42px 42px 0 0;gap:1.2em;" fl jucsb alistart>
          <p text>{$ex->getFile()}</p>
          <p text std style="background:#FDB2A7;padding:4px 8px;border-radius:12px;color:#670002;">Line <strong>{$ex->getLine()}</strong></p>
        </div>

        <div style="background:#5C3F3F;padding:32px 42px 12px;gap:1.2em;" fl fldircol>
          <p text mid>{$ex->getMessage()}</p>
        </div>

        <div style="background:#5C3F3F;padding:0 42px 42px;border-radius:0 0 42px 42px;gap:1.2em;" fl fldircol>
          <p text std>{$stacktrace}</p>
        </div>
      </div>
    </exception-container>

  HTML;
});

/**
 * Reformat the php exception message.
 */
set_error_handler(function (
  int $errno,
  string $errstr,
  ?string $errfile = null,
  ?int $errline = null,
  ?array $errcontext = null
) {
  /**
   * @var string
   */
  $type = match ($errno) {
    E_ERROR => "PHP Error",
    E_NOTICE => "PHP Notice",
    E_WARNING => "PHP Warning",
    E_STRICT => "PHP Strict",
    E_PARSE => "PHP Parse Error",
    default => "Unknown Error Type"
  };

  /**
   * @var string
   */
  $time = date("d.m.Y<;>H:i:s");

  /**
   * Log to the default log file defined for the local environment.
   */
  error_log("\n{$time}<;>{$type}<;>{$errstr}<;>{$errfile}:{$errline}<&>\n");

  /**
   * Only show the errors in dev env.
   */
  if (current_env() !== "dev" && !_env("STAGING"))
    return;

  echo "<span color=company><strong>{$type}</strong><br> $errstr<br>📁 {$errfile}:{$errline}</span>";
});
