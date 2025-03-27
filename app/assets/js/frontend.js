import * as Image from "./images";
import * as Responder from "./elements/responder";

/**
 * KEYBOARD SHORTCUT
 */
document.addEventListener("keyup", (e) => {
  if (!e.key) return;

  /**
   * ESC
   */
  if (e.key.toLowerCase() === "enter") {
    if (
      document.activeElement.tagName.toLowerCase() === "mbutton" &&
      !document.activeElement.hasAttribute("disabled")
    )
      document.activeElement.click();

    return;
  }
});

/**
 * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
 * ? Frontend state
 * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
 */
export const load = async () => {
  __page.is_loading = true;

  let overlay = document.find("page-loader");

  if (overlay) overlay.setAttribute("visible", true);
};

export const unload = async () => {
  __page.is_loading = false;

  let overlay = document.find("page-loader");

  setTimeout(() => {
    if (overlay) overlay.setAttribute("visible", false);
  }, 10);
};

export const reload_images = async () => {
  const images = document.find_all("img");
  await Image.load_images(images);
};

/**
 * Creates a new responder with default values set.
 */
export const create_responder = (
  message,
  status = "error",
  append_to = document.body
) => {
  if (
    typeof message === "object" &&
    message !== null &&
    !Array.isArray(message)
  )
    new Responder.Responder().add(
      append_to,
      message?.message ?? "No message",
      message?.status ? "success" : "error"
    );
  else new Responder.Responder().add(append_to, message, status);
};

/**
 * Closes all open overlays.
 */
export const close_overlays = () => {
  let overlays = document.querySelectorAll("overlay");

  if (!overlays) return;

  overlays.forEach((overlay) => {
    overlay.removeAttribute("visible");

    setTimeout(() => {
      overlay.remove();

      if (
        document.body.hasAttribute("toggled") &&
        document.body.getAttribute("toggled") == "true"
      )
        document.body.setAttribute("toggled", "false");
    }, 400);
  });

  __current_overlay = null;
  __current_second_overlay = null;
};

export const close_exception_overlay = () => {
  document.find("exception-container")?.remove();
};

/**
 * Extract an exception from the incoming reponse either as text
 * or HTML already.
 */
export const extract_exception = (from) => {
  if (
    from &&
    !(from instanceof Element) &&
    from.includes("exception-container")
  )
    document.body.insertAdjacentHTML("beforeend", from);
  else if (from && !(from instanceof Element)) return;

  let exception = document.find("exception-container");

  if (exception) {
    exception.remove();
    document.body.appendChild(exception);
  }
};

/**
 * Default error behaviour on an ajax request.
 */
export const ajax_error = (error) => {
  unload();

  /**
   * Activate all submit buttons, so the user can try again.
   */
  let buttons = document.find_all("[submit-closest]");
  if (buttons)
    buttons.forEach((button) => {
      button.enable();
    });

  extract_exception(error.responseText);
  create_responder(error.statusText, "error");
};

/**
 * Finds all <get-content></get-content> elements and loads the
 * content from the specified from attribute dynamically.
 */
export const get_content = () => {
  let get_contents = document.find_all("get-content");
  let from;
  let method;

  get_contents.forEach((getc) => {
    from = getc.getAttribute("from");
    method = getc.getAttribute("method");

    if (from)
      $.ajax({
        url: from,
        method: method === "POST" ? "POST" : "GET",
        contentType: false,
        processData: false,
        success: function (data) {
          getc.innerHTML = data;
          Frontend.reload_images();
          $.globalEval($(getc).find("script").text());
        },
        error: function (error) {
          Frontend.ajax_error(error);
        },
      });
  });
};

