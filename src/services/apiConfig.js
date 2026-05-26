// apiConfig.js

// 1. Defina a URL base do seu back-end
export const API_BASE_URL = "http://localhost/5173";

// 2. Crie funções para lidar com as requisições separadamente
export async function fetchData() {
  try {
    const response = await fetch(`${API_BASE_URL}/dados`);
    if (!response.ok) throw new Error("Erro na requisição");
    return await response.json();
  } catch (error) {
    console.error("Erro ao conectar com o back-end:", error);
  }
}
