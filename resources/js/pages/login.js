import Swal from "sweetalert2";
import Requests from "../components/requests.js";

const buttonLogin = document.getElementById("login");

if (buttonLogin) {
  buttonLogin.addEventListener("click", async () => {
    const login = document.getElementById("login-email")?.value?.trim();
    const senha = document.getElementById("login-pass")?.value?.trim();

    if (!login || !senha) {
      Swal.fire({
        icon: "error",
        title: "Ops...",
        text: "Preencha seu email e senha!",
        timer: 2500,
      });
      return;
    }

    const requests = new Requests();
    const originalText = buttonLogin.textContent;

    try {
      buttonLogin.textContent = "Entrando, aguarde...";
      buttonLogin.disabled = true;

      const data = await requests
        .setForm("formlogin")
        .post("/authentication/authenticate");

      Swal.fire({
        icon: "success",
        title: data?.msg || "Bem-vindo!",
        timer: 1500,
        showConfirmButton: false,
        willClose: () => {
          window.location.href = "/dashboard";
        },
      });
    } catch (e) {
      console.error("Erro no login:", e);
      Swal.fire({
        icon: "error",
        title: "Erro ao entrar",
        text: e?.message || "Erro ao conectar ao servidor.",
      });
    } finally {
      buttonLogin.disabled = false;
      buttonLogin.textContent = originalText;
    }
  });
}
