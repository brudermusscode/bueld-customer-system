$(function () {
  /**
   * Hide search box on input blur.
   */
  $(document).on("click", function (e) {
    let search_outer = e.target.closest("[has-search]");
    let current_search = search_outer?.find("[search]");

    /**
     * Close all searches if none is focused.
     */
    if (!search_outer) close_searches();

    /**
     * If any search outer is available, iterate through all tghat
     * are available and close them, only if the one clicked on is
     * not the one being closed.
     */
    if (search_outer) {
      document.find_all("[search]").forEach((search) => {
        if (current_search !== search) {
          hide_search(search);
        }
      });
    }
  });

  /**
   * All keydown events for this killer form. Markus be proud pls.
   */
  $(document).on("keyup", function (e) {
    const key = e.key.toLowerCase();
    const target = e.target;

    /**
     * Disable current active search box on escape.
     */
    if (
      target.tagName.toLowerCase() === "input" &&
      target.closest("[has-search]")
    ) {
      let outer = target.closest("[has-search]");
      let clone = outer.hasAttribute("clone");
      let custom_function = outer.hasAttribute("custom-function");

      /**
       * Hide & clear search if input is empty.
       */
      if (target.value.length < 1) {
        clear(target);
        hide_search(target, true);
      }

      if (key === "escape") {
        hide_search(target, true);
        document.activeElement.blur();
      }

      if (key === "enter" && !custom_function) {
        select_search_element(target, "enter", clone);
      }
    }
  });

  /**
   * Will fill out the minput's visible value and all other
   * references to show the clicked element.
   */
  $(document).on("click", "[search-element]", function (e) {
    let add_clone = false;
    let outer = this.closest("[has-search]");

    /**
     * Any clickable search item that should have their custom
     * behaviour can be excluded by adding a name to the attribute
     * and return here.
     */
    if (outer.hasAttribute("custom-function")) return;
    if (outer.hasAttribute("clone")) add_clone = true;

    select_search_element(this, "click", add_clone);
  });
});

/**
 * Finds the closest element with and attribute of [input] and
 * searches for a element with the attribute [search] from there
 * to activate it and append data.
 *
 * @param {element} closest
 * @param {HTML} append_data
 * @param {bool} clear
 */
export const show_search = (closest, append_data, clear = true) => {
  let input_outer = closest.closest("[input]");
  let search = input_outer?.find("[search]");

  input_outer?.setAttribute("in-front", true);
  input_outer?.activate();
  search?.activate();

  if (clear && search) search.innerHTML = "";
  if (append_data && search) search.innerHTML = append_data;
};

/**
 * Finds the closest element with and attribute of [input] and
 * searches for a element with the attribute [search] from there
 * to deactivate it again and remove it's data.
 *
 * @param {element} closest
 * @param {bool} clear
 */
export const hide_search = (closest, clear = true) => {
  let input_outer = closest.closest("[input]");
  let search = input_outer?.find("[search]");

  input_outer?.removeAttribute("in-front");
  input_outer?.deactivate();
  search?.deactivate();

  if (clear && search) search.innerHTML = "";
};

/**
 * When a target element inside a [search] has been clicked or
 * enter has been pressed in the input related to it, the first
 * and only element will be selected and inserted into all
 * necessary places of this input.
 *
 * @param {element} target
 * @param {string} action
 * @param {bool} add_clone
 */
export const select_search_element = (
  target,
  action = "enter",
  add_clone = false,
) => {
  /**
   * The target has to be a search-element, otherwise return
   */
  if (action === "click" && target.closest("[search-element]") !== target) {
    console.log(
      "%cClick action failure: Target is not a search element",
      "color: red; font-weight: bold;",
    );
    return;
  }

  let outer = target.closest("[input]").parentElement;
  let all_nodes = outer?.find_all("[input]");
  let clone = all_nodes[all_nodes.length - 1].cloneNode(true);
  let visible_input = target.closest("[input]").find("input[type=text]");
  let input = visible_input.closest("[input]").find("input[type=hidden]");
  let search_box = target.closest("[has-search]")?.find("[search]");
  let children_length = outer?.children.length;
  let search =
    action === "click"
      ? target
      : target.closest("[has-search]").find("[search-element]");

  /**
   * Return if there is no search-element.
   */
  if (!search) return;

  let price = search.dataset.price;
  let new_value = search.dataset.value;
  let id = search.dataset.id;

  /**
   * A search for the entered number has been found. We want
   * to clone the searches element's data to the input.
   */
  if (
    (search_box.find_all("[search-element]").length < 2 &&
      action === "enter") ||
    action === "click"
  ) {
    /**
     * Hide the search.
     */
    hide_search(target, true);

    /**
     * Set the invisible input value.
     */
    if (input) input.value = id == 0 ? new_value : id;

    /**
     * Set the visible's input values.
     */
    visible_input.value = search.dataset.value;
    if (price)
      visible_input.closest("[input]").find("[input-extra-text]").innerHTML =
        price;
    visible_input.closest("[input]").find("[input-delete]")?.enable();

    /**
     * Show a positive response for the user, that this field is
     * now checked or marked as done.
     */
    let input_type_icon = visible_input
      .closest("[input]")
      .find("[input-type-icon]");

    if (input_type_icon) {
      input_type_icon.setAttribute("color", "green");
      input_type_icon.setAttribute("bold", "");
      input_type_icon.innerHTML = "done";
    }

    let empty_input;

    /**
     * Check if there is an empty input lingering and focus this.
     */
    if (add_clone) {
      if ((empty_input = outer.find("input[type=hidden][value='']"))) {
        empty_input.closest("[input]").find("input[type=text]").focus();

        /**
         * Append the clone.
         */
      } else if (clone) {
        let clone_search = clone.find("[search]");

        clone.deactivate();
        clone.find_all("input").forEach((inp) => {
          inp.value = "";
        });
        clone_search.deactivate();
        clone_search.innerHTML = "";
        outer.appendChild(clone);

        /**
         * Add a new field.
         */
        setTimeout(() => {
          outer.children[children_length].find("input[type=text]").focus();
        }, 10);
      }
    }
  }
};

/**
 * Clears the values of a target input.
 *
 * @param {element} input
 */
export const clear = (input) => {
  let outer = input.closest("[input]");
  let hidden_input = outer.find("input[type=hidden]");
  let input_type_icon = outer.find("[input-type-icon]");

  /**
   * Clear the actual request inputs value.
   */
  if (hidden_input) hidden_input.value = "";

  /**
   * If icons exist, reset them to add.
   */
  if (input_type_icon) {
    input_type_icon.removeAttribute("color");
    input_type_icon.removeAttribute("bold");
    input_type_icon.innerHTML = "add";
  }
};

/**
 * Closes all available searches for any input on the current page visible.
 */
export const close_searches = () => {
  document.find_all("[has-search]").forEach((input) => {
    hide_search(input);
  });
};
