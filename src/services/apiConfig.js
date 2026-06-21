export const BASE_URL = "http://localhost:8080/ucfinal";

export async function fetchData() {
  try {
    const response = await fetch(`${BASE_URL}/dados`);
    
    if (!response.ok) {
      throw new Error(`Erro na requisição: ${response.status}`);
    }
    
    return await response.json();
  } catch (error) {
    throw error; 
  }
}