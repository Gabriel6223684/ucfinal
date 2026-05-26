function showSection(sectionId) {
  // 1. Esconder todas as seções
  const sections = document.querySelectorAll(".content-section");
  sections.forEach((s) => {
    s.classList.add("is-hidden");
  });

  // 2. Desativar todos os links do menu
  const links = document.querySelectorAll(".menu-list a");
  links.forEach((l) => {
    l.classList.remove("is-active");
  });

  // 3. Mostrar a seção clicada
  const target = document.getElementById("section-" + sectionId);
  if (target) {
    target.classList.remove("is-hidden");
  }

  // 4. Ativar o link clicado
  const activeLink = document.getElementById("link-" + sectionId);
  if (activeLink) {
    activeLink.classList.add("is-active");
  }
}

function toggleTelEdit() {
  const display = document.getElementById("tel-display");
  const edit = document.getElementById("tel-edit");
  if (display && edit) {
    display.classList.toggle("is-hidden");
    edit.classList.toggle("is-hidden");
  }
}

// Função para mudar o tema
function setTheme(theme) {
  const htmlElement = document.documentElement;
  const btnLight = document.getElementById("btn-light");
  const btnDark = document.getElementById("btn-dark");

  if (theme === "dark") {
    htmlElement.setAttribute("data-theme", "dark");
    btnDark.classList.add("is-info", "is-selected");
    btnLight.classList.remove("is-info", "is-selected");
    localStorage.setItem("theme", "dark"); // Salva a preferência
  } else {
    htmlElement.setAttribute("data-theme", "light");
    btnLight.classList.add("is-info", "is-selected");
    btnDark.classList.remove("is-info", "is-selected");
    localStorage.setItem("theme", "light");
  }
}

// Ao carregar a página, verifica se o usuário já tinha escolhido um tema
document.addEventListener("DOMContentLoaded", () => {
  const savedTheme = localStorage.getItem("theme") || "light";
  setTheme(savedTheme);
});

// Exemplo de verificação: simula se o usuário está logado (mude para sua lógica real)
// Geralmente você checa se existe um token: !!localStorage.getItem('userToken')
const usuarioEstaLogado = false;

document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("modal-login-required");
  const mainContent = document.querySelector(".section"); // Seleciona o conteúdo da página

  if (usuarioEstaLogado) {
    // Se estiver logado, remove a modal e mostra a página normalmente
    modal.classList.remove("is-active");
  } else {
    // Se NÃO estiver logado, garante que a modal apareça e esconde os dados ao fundo
    modal.classList.add("is-active");
    if (mainContent) {
      mainContent.style.display = "none";
    }
  }
});
