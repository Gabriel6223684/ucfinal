function switchTab(perfil) {
  document
    .getElementById("tab-cliente")
    .classList.toggle("is-active", perfil === "cliente");
  document
    .getElementById("tab-fornecedor")
    .classList.toggle("is-active", perfil === "fornecedor");

  document
    .getElementById("form-cliente")
    .classList.toggle("is-hidden", perfil !== "cliente");
  document
    .getElementById("form-fornecedor")
    .classList.toggle("is-hidden", perfil !== "fornecedor");
}
