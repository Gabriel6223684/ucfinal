import $ from "jquery";
import "jquery-validation";

export default class Validate {
  static #form = null;

  static SetForm(id) {
    $.validator.setDefaults({
      errorElement: "span",
      errorPlacement(error, element) {
        error.addClass("invalid-feedback");
        element.closest(".form-group").append(error);
      },
      highlight(element) {
        $(element).addClass("is-invalid");
      },
      unhighlight(element) {
        $(element).removeClass("is-invalid");
      },
    });

    this.#form = $(`#${id}`);

    if (!this.#form || this.#form.length === 0) {
      throw new Error(`Formulário #${id} não encontrado!`);
    }

    this.#form.validate();
    return this;
  }

  static Validate() {
    if (!this.#form || this.#form.length === 0) {
      throw new Error(
        "Formulário não inicializado. Chame Validate.SetForm(id) primeiro.",
      );
    }

    if (!this.#form.data("validator")) {
      this.#form.validate();
    }

    return this.#form.valid();
  }
}
