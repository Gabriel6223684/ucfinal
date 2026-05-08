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

function togglePassword(inputId) {
  const input = document.getElementById(inputId);
  const icon = document.getElementById(inputId + "-icon");
  if (input.type === "password") {
    input.type = "text";
    icon.classList.replace("fa-eye", "fa-eye-slash");
  } else {
    input.type = "password";
    icon.classList.replace("fa-eye-slash", "fa-eye");
  }
}
