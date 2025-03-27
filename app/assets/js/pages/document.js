import * as Frontend from "../frontend";
import * as Page from "../page";
import * as MaterialInput from "../elements/minput";

$(function () {
  /**
   * @action POST
   * @controller document/get
   */
  $(document).on("submit", '[data-form="document:get"]', function (e) {
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
          const byteCharacters = atob(data.data);
          const byteNumbers = new Uint8Array(byteCharacters.length);
          for (let i = 0; i < byteCharacters.length; i++) {
            byteNumbers[i] = byteCharacters.charCodeAt(i);
          }
          const blob = new Blob([byteNumbers], { type: "application/pdf" });
          const url = URL.createObjectURL(blob);

          setTimeout(() => {
            window.open(url, "_blank");
          }, 200);
        } else Frontend.create_responder(data);
      },
      error: function (error) {
        button.enable();
        Frontend.ajax_error(error);
      },
    });
  });
});
