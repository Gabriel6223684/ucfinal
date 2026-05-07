// Função para trocar as abas
function showTab(type) {
  document
    .getElementById("login-tab")
    .classList.toggle("is-active", type === "login");
  document
    .getElementById("register-tab")
    .classList.toggle("is-active", type === "register");
  document
    .getElementById("login-content")
    .classList.toggle("is-active", type === "login");
  document
    .getElementById("register-content")
    .classList.toggle("is-active", type === "register");
}

// Função para mostrar/esconder a senha
function togglePassword(inputId) {
  const input = document.getElementById(inputId);
  const icon = document.getElementById(inputId + "-icon");

  if (input.type === "password") {
    input.type = "text";
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
  } else {
    input.type = "password";
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
  }
}
