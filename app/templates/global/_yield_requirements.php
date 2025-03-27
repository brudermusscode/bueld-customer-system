<!-- Stylesheets -->
<link href="/assets/css/fonts.css" rel="stylesheet">
<link href="/assets/css/normalize@8.0.1.css" rel="stylesheet">

<!-- Javascript -->
<!-- <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script> -->
<script src="/assets/js/jquery@3.7.1.js"></script>

<script>
  const echo = (msg) => {
    console.log(msg);
  };

  document.find = (selector) => {
    return document.querySelector(selector);
  };

  document.find_all = (selector) => {
    return document.querySelectorAll(selector);
  };

  Element.prototype.find = function(selector) {
    return this.querySelector(selector);
  };

  Element.prototype.find_all = function(selector) {
    return this.querySelectorAll(selector);
  };

  Element.prototype.disable = function(selector) {
    return this.setAttribute("disabled", "");
  };

  Element.prototype.enable = function(selector) {
    return this.removeAttribute("disabled");
  };

  Element.prototype.activate = function(selector) {
    return this.setAttribute("active", true);
  };

  Element.prototype.deactivate = function(selector) {
    return this.removeAttribute("active");
  };

  Element.prototype.done = function(selector) {
    return this.setAttribute("done", true);
  };

  Element.prototype.undone = function(selector) {
    return this.removeAttribute("done");
  };

  Element.prototype.set_inactive = function(selector) {
    return this.setAttribute("inactive", false);
  };

  Element.prototype.unset_inactive = function(selector) {
    return this.removeAttribute("inactive", false);
  };

  Element.prototype.make_tag = function(selector) {
    this.setAttribute("tag", "");
    this.enable();
    this.removeAttribute("submit-closest");
    this.removeAttribute("confirm-button");
    this.removeAttribute("submit");
    return;
  };

  Element.prototype.is_activated = function(selector) {
    return this.hasAttribute("active");
  };

  Element.prototype.fade_out = function(selector) {
    return this.setAttribute("invisible", "");
  };

  let __project = {
    environment: "<?php echo current_env(); ?>",
  };

  let __page = {
    current: "<?php echo filter_input(INPUT_GET, "page", FILTER_SANITIZE_SPECIAL_CHARS); ?>",
    marked: "home",
    is_loading: false,
  };

  let __infinite_scroll = {
    page_offset: 1620,
    start: 0,
    limit: 40,
    reached_end: false,
    reached_full_end: false,
  };


  let __current_overlay = null;
  let __current_second_overlay;
  let __current_audio_element;
  let __current_hover_card;


  let __material_button_ripple_effect_remove_interval = 100;
  let __material_button_ripple_effect_done = false;


  let __submit;
  let __submit_timeout = 0;
  let __submit_timeout_delay = 324;

  let __body = document.body;
  let __main = document.find("main");
  let __env = "<?php echo current_env() ?>";
  let __init = false;
</script>

<?php

/**
 * Main JavaScript entry file.
 */
include TEMPLATE . "/global/_bundled_js.php";
