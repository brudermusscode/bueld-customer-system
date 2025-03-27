import * as Frontend from "../frontend";
import * as Page from "../page";
import * as MaterialInput from "../elements/minput";

let search_timeout = null;

$(function () {
  $(document).on("click", function (e) {
    /**
     * Close searches
     */
    let searchers = document.find_all("[searcher]");
    if (searchers)
      searchers.forEach((i) => {
        if (
          e.target.closest("[searcher]") !== i &&
          e.target !== i &&
          !e.target.closest("[open-searcher]")
        )
          if (i.hasAttribute("active")) i.deactivate();
      });
  });

  /**
   * Search page lists.
   */
  $(document).on("submit", '[data-form="page-searcher"]', function (e) {
    e.preventDefault();

    let formdata = new FormData(this);
    let search = formdata.get("query");
    let searchers = document.querySelectorAll("[searcher]");

    searchers.forEach((searcher) => {
      searcher.deactivate();
    });

    /**
     * Get current location
     */
    let pathname = window.location.pathname;
    let updated_pathname = pathname.replace(/\?query=[^&]*/, "");

    Page.get(`${updated_pathname}?query=${search}`);
  });

  $(document).on("click", "[open-searcher]", function (e) {
    let searcher = document.find("[searcher]");

    searcher?.activate();

    setTimeout(() => {
      searcher?.find("input")?.focus();
    }, 100);
  });

  /**
   * Searches for all customers with the input string matching
   * firstname &|| lastname
   *
   * @action GET
   * @controller customer/search
   */
  $(document).on("input", "[data-action='customer:search']", function (e) {
    let input = this;
    let value = this.value;
    let results = input.closest("[customer]").find("[search-results]");
    let type = this.dataset.type;

    /**
     * Hide the search if no value is in the input and return.
     */
    if (value.trim().length < 1 && results) {
      results.innerHTML = "";

      return;
    }

    let url = "/" + this.dataset.action.replaceAll(":", "/");
    let data_string = "?full_name=" + value + (type ? `&type=${type}` : "");

    $.ajax({
      url: url + data_string,
      method: "GET",
      processData: false,
      contentType: false,
      success: function (data) {
        // console.log(data);

        if (results) {
          results.innerHTML = "";
          results.insertAdjacentHTML("beforeend", data.data);
        }
      },
      error: function (error) {
        Frontend.ajax_error(error);
      },
    });
  });

  /**
   * When a customer in order process was selected already, there
   * is shown an edit button. This function will hide the form
   * data and show the search results on type while giving free
   * the full name input.
   */
  $(document).on(
    "click",
    '[data-action="customer:edit-selection"]',
    function (e) {
      let button = this;
      let section = this.closest("[customer]");
      let name_input = section.find("input[name=full_name]");

      section.undone();
      section.deactivate();
      name_input.removeAttribute("not-editable");

      name_input.focus();
      name_input.setSelectionRange(
        name_input.value.length,
        name_input.value.length
      );
    }
  );

  /**
   * Finds a customer by their ID.
   *
   * @action GET
   * @controller customer/find
   */
  $(document).on("click", "[data-action='customer:find']", function (e) {
    let button = this;
    let id = this.dataset.id;
    let section = this.closest("[customer]");
    let name_input = section.find("input[name=full_name]");
    let id_input = section.find("input[name=customer_id]");
    let address_input = section.find("input[name=address]");
    let phone_input = section.find("input[name=phone]");
    let mail_input = section.find("input[name=mail]");

    let url = "/" + this.dataset.action.replaceAll(":", "/");
    let data_string = "?id=" + id;

    Frontend.load();
    button.disable();

    $.ajax({
      url: url + data_string,
      method: "GET",
      processData: false,
      contentType: false,
      success: function (data) {
        Frontend.unload();
        document.activeElement.blur();

        /**
         * Customer has been found.
         */
        if (data.status && data.data.id && data.data.id > 0) {
          clear_form(section);

          section.deactivate();
          section.setAttribute("done", "");

          let customer = data.data;

          /**
           * Disable the full name input. When wanting to edit,
           * you need to click the edit button and populate all
           * other inputs.
           */
          name_input.setAttribute("not-editable", "");
          id_input.value = customer.id;
          name_input.value =
            customer.company_name ??
            customer.firstname + " " + customer.lastname;
          address_input.value = customer.address;
          phone_input.value = customer.phone;
          mail_input.value = customer.mail;
        } else {
          button.enable();
          name_input.enable();
        }
      },
      error: function (error) {
        Frontend.ajax_error(error);
      },
    });
  });

  /**
   * Opens the whole form for adding a new customer.
   */
  $(document).on("click", '[data-action="customer:new"]', function (e) {
    let section = this.closest("section[customer]");
    let name_input = section?.find("input[name=full_name]");
    let mail_input = section?.find("input[name=mail]");

    /**
     * Return error responder when name has no space in it. This
     * will most likely be due to just a first- or lastname being entered.
     */
    if (!name_input.value.includes(" ") || name_input.value.length < 4) {
      Frontend.create_responder(
        "<strong>Der Name des Kunden sollte aus Vor- und Nachname bestehen!</strong>",
        "error"
      );

      return;
    }

    section.undone();
    section.activate();

    clear_form(section);

    mail_input.focus();
    mail_input.setSelectionRange(
      mail_input.value.length,
      mail_input.value.length
    );
  });

  $(document).on("input", "[input][has-search] input", function (e) {
    if (!this.hasAttribute("data-request")) return;

    let input = this;
    let request = this.dataset.request.replaceAll(":", "/");
    let query = this.value;

    let url = "/" + request + "?query=" + query;

    $.ajax({
      url: url,
      method: "GET",
      processData: false,
      contentType: false,
      success: function (data) {
        console.log(data);

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
});

const enable_submit_button = (form) => {
  if (!form) return;

  let submit = form.find("mbutton[submit-closest]");

  submit?.enable();
};

/**
 * Searches in the given element for all inputs and clears them as
 * well as unsetting the element itself active and removing any
 * attribute that could manipulate the appearance of the innerHTML.
 *
 * @param {Element} section_or_form
 */
export const clear_form = (section_or_form) => {
  section_or_form.find_all("input").forEach((input) => {
    if (input.getAttribute("name") !== "full_name") input.value = "";
  });

  section_or_form.find("[search-results]").innerHTML = "";
};
