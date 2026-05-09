document.addEventListener("DOMContentLoaded", () => {
  const $navbarBurgers = Array.prototype.slice.call(
    document.querySelectorAll(".navbar-burger"),
    0,
  );
  if ($navbarBurgers.length > 0) {
    $navbarBurgers.forEach((el) => {
      el.addEventListener("click", () => {
        const target = el.dataset.target;
        const $target = document.getElementById(target);
        el.classList.toggle("is-active");
        $target.classList.toggle("is-active");
      });
    });
  }
});

document.addEventListener("DOMContentLoaded", () => {
  // Funções para abrir e fechar
  function openModal($el) {
    $el.classList.add("is-active");
  }

  function closeModal($el) {
    $el.classList.remove("is-active");
  }

  function closeAllModals() {
    (document.querySelectorAll(".modal") || []).forEach(($modal) => {
      closeModal($modal);
    });
  }

  // 1. Adiciona evento de clique nos botões que abrem o modal
  (document.querySelectorAll(".js-modal-trigger") || []).forEach(($trigger) => {
    const modalId = $trigger.dataset.target;
    const $target = document.getElementById(modalId);

    $trigger.addEventListener("click", () => {
      openModal($target);
    });
  });

  // 2. Adiciona evento de clique para fechar (botão delete, background ou cancelar)
  (
    document.querySelectorAll(
      ".modal-background, .delete, .modal-card-foot .button",
    ) || []
  ).forEach(($close) => {
    const $target = $close.closest(".modal");

    $close.addEventListener("click", () => {
      closeModal($target);
    });
  });

  // 3. Fechar com a tecla ESC
  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeAllModals();
    }
  });
});
