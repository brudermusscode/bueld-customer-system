$(function () {
  $(document).on("click", "mradio radio-option", function (e) {
    let radio = this.closest("mradio");
    let input = radio.find("[radio-input]");
    let value = this.dataset.value;
    let options = radio.find_all("radio-option");
    let unselect = radio.getAttribute("unselect");

    if (!input || typeof value === undefined || value.length < 1) return;

    /**
     * Same is selected as already active? Unselect it!
     */
    if (value === input.value) {
      /**
       * Should be unselectable by default and when the attribute
       * is set. Setting it to false will disable it.
       */
      if (!unselect || unselect == true) {
        this.deactivate();
        input.value = "";
      }

      return;
    }

    /**
     * Unselect all others.
     */
    options.forEach((option) => {
      option.deactivate();
    });

    /**
     * Activate current.
     */
    this.activate();

    /**
     * Set the new input value.
     */
    input.value = value;
  });
});
