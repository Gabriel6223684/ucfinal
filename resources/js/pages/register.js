import Swal from "sweetalert2";
import Validate from "../components/validate.js";
import Request from "../components/requests.js";

const buttonRegister = document.getElementById("register");

if (buttonRegister) {
  // Adicionado o parâmetro 'e' para gerenciar o evento de clique
  buttonRegister.addEventListener("click", async (e) => {
    e.preventDefault(); // Evita o comportamento padrão de recarregar a página

    // Valida os inputs do formulário #formregister
    const validou = Validate.SetForm("formregister").Validate();

    if (!validou) {
      Swal.fire({
        icon: "error",
        title: "Ops...",
        text: "Preencha os campos corretamente!",
        timer: 2500,
        showConfirmButton: false,
      });
      return;
    }

    const requests = new Request({
      baseUrl: "http://localhost:8080/ucfinal",
    });
    const originalText = buttonRegister.textContent;

    try {
      buttonRegister.textContent = "Cadastrando, por favor aguarde...";
      buttonRegister.disabled = true;

      // Corrigido: alterado de "register" para "formregister" para capturar o formulário correto
      const dados = await requests
        .setForm("formregister")
        .post("/authentication/register");

      // Garantia defensiva: se o back-end retornar vazio, força a ida para o catch
      if (!dados) {
        throw new Error("Resposta inválida do servidor.");
      }

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
    } catch (error) {
      // Mantém o log apenas aqui no ambiente de desenvolvimento para você debugar se a API falhar
      console.error("Erro no fluxo de cadastro:", error);

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
