import * as Frontend from "../frontend";
import * as Page from "../page";
import * as MaterialInput from "../elements/minput";

$(function () {
  $(document).on(
    "click",
    "[data-action='leasing-company:select']",
    function (e) {
      let button = this;
      let section = this.closest("section");
      let inspection_id = section?.find("[leasing-inspection]");
      let requires_inspection_id = this.hasAttribute("requires-inspection-id");

      if (requires_inspection_id) inspection_id?.removeAttribute("disn");
      else inspection_id?.setAttribute("disn", "");
    }
  );

  /**
   * Create a new repair order and redirect to the next page.
   *
   * @action POST
   * @controller repair/order/create
   */
  $(document).on("submit", "[data-form='repair:order:create']", function (e) {
    e.preventDefault();

    Frontend.load();

    setTimeout(() => {
      let formdata = new FormData(this);
      let url = "/" + this.dataset.form.replaceAll(":", "/");

      $.ajax({
        url: url,
        method: "POST",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (data) {
          Frontend.unload();

          if (data.status) {
            Page.get(
              `/repair/order/edit/${data.data.order_id}/employee#activeObject`
            );
          } else {
            Frontend.create_responder(data.message, "error");
          }
        },
        error: function (error) {
          Frontend.ajax_error(error);
        },
      });
    }, 120);
  });

  /**
   * Delete an order.
   *
   * @action DELETE
   * @controller repair/order/delete
   */
  $(document).on("click", "[data-action='repair:order:delete']", function (e) {
    e.preventDefault();

    let url = "/" + this.dataset.action.replaceAll(":", "/");
    let id = this.dataset.id;
    let formdata = new FormData();

    console.log(formdata);

    formdata.append("id", id);

    Frontend.load();

    $.ajax({
      url: url,
      method: "POST",
      data: formdata,
      contentType: false,
      processData: false,
      success: function (data) {
        Frontend.unload();
        Frontend.create_responder(data);

        if (data.status) {
          Page.get(`/orders`);
        }
      },
      error: function (error) {
        Frontend.ajax_error(error);
      },
    });
  });

  /**
   * This will create an invoice, downloads and opens it in a new
   * window as well as updating the order to be finished.
   *
   * @action UPDATE
   * @controller repair/order/finish
   */
  $(document).on("submit", '[data-form="repair:order:finish"]', function (e) {
    e.preventDefault();

    let button = this.find("[submit-closest]");
    let formdata = new FormData(this);
    let url = "/" + this.dataset.form.replaceAll(":", "/");

    Frontend.load();
    button.disable();

    $.ajax({
      url: url,
      data: formdata,
      method: "POST",
      contentType: false,
      processData: false,
      success: function (data) {
        button.enable();
        Frontend.unload();

        if (data.status) {
          console.log(data);

          const byteCharacters = atob(data.data);
          const byteNumbers = new Uint8Array(byteCharacters.length);
          for (let i = 0; i < byteCharacters.length; i++) {
            byteNumbers[i] = byteCharacters.charCodeAt(i);
          }
          const blob = new Blob([byteNumbers], { type: "application/pdf" });
          const url = URL.createObjectURL(blob);
          window.open(url, "_blank"); // PDF im neuen Tab öffnen

          Page.reload();
        }

        Frontend.create_responder(data);
      },
      error: function (error) {
        button.enable();
        Frontend.ajax_error(error);
      },
    });
  });

  /**
   * Creates a new part for an order and appends the returned data
   * to the repair order parts container.
   *
   * @action CREATE
   * @controller repair/order/part/create
   */
  $(document).on(
    "submit",
    '[data-form="repair:order:part:create"]',
    function (e) {
      e.preventDefault();

      let button = this.find("[submit-closest]");
      let formdata = new FormData(this);
      let url = "/" + this.dataset.form.replaceAll(":", "/");

      Frontend.load();
      button.disable();

      $.ajax({
        url: url,
        data: formdata,
        method: "POST",
        contentType: false,
        processData: false,
        success: function (data) {
          button.enable();
          Frontend.unload();

          if (data.status) {
            // Page.reload();
            Frontend.close_overlays();
            document
              .find("section[order-parts] [list]")
              ?.insertAdjacentHTML("beforeend", data.data);
          } else Frontend.create_responder(data);
        },
        error: function (error) {
          button.enable();
          Frontend.ajax_error(error);
        },
      });
    }
  );

  /**
   * Delete a part from a repair order.
   *
   * @action DELETE
   * @controller repair/order/part/delete
   */
  $(document).on(
    "click",
    '[data-action="repair:order:part:delete"]',
    function (e) {
      e.preventDefault();

      let button = this;
      let formdata = new FormData();
      let url = "/" + this.dataset.action.replaceAll(":", "/");
      let id = this.dataset.id;

      formdata.append("id", id);

      Frontend.load();
      button.disable();

      $.ajax({
        url: url,
        data: formdata,
        method: "POST",
        contentType: false,
        processData: false,
        success: function (data) {
          button.enable();
          Frontend.unload();

          console.log(data);

          if (data.status) {
            // Page.reload();
          } else Frontend.create_responder(data);
        },
        error: function (error) {
          button.enable();
          Frontend.ajax_error(error);
        },
      });
    }
  );

  /**
   * Searches for all customers with the input string matching
   * firstname &|| lastname
   *
   * @action GET
   * @controller customer/search
   */
  $(document).on("input", "[data-action='repair:type:search']", function (e) {
    let input = this;
    let value = this.value;
    let search_box = this.closest("[has-search]")?.find("[search]");
    let type = this.closest("form")?.find("input[name=type]").value;

    let url = "/" + this.dataset.action.replaceAll(":", "/");
    let data_string = "?query=" + value + "&type=" + type;

    if (value.trim().length < 1) {
      MaterialInput.hide_search(input, false);

      return;
    }

    $.ajax({
      url: url + data_string,
      method: "GET",
      processData: false,
      contentType: false,
      success: function (data) {
        if (data.status) {
          MaterialInput.show_search(input, data.data, true);
        } else {
          MaterialInput.hide_search(input, true);
        }
      },
      error: function (error) {
        Frontend.ajax_error(error);
      },
    });
  });

  /**
   * Delete deletable inputs.
   */
  $(document).on("click", "[input][has-delete] [input-delete]", function (e) {
    let input_w_value = this.closest("[input]").find("input[type=hidden]");

    /**
     * Only delete the input if a value has been entered.
     */
    if (input_w_value && input_w_value.value.trim().length < 1) return;

    /**
     * Delete the input.
     */
    this.closest("[input]").remove();
  });

  /**
   * Sets a button on click to be done.
   */
  $(document).on("click", "[done-after-click]", function (e) {
    this.done();
  });

  /**
   * Set any closest timeline option to be done. Should show a
   * different icon then, when implemented correctly :).
   */
  $(document).on(
    "click",
    '[data-action="repair:order:print-done"]',
    function (e) {
      let tloption = this.closest("timeline-option");

      tloption?.done();
      tloption?.deactivate();
    }
  );
});