$(function () {
  $(document).on("click", "[choose-file]", function (e) {
    let input = this.find("input[type=file]");

    if (!input) return;

    input.click();
  });

  $(document).on(
    "change",
    "[choose-file='user:edit:logo'] input[type=file]",
    function (e) {
      let file = e.target.files[0];
      let image = document.find("[user-logo] img");

      if (file && file.type.startsWith("image/")) {
        const Reader = new FileReader();

        Reader.onload = function (e) {
          image.src = e.target.result;
          image.closest("picture").setAttribute("modified", true);
        };

        Reader.readAsDataURL(file);
      }
    }
  );

  /**
   * Toggle switches.
   */
  $(document).on("click", "toggle-switch", function (e) {
    let input = this.find('input[type="hidden"]');
    let toggle_active = this.getAttribute("toggle-active");
    let toggle_active_element = this.closest(`[${toggle_active}]`);

    let toggle_name = this.dataset.toggle;
    let toggle_closest = this.closest(`${this.dataset.toggleClosest}`);

    if (this.getAttribute("toggled") === "false") {
      this.setAttribute("toggled", true);
      if (input) input.value = "1";
      toggle_active_element?.activate();

      /**
       * Toggle the field visible correspondent to the toggle name
       * on this element.
       */
      if (toggle_name) {
        toggle_closest
          ?.find(`[data-field="${toggle_name}"]`)
          ?.removeAttribute("disn");
      }
    } else {
      this.setAttribute("toggled", false);
      if (input) input.value = "0";
      toggle_active_element?.deactivate();

      /**
       * Toggle the field invisible correspondent to the toggle
       * name on this element.
       */
      if (toggle_name) {
        toggle_closest
          ?.find(`[data-field="${toggle_name}"]`)
          ?.setAttribute("disn", "");
      }
    }
  });

  /**
   * Close all dangling overlays on click.
   */
  $(document).on("click", "[o-closer], [close-overlay]", function () {
    close_exception_overlay();
    close_overlays();
  });

  /**
   * KEYBOARD SHORTCUT
   */
  document.addEventListener("keyup", (e) => {
    if (!e.key) return;

    /**
     * ESC
     */
    if (e.key.toLowerCase() === "escape") {
      let has_overlays = document.find_all("overlay");

      if (has_overlays) close_overlays();

      return;
    }
  });

  /**
   * Generalize keypress events
   */
  document.addEventListener("keypress", function (e) {
    if (!e.key) return;

    if (e.key.toLowerCase() === "enter") e.preventDefault();
    if (
      e.key.toLowerCase() === "enter" &&
      (e.target.tagName.toLowerCase() === "input" ||
        e.target.tagName.toLowerCase() === "textarea") &&
      e.target.hasAttribute("enter-submitable")
    ) {
      e.preventDefault();

      let form = e.target.closest("form");
      let button = form.querySelector("[submit-closest]");
      if (form && button && !button.hasAttribute("disabled")) button.click();
    }
  });

  /**
   * Submit closest form when clicked.
   */
  $(document).on("click", "[submit-closest]", function (e) {
    let form = this.closest("form");

    if (!form) return;

    let submit_button = form.querySelector("button[type='submit']");

    if (!submit_button) {
      submit_button = document.createElement("button");
      submit_button.setAttribute("type", "submit");
    }

    form.appendChild(submit_button);
    form.querySelector("button[type='submit']").click();

    if (this.hasAttribute("confirm-submit-button"))
      this.removeAttribute("submit-closest");
  });

  /**
   * Activate scroll manipulations on elements with [scroll-manipulated] attribute.
   */
  document.addEventListener("scroll", (e) => {
    let $scroll_container = document.body.querySelectorAll(
      "[scroll-manipulated]"
    );

    if (!$scroll_container[0]) return;

    if (
      document.documentElement.scrollTop >= 40 ||
      document.body.scrollTop >= 40
    ) {
      $scroll_container.forEach((s) => {
        s.setAttribute("scrolled", true);
      });
    } else {
      $scroll_container.forEach((s) => {
        s.setAttribute("scrolled", false);
      });
    }
  });

  /**
   * Choose element - Basically a radio element but cooler.
   */
  $(document).on("click", "[chooser] [chooser-option]", function (e) {
    let input = this.closest("[chooser]").find("[chooser-input]");
    let chooser = this.closest("[chooser]");
    let option_value = this.getAttribute("chooser-option");
    let options = chooser.find_all("[chooser-option]");

    if (option_value) input.value = option_value;

    options.forEach(function (option) {
      option.deactivate();
    });

    this.activate();
  });
});
