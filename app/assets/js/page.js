import * as Router from "./router.js";
import * as Utils from "./utils.js";
import * as Frontend from "./frontend.js";
import * as Window from "./window.js";
import * as Cookies from "./cookies.js";
import * as Responder from "./elements/responder.js";
import Overlay from "./elements/overlay.js";

/**
 * Page settings
 */
let scroll_interval;

/**
 * Reloads the page by calling the get function and setting the
 * reload parameter to true.
 */
export const reload = (keep_overlays = false) => {
  get(
    window.location.pathname + window.location.search,
    false,
    null,
    false,
    true,
    keep_overlays
  );
};

/**
 *
 * @param {string} url
 * @returns Router
 */
export const get_route = async (url) => {
  let query_params_split = url.split("?");
  let url_no_query_params = query_params_split[0];
  let url_parameter = url_no_query_params.split("/");

  url_parameter.shift();

  /**
   * Mark home if none.
   */
  if (url_parameter[0] === "") url_parameter[0] = "home";

  let route = url_parameter[0];

  return await Router.router(route);
};

/**
 *
 * @param {string} href
 * @param {boolean} state
 * @param {?string} anchor
 * @param {boolean} scroll_top
 * @param {boolean} reload
 * @param {boolean} keep_overlays
 * @returns void
 */
export const get = async (
  href,
  state = false,
  anchor = null,
  scroll_top = true,
  reload = false,
  keep_overlays = false
) => {
  /**
   * If the page is in loading state, return.
   */
  if (__page.is_loading && !reload) return;
  if (__page.current === "maintenance") return;

  let url = href;

  let main_container = document.querySelector("main");
  // let csrf_token = Utils.get_csrf_token();
  let title;

  /**
   * Serialize the route the user is coming from.
   */
  let coming_from_path = window.location.pathname;
  let coming_from_path_split = coming_from_path.split("/");

  /**
   * Prepare a reload.
   */
  if (reload)
    url =
      url.concat(url.includes("?") ? "&reload=" : "?reload=") +
      random_string(8);
  /**
   * Get the current route.
   */
  let Route = await get_route(url);

  /**
   * Create the object for the history.
   */
  let history_params = {};
  history_params["href"] = url;

  /**
   * Check if the current location is the same as the
   * requested url. Return nothing in this case. Defines a variable
   * is_same_location to use around this function as well as a
   * variable that just figures out if the current location
   * and the destinated one are from the same oruigin page
   */
  let current_location_route = window.location.pathname.split("/").shift()[0];
  let is_same_location =
    window.location.pathname.concat(window.location.search) == url;
  let is_same_history_route = url == current_location_route;

  /**
   * Return nothing if the current location is the same as the
   * requested one.
   */
  if (!state && is_same_location) return;

  /**
   * Create a new overlay only when the app is being initialized.
   * Otherwise there would be two overlays lingering.
   */
  if (document.find("[loading-app]")) new Overlay(null, false);

  /**
   * Begin the new page load.
   */
  Frontend.load();

  /**
   * Scroll to the top if the param is set to true.
   */
  if (scroll_top) Window.scroll_to_top();

  /**
   * Get CSRF token.
   */
  const __csrf_token = document
    .find("head meta[name=csrf_token]")
    .getAttribute("content");

  $.ajax({
    url: url,
    method: "GET",
    contentType: false,
    processData: false,
    success: async function (data) {
      /**
       * Close overlays if.
       */
      if (!keep_overlays) Frontend.close_overlays();

      /**
       * Scroll to the top.
       */
      if (scroll_top && !state) window.scrollTo(0, 0);

      /**
       * Update the page global.
       */
      __page.current = Route.key;

      /**
       * Update main content.
       */
      main_container.innerHTML = data;

      /**
       * Extract the title.
       * @var string
       */
      title = main_container.find("title")?.innerHTML;

      /**
       * Pushes the coming state to the browser history and sets a proper
       * title to the document.
       */
      if (!state && !reload) {
        history.pushState(history_params, title, url);
        document.title =
          title !== undefined && title !== null && title
            ? title
            : "Unknown Page";
      }

      /**
       * Stop playing audio.
       */
      if (__current_audio_element) {
        __current_audio_element.pause();
        __current_audio_element.remove();
        __current_audio_element = null;
      }

      /**
       * For each page load, we need to reset the infinite scrolling variables
       * to let the new page, if available, can access those freshly and calculate
       * when to add new items
       */
      __infinite_scroll.start = 0;
      __infinite_scroll.reached_end = false;
      __infinite_scroll.reached_full_end = false;

      clearInterval(scroll_interval);

      /**
       * Set header to scrolled.
       */
      if (window.scrollY >= 20)
        document.find("[scroll-manipulated]")?.setAttribute("scrolled", "true");

      /**
       * Check for an exception and move it to a direct child of
       * the body to be present in the very foreground.
       */
      Frontend.extract_exception(main_container);

      /**
       * If a redirect exists, redirect to the page inside the to attribute.
       */
      let redirector = main_container.find("redirect");
      if (redirector && redirector.hasAttribute("to"))
        return window.location.replace(redirector.getAttribute("to"));

      /**
       * Autofocus any input that has the attribute.
       */
      if (document.find("[autofocus]")) document.find("[autofocus]").focus();

      /**
       * Execute once function will only be fired when first
       * accessing a new page, which is determined by the body
       * carrying an attribute named after the route itself.
       */
      if (
        typeof Route.execute_once === "function" &&
        !document.body.hasAttribute(Route.key)
      )
        Route.execute_once(url);

      /**
       * Remove all route attributes from any section where it will be
       * added to.
       */
      Object.keys(Router.routes).forEach((index) => {
        document.body.removeAttribute(index);
      });

      /**
       * Set route attributes.
       */
      document.body.setAttribute(
        Route.key == ""
          ? "home"
          : Route.body_attribute !== undefined
            ? Route.body_attribute
            : Route.key,
        ""
      );

      /**
       * Free the clicking on other links by disabling page loading.
       */
      Frontend.unload();

      /**
       * Load dynamic content.
       */
      Frontend.get_content();

      /**
       * Eval all script tags.
       */
      main_container.find_all("script").forEach((script) => {
        eval(script.innerHTML);
      });

      /**
       * Deactivate all main navigation buttons.
       */
      document.find_all("[page]").forEach((button) => {
        button.deactivate();
      });

      /**
       * Set main navigation button to activate.
       */
      if (Route.mark)
        document
          .find_all(`[page=${Route.mark}]`)
          ?.forEach((button) => button.activate());
      else
        document
          .find_all(`[page=${Route.key}]`)
          ?.forEach((button) => button.activate());

      // Set the current page to be marked.
      // __page.marked = Route.mark ? Route.mark : route;

      // The previous Router, basically the router for the page
      // the user is coming from.
      let PreviousRoute = await Router.router(coming_from_path_split[1]);

      /**
       * Show-header element.
       */
      let show_header = document.find("show-header");

      /**
       * Find the header and hide it, if the current page is set
       * to hide it.
       */
      let __header = document.find("sub-menu");
      if (Route.hide_header === true && !show_header && __header) {
        console.log("%cHeader should be hidden", "color: green");
        __header.setAttribute("inactive", true);
      } else if (document.find("hide-header")) {
        console.log("%cHeader should be hidden", "color: green");
        __header.setAttribute("inactive", true);
      } else if (__header) {
        __header.removeAttribute("inactive");
      }

      /**
       * Set the body to initialized.
       */
      document.body.setAttribute("initialized", true);

      /**
       * If a hashtag is at the end of url, search for the
       * corresponding container and scroll it into the view.
       */
      let url_has_hashtag = url.includes("#");
      let hashtag_id = url_has_hashtag ? url.split("#")[1] : null;

      if (hashtag_id) {
        main_container
          .find(`#${hashtag_id}`)
          ?.scrollIntoView({ behavior: "smooth" });
      }

      /**
       * Load in all images with a nice effect.
       */
      Frontend.reload_images();

      return true;
    },
    error: function (error) {
      Frontend.ajax_error(error);
    },
  });
};

