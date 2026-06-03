(function(){let e=document.createElement(`link`).relList;if(e&&e.supports&&e.supports(`modulepreload`))return;for(let e of document.querySelectorAll(`link[rel="modulepreload"]`))n(e);new MutationObserver(e=>{for(let t of e)if(t.type===`childList`)for(let e of t.addedNodes)e.tagName===`LINK`&&e.rel===`modulepreload`&&n(e)}).observe(document,{childList:!0,subtree:!0});function t(e){let t={};return e.integrity&&(t.integrity=e.integrity),e.referrerPolicy&&(t.referrerPolicy=e.referrerPolicy),e.crossOrigin===`use-credentials`?t.credentials=`include`:e.crossOrigin===`anonymous`?t.credentials=`omit`:t.credentials=`same-origin`,t}function n(e){if(e.ep)return;e.ep=!0;let n=t(e);fetch(e.href,n)}})();var e=`http://localhost/5173`;async function t(){try{let t=await fetch(`${e}/dados`);if(!t.ok)throw Error(`Erro na requisição`);return await t.json()}catch(e){console.error(`Erro ao conectar com o back-end:`,e)}}document.addEventListener(`DOMContentLoaded`,async()=>{let e=await t();e&&console.log(`Dados recebidos do back-end:`,e)});var n=document.createElement(`style`);n.innerText=`
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
  
`,document.head.appendChild(n);var r=[],i=null,a=null,o=document.querySelector(`#app`);async function s(){typeof d==`function`&&d();let e=window.currentUser,t=typeof getInitials==`function`?getInitials(e?.username||e?.email):`??`;try{r=(await window.api.getConversations(e.id)).map(e=>({id:e.other_id,username:e.username,email:e.email,lastMsg:e.last_msg||``,time:typeof formatTime==`function`?formatTime(e.time):e.time}))}catch{r=[]}o.innerHTML=`
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
          ${c()}
        </div>

      <div style="display: flex; gap: 20px; align-items: center;">
        <a href="../app/view/pages/login.html" class="login-btn">Log in</a>
        <a href="../app/view/pages/profile.html" class="user-profile-link">
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
          <div class="avatar">${t}</div>
        </div>
        <div class="chat-list" id="chat-list">
          ${c()}
        </div>
      </div>
      <div class="main-content" id="main-content">
        ${l()}
      </div>
    </div>
  `,i&&u(i)}function c(){return r.length===0?`<div style="padding: 20px; text-align: center;">No conversation yet</div>`:r.map(e=>`
    <div class="chat-item ${i===e.id?`active`:``}" onclick="openChat(${e.id})" 
         style="padding: 15px; border-bottom: 1px solid #eee; cursor: pointer;">
      <div class="chat-item-name"><strong>${e.username||e.email}</strong></div>
      <div class="chat-item-preview" style="font-size: 0.8rem; color: #666;">${e.lastMsg||`Start conversation`}</div>
    </div>
  `).join(``)}function l(){return`
    <div class="empty-state" style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%;">
      <h3>MINIMAL.</h3>
      <p>Select a conversation to start.</p>
    </div>
  `}async function u(e){i=e,typeof d==`function`&&d();let t=r.find(t=>t.id===e),n=t?.username||t?.email||`User`,o=document.getElementById(`main-content`);o&&(o.innerHTML=`
    <div class="chat-header" style="padding: 15px; border-bottom: 1px solid #eee;">
      <strong>${n}</strong>
    </div>
    <div class="messages-area" id="messages-area" style="height: 400px; overflow-y: auto; padding: 20px;">
      <div style="text-align:center;color:gray;">Loading...</div>
    </div>
    <div class="message-input-bar" style="padding: 15px; display: flex; gap: 10px;">
      <input id="msg-input" style="flex:1; padding: 8px;" placeholder="Menssage..." 
             onkeydown="if(event.key==='Enter') sendMsg(${e})">
      <button onclick="sendMsg(${e})">Submit</button>
    </div>
  `,await loadMessages(e),a=setInterval(()=>loadMessages(e),3e3))}function d(){a&&clearInterval(a)}s();