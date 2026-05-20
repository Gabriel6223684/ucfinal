import Swal from "sweetalert2";
import Validate from "../components/validate.js";
import Request from "../components/requests.js";

$(document).ready(function () {
  $("#tel").mask("(00) 00000-0000");
});

const buttonRegister = document.getElementById("register");

if (buttonRegister) {
  buttonRegister.addEventListener("click", async () => {
    // Valida inputs do formulário #formregister
    const validou = Validate.SetForm("formregister").Validate();

    if (!validou) {
      Swal.fire({
        icon: "error",
        title: "Ops...",
        text: "Preencha os campos corretamente!",
        timer: 2500,
        progressBar: true,
      });
      return;
    }

    const requests = new Request();

    const originalText = buttonRegister.textContent;
    try {
      buttonRegister.textContent = "Cadastrando, por favor aguarde...";
      buttonRegister.disabled = true;

      // Envia para /authentication/preregister
      await requests.setForm("register").post("/authentication/register");

      Swal.fire({
        icon: "success",
        title: "Conta criada!",
        text: "Seu cadastro foi realizado com sucesso.",
        timer: 2500,
        showConfirmButton: false,
        willClose: () => {
          window.location.href = "/login";
        },
      });
    } catch (e) {
      Swal.fire({
        icon: "error",
        title: "Erro",
        text: "Não foi possível concluir o cadastro. Tente novamente.",
      });
    } finally {
      buttonRegister.disabled = false;
      buttonRegister.textContent = originalText;
    }
  });
}

// Função auxiliar simples para capturar cookies no front-end
function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(";").shift();
}