export const get_component = async (
  react,
  url,
  data = null,
  empty_container = false,
  where = "top"
) => {
  $.ajax({
    url: url,
    data: data,
    method: "GET",
    processData: false,
    contentType: "JSON",
    success: function (data) {
      if (!data.status)
        return new Responder.Responder().add(
          document.body,
          data.message,
          "error"
        );

      if (empty_container) {
        react.innerHTML = data;
      } else {
        if (where === "top") react.insertAdjacentHTML("afterbegin", data);
        else react.insertAdjacentHTML("beforeend", data);
      }

      Frontend.reload_images();
    },
    error: function (data) {
      new Responder.Responder().add(
        document.body,
        data.message,
        "error",
        "user"
      );
    },
  });
};

export const objectify_query_params = (query) => {
  if (!query) return;

  query = query.replace("?", "");

  let params = query.split("&");

  if (!params[0] && params.length > 0) params.shift();

  let obj = {};

  params.forEach((param) => {
    let ass = param.split("=");
    obj[ass[0]] = ass[1];
  });

  return obj;
};

export const disable_scroll = () => {
  let TopScroll = window.pageY || document.documentElement.scrollTop;
  let LeftScroll = window.pageX || document.documentElement.scrollLeft;

  window.onscroll = function () {
    window.scrollTo(LeftScroll, TopScroll);
  };
};

export const enable_scroll = () => {
  window.onscroll = function () {};
};

Utils.delegate(
  document,
  "click",
  "[data-action='legal:consent,forward']",
  function (e) {
    let page = this.dataset.legalPage;

    Cookies.set("POLICIES_CONSENT_STEP", page, 365);
  }
);

export const random_string = (length) => {
  const characters =
    "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  let randomString = "";

  for (let i = 0; i < length; i++) {
    const randomIndex = Math.floor(Math.random() * characters.length);
    randomString += characters.charAt(randomIndex);
  }

  return randomString;
};

/**
 * Create an install app prompt.
 */
$(function () {
  window.addEventListener("beforeinstallprompt", (event) => {
    // Prevent Chrome 76 and later from showing the automatic install prompt
    event.preventDefault();

    // Store the event for later use
    let deferredPrompt = event;

    // Show your own custom install button
    // For example, you can have an "Install" button on your webpage
    const installButton = document.find("installButton");

    if (installButton) {
      installButton.style.display = "block";

      installButton?.addEventListener("click", () => {
        // Trigger the deferred prompt
        deferredPrompt.prompt();

        // Wait for the user to respond to the prompt
        deferredPrompt.userChoice.then((choiceResult) => {
          if (choiceResult.outcome === "accepted") {
            console.log("User accepted the install prompt");
          } else {
            console.log("User dismissed the install prompt");
          }

          // Reset the deferred prompt
          deferredPrompt = null;
          installButton.style.display = "none";
        });
      });
    }
  });
});
