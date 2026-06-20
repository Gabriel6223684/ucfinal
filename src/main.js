// main.js  
import { fetchData } from "./services/apiConfig";
import '../resources/css/main.css';

// Exemplo de uso no front-end ao carregar a página
document.addEventListener("DOMContentLoaded", async () => {
  const dados = await fetchData();

  if (dados) {
    console.log("Dados recebidos do back-end:", dados);
    // Lógica para injetar os dados no HTML aqui...
  }
});

// --- 1. Injeção de Estilo (CSS in JS) ---
const styleSheet = document.createElement("style");
styleSheet.innerText = `
  :root {
    --bg-color: #ffffff;
    --accent: #000000;
    --border: #f0f0f0;
    --text-main: #111111;
  }

  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'Inter', -apple-system, sans-serif;
    background-color: var(--bg-color);
    color: var(--text-main);
    line-height: 1.6;
  }

  /* Navbar */
  .navbar {
    position: fixed;
    top: 0;
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.2rem 10%;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--border);
    z-index: 1000;
  }

  .logo { font-weight: 800; font-size: 1.2rem; letter-spacing: -1px; }

  .user-profile-link {
    display: flex !important;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    color: var(--text-main);
  }

  .user-profile-link svg { color: #febd69; }
  
  .app-layout { display: flex; height: 100vh; pt: 80px; } /* Ajuste para navbar fixa */
  .sidebar { width: 300px; border-right: 1px solid var(--border); }
  .main-content { flex: 1; }
  
`;
document.head.appendChild(styleSheet);

// --- 2. Variáveis de Estado ---
let conversations = [];
let activeChat = null;
let pollingInterval = null;
const appEl = document.querySelector("#app");

// --- 3. Funções de Renderização ---

async function renderHome() {
  if (typeof stopPolling === "function") stopPolling();

  const user = window.currentUser; // Assumindo que existe globalmente
  const initials =
    typeof getInitials === "function"
      ? getInitials(user?.username || user?.email)
      : "??";

  try {
    const dbConvs = await window.api.getConversations(user.id);
    conversations = dbConvs.map((c) => ({
      id: c.other_id,
      username: c.username,
      email: c.email,
      lastMsg: c.last_msg || "",
      time: typeof formatTime === "function" ? formatTime(c.time) : c.time,
    }));
  } catch (e) {
    conversations = [];
  }

  appEl.innerHTML = `
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.4/css/bulma.min.css">
    <nav class="navbar">
      <div class="logo">MINIMAL.</div>

      <div class="field has-addons">
        <p class="control">
          <input class="input" type="text" placeholder="Find a user" />
        </p>
        <p class="control">
          <button class="button">Search</button>
        </p>
      </div>
      <div class="chat-list" id="chat-list">
          ${renderChatList()}
        </div>

      <div style="display: flex; gap: 20px; align-items: center;">
        <a href="<?= $routeParser->urlFor('login') ?>" class="login-btn">Log in</a>
        <a href="<?= $routeParser->urlFor('profile') ?>" class="user-profile-link">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
          </svg>
          <span class="user-name-text">My Account</span>
        </a>
      </div>
    </nav>

    <div class="app-layout" style="margin-top: 70px;">
      <div class="sidebar">
        <div class="sidebar-header" style="padding: 20px;">
          <div class="avatar">${initials}</div>
        </div>
        <div class="chat-list" id="chat-list">
          ${renderChatList()}
        </div>
      </div>
      <div class="main-content" id="main-content">
        ${renderEmptyState()}
      </div>
    </div>
  `;

  if (activeChat) openChat(activeChat);
}

function renderChatList() {
  if (conversations.length === 0) {
    return `<div style="padding: 20px; text-align: center;">No conversation yet</div>`;
  }

  return conversations
    .map(
      (c) => `
    <div class="chat-item ${activeChat === c.id ? "active" : ""}" onclick="openChat(${c.id})" 
         style="padding: 15px; border-bottom: 1px solid #eee; cursor: pointer;">
      <div class="chat-item-name"><strong>${c.username || c.email}</strong></div>
      <div class="chat-item-preview" style="font-size: 0.8rem; color: #666;">${c.lastMsg || "Start conversation"}</div>
    </div>
  `,
    )
    .join("");
}

function renderEmptyState() {
  return `
    <div class="empty-state" style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%;">
      <h3>MINIMAL.</h3>
      <p>Select a conversation to start.</p>
    </div>
  `;
}

async function openChat(receiverId) {
  activeChat = receiverId;
  if (typeof stopPolling === "function") stopPolling();

  const conv = conversations.find((c) => c.id === receiverId);
  const name = conv?.username || conv?.email || "User";

  const main = document.getElementById("main-content");
  if (!main) return;

  main.innerHTML = `
    <div class="chat-header" style="padding: 15px; border-bottom: 1px solid #eee;">
      <strong>${name}</strong>
    </div>
    <div class="messages-area" id="messages-area" style="height: 400px; overflow-y: auto; padding: 20px;">
      <div style="text-align:center;color:gray;">Loading...</div>
    </div>
    <div class="message-input-bar" style="padding: 15px; display: flex; gap: 10px;">
      <input id="msg-input" style="flex:1; padding: 8px;" placeholder="Menssage..." 
             onkeydown="if(event.key==='Enter') sendMsg(${receiverId})">
      <button onclick="sendMsg(${receiverId})">Submit</button>
    </div>
  `;

  await loadMessages(receiverId);
  // Reatribui o polling
  pollingInterval = setInterval(() => loadMessages(receiverId), 3000);
}

function stopPolling() {
  if (pollingInterval) clearInterval(pollingInterval);
}

// Inicializa a página
renderHome();
