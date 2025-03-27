$(function () {
  /**
   * When clicked, evaluate if it was the model itself or the
   * dropdown option.
   */
  $(document).on("click", "mselect", function (e) {
    if (this.hasAttribute("disabled")) return;

    let dropdown = this.querySelector("mselect-dropdown");
    let dropdown_size = dropdown.querySelector("[get-size]");
    let height = dropdown_size.clientHeight;
    let width = dropdown_size.clientWidth;
    let type = this.getAttribute("mselect-type");

    if (!e.target.closest("mselect-dropdown")) {
      dropdown.setAttribute("active", "");
      dropdown.style.height = height + "px";

      setTimeout(() => {
        dropdown.style.width = width + "px";

        setTimeout(() => {
          dropdown_size.setAttribute("active", "");
        }, 320);
      }, 60);
    }

    if (
      e.target.closest("mselect-option") ||
      e.target.tagName === "mselect-option"
    ) {
      dropdown.removeAttribute("active");
      dropdown.removeAttribute("style");
      dropdown_size.removeAttribute("active");

      let change_visible_value = e.target.closest(
        "[mselect-change-visible-value]"
      )
        ? e.target.closest("[mselect-change-visible-value]")
        : e.target;
      let visible_value = this.querySelector("[mselect-visible-value]");
      let change_input_value = change_visible_value.getAttribute(
        "mselect-input-value"
      );
      let input = this.querySelector("input[mselect-input]");

      /**
       * Each type
       */
      if (type == "input-visible") {
        visible_value.innerHTML = change_visible_value.innerHTML;
        input.value = change_input_value;
      } else if (type == "visible") {
        visible_value.innerHTML = change_visible_value.innerHTML;
      }
    }
  });

  $(document).on("click", function (e) {
    let selects = document.find_all("mselect");

    if (!e.target.closest("mselect") && selects.length > 0) {
      console.log("select!");

      selects.forEach((element) => {
        close_select(element);
      });
    }

    if (e.target.closest("mselect")) {
      selects.forEach((element) => {
        if (element !== e.target.closest("mselect")) close_select(element);
      });
    }
  });

  /**
   * Close all when clicked outside.
   */
  document.addEventListener("click", function (e) {
    const target = e.target;
    const selects = document.querySelectorAll("mselect");

    if (!target.matches("mselect") && !target.closest("mselect")) {
      /**
       * Close the dropdown
       */
      selects.forEach((element) => {
        close_select(element);
      });
    }
  });
});

const close_select = (select) => {
  if (!select.hasAttribute("disabled")) {
    select.querySelector("mselect-dropdown")?.removeAttribute("active");
    select.querySelector("mselect-dropdown")?.removeAttribute("style");
    select.querySelector("[get-size]")?.removeAttribute("active");
  }
};
