import * as Frontend from "./frontend";
import * as Page from "./page";

const init_application = async () => {
  console.log("Starting initilalizing app...");

  // let __init_overlay = $(document).find("overlay[loading-app]");
  // let Route = await Page.get_route(window.location.pathname);

  /**
   * Show-header element.
   */
  //  let show_header = document.find("show-header");

  /**
   * Find the header and hide it, if the current page is set
   * to hide it.
   */

  // let __header = document.find("header[sidebar]");
  // if (Route.hide_header === true && !show_header && __header) {
  //   __header.setAttribute("inactive", true);
  // } else if (__header) {
  //   __header.removeAttribute("inactive");
  // }

  // Frontend.extract_exception(document.body);

  /**
   * Workaround:
   * When loading the page first and going to a new page, going
   * back in history won't do anything. We need toi load the page
   * on first page startup but I haven't figured out why by now.
   */
  Page.reload();

  console.log(
    "%c🌞 Framework/Bruder loaded!",
    "color:light-blue;font-size:1.32em;font-weight:800;",
    "\nheia.kim ©️ 2022-" + new Date().getFullYear()
  );
};

$(function () {
  /**
   * Call the init function.
   */
  init_application();

  /**
   * Generalize click event listeners
   */
  document.addEventListener("click", async function (e) {
    let anchor = e.target.closest("a");
    let href;

    /**
     * Close jump menu
     */
    // if (jumper)
    //   jumper.unactivate();

    if (anchor !== null) {
      href = anchor.getAttribute("href");

      if (!anchor.hasAttribute("extern") && href) {
        e.preventDefault();

        await Page.get(
          href,
          false,
          anchor,
          !anchor.hasAttribute("no-scroll-top")
        );
      }
    }
  });

  /**
   * When clicking the history back button in the browser.
   */
  window.addEventListener("popstate", async (e) => {
    if (!e.state) return;

    Frontend.close_overlays();

    if (e.state.href == undefined || !e.state.href) return;

    await Page.get(e.state.href, true);
  });
});
