<?php

namespace Bruder\Model;

use Bruder\Bruder;
use Bruder\Application\Session as SMG;
use Bruder\Application\Cookie;
use Bruder\Http\Request;
use Bruder\Geo\Geo;
use Bruder\Utils\Utils;
use Bruder\Model\User\User;

class Session extends Bruder
{

  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "token",
    "remote_address",
    "browser",
    "browser_version",
    "browser_title",
    "os",
    "os_type",
    "os_title",
    "device_type",
    "city",
    "postal_code",
    "country",
    "region",
    "continent",
    "timezone",
    "deleted_at",
    "updated_at",
  ];

  /**
   * @var array
   */
  public static $persistent_cookies = [
    "USER_ID",
    "USER_TOKEN",
  ];

  /**
   * Just needs the user_id to passed with the $params object.
   *
   * @param object $params
   * @return object
   */
  public function new(object $params)
  {

    /**
     * You could just pass a user id to the params object and
     * login this user easily.
     */
    if (!empty($params->user_id))

      /**
       * @var ?User
       */
      $User = User::find($params->user_id ?? 0);
    else

      /**
       * @var ?User
       */
      $User = User::verify_login($params->username, $params->password);

    /**
     * User exists?
     */
    if (!$User)
      return $this->error("<strong>Username oder Passwort falsch!</strong>");

    /**
     * @var array
     */
    $Geo = $params->geodata ?? (new Geo)->get();

    /**
     * @var object
     */
    $device = Utils::detect_browser();

    /**
     * Unique token
     */
    $token = Utils::random_alpha_token(100);

    /**
     * Session with similiar info exists?
     * @var ?Session
     */
    $Session = $User->sessions()
      ->where([
        "user_id" => $User->id,
        "os" => $device->os_name ?? null,
        "os_type" => $device->os_type ?? null,
        "os_title" => $device->os_title ?? null,
        "device_type" => $device->device_type ?? null,
        "city" => $Geo["city"] ?? null,
        "postal_code" => $Geo["postCode"] ?? null,
        "country" => $Geo["countryCode"] ?? null,
      ])
      ->first();

    /**
     * Either update the existing session or create a new one.
     */
    if ($Session)
      $Session->update([
        "token" => $token,
        "remote_address" => $Geo["request"] ?? null,
        "browser" => $device->browser_name ?? null,
        "browser_version" => $device->browser_version ?? null,
        "deleted_at" => null,
      ]);
    else
      $Session = $User->sessions()
        ->create([
          "token" => $token,
          "remote_address" => $Geo["request"] ?? null,
          "browser" => $device->browser_name ?? null,
          "browser_version" => $device->browser_version ?? null,
          "os" => $device->os_name ?? null,
          "os_type" => $device->os_type ?? null,
          "os_title" => $device->os_title ?? null,
          "device_type" => $device->device_type ?? null,
          "city" => $Geo["city"] ?? null,
          "postal_code" => $Geo["postCode"] ?? null,
          "country" => $Geo["countryCode"] ?? null,
          "region" => $Geo["region"] ?? null,
          "continent" => null,
          "timezone" => $Geo["timezone"] ?? null,
          "updated_at" => null,
        ]);

    /**
     * @var Session $Session
     */

    /**
     * Append to PHP session cookie
     */
    (new SMG)->set('session', $Session->data());

    /**
     * Cookies for persistent session
     */
    Cookie::set('USER_ID', $User->id, "+10 months", false);
    Cookie::set('USER_TOKEN', $token, "+10 months", false);

    /**
     * Cookies for even when logged out.
     */
    Cookie::set('USR0CMB', $User->id, "+10 months", false);
    Cookie::set('TKN0CMB', $token, "+10 months", false);

    return $this->success("Was geht ab, <strong>$User->username</strong>!");
  }

  /**
   * @param object $params
   * @return object
   */
  public function remove(object $params)
  {
    /**
     * @var bool
     */
    $is_current_session = Cookie::get(self::$persistent_cookies[1]) === $params->token;

    /**
     * Äh ja
     */
    if ($is_current_session)
      return self::cancel();

    /**
     * Soft-delete it!
     */
    $this->update([
      "deleted_at" => $this->current_timestamp(),
    ]);

    return $this->success("<strong>Ausgeloggt!</strong>");
  }

  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * @return ?Session
   */
  public static function is_valid()
  {
    /**
     * Persistent cookies exist?
     */
    foreach (self::$persistent_cookies as $cookie)
      if (!Cookie::get($cookie)) {
        self::cancel();

        return null;
      }

    /**
     * Cookies are set, search the database for a matching session.
     */
    $Session = self::where('user_id', Cookie::get(self::$persistent_cookies[0]))
      ->where('token', Cookie::get(self::$persistent_cookies[1]))
      ->whereNull('deleted_at')
      ->first();

    /**
     * Cancel the current session if none was found.
     */
    if (!$Session)
      self::cancel();

    return $Session;
  }

  /**
   * Removes session cookies and session object from PHP Session
   * Cookie and deletes a possible matching instance in the database.
   *
   * @return object
   */
  public static function cancel()
  {
    /**
     * @var Session
     */
    $Session = self::where("user_id", Cookie::get(self::$persistent_cookies[0]))
      ->where("token", Cookie::get(self::$persistent_cookies[1]))
      ->first();

    /**
     * Soft-delete it!
     */
    $Session?->update([
      "deleted_at" => date("Y-m-d H:i:s", time()),
    ]);

    /**
     * Remove session cookies.
     */
    foreach (self::$persistent_cookies as $cookie)
      Cookie::delete($cookie);

    /**
     * Remove session object from PHP Session Cookie.
     */
    (new SMG)->remove('session');

    return (new Request)->error("<strong>Tschüss mein Freund</strong>");
  }

  /**
   * @return string
   */
  public function display()
  {
    return (object) [
      "icon_class" => match (strtolower($this->os ?? "")) {
        "windows" => "ri-microsoft-fill",
        "linux" => "ri-ubuntu-fill",
        "ios" => "ri-apple-fill",
        "android" => "ri-android-fill",
        "macos" => "ri-finder-fill",
        default => "ri-mac-fill",
      },
      "os_full" => $this->os ?? "Unknown OS " . "-" . ucfirst($this->os_type ?? " N/A"),
      "browser_icon_class" => match (strtolower($this->browser ?? "")) {
        "firefox" => "ri-firefox-fill",
        "chrome" => "ri-chrome-fill",
        "edge" => "ri-edge-new-fill",
        "opera" => "ri-opera-fill",
        "safari",
        "safari mobile" => "ri-safari-fill",
        default => "ri-question-fill",
      },
    ];
  }
}
