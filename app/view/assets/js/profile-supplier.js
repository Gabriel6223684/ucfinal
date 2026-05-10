function showSection(sectionId) {
  // 1. Esconde TODAS as divs de conteúdo
  const sections = document.querySelectorAll(".content-section");
  sections.forEach((s) => s.classList.add("is-hidden"));

  // 2. Remove a classe 'is-active' de todos os links do menu
  const links = document.querySelectorAll(".menu-list a");
  links.forEach((l) => l.classList.remove("is-active"));

  // 3. Tenta mostrar a seção específica
  const targetSection = document.getElementById("section-" + sectionId);
  const targetLink = document.getElementById("link-" + sectionId);

  if (targetSection) {
    targetSection.classList.remove("is-hidden");
  }

  if (targetLink) {
    targetLink.classList.add("is-active");
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
