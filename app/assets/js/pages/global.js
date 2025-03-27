$(function () {
  /**
   * Toggle switches.
   */
  $(document).on("click", "[set-active]", function (e) {
    let set_active = this.getAttribute("set-active");
    let set_active_element = this.closest(`[${set_active}]`);

    if (!set_active || !set_active_element) return;

    if (!set_active_element.hasAttribute("active")) {
      set_active_element.activate();
    }
  });

  /**
   * Toggle switches.
   */
  $(document).on("click", "[unset-active]", function (e) {
    let unset_active = this.getAttribute("unset-active");
    let unset_active_element = this.closest(`[${unset_active}]`);

    if (!unset_active || !unset_active_element) return;

    if (unset_active_element.hasAttribute("active")) {
      unset_active_element.deactivate();
    }
  });
});
