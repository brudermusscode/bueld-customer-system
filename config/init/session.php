<?php

use Bruder\Application\Session as SessionManager;
use Bruder\Model\Session;
use Bruder\Model\User\User;

/**
 * Initialize a new session.
 */
new SessionManager;

/**
 * User is logged?
 */
define("LOGGED", Session::is_valid());

/**
 * @var ?User
 */
$current_user = User::find(LOGGED?->user_id ?? 0);

global $current_user;

/**
 * @var ?User
 */
define("CURRENT_USER", $GLOBALS["current_user"]);
