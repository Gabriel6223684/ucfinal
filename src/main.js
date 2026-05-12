// --- Injeção de Estilo (CSS in JS) ---
const styleSheet = document.createElement("style");
styleSheet.innerText = `
  :root {
    --bg-color: #ffffff;
    --text-main: #1a1a1a;
    --text-muted: #666666;
    --accent: #000000;
    --border: #f0f0f0;
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

  .logo {
    font-weight: 800;
    font-size: 1.2rem;
    letter-spacing: -1px;
  }

  .nav-links {
    display: flex;
    gap: 2rem;
    list-style: none;
  }

  .nav-links a {
    text-decoration: none;
    color: var(--text-muted);
    font-size: 0.9rem;
    font-weight: 500;
    transition: color 0.2s;
  }

  .nav-links a:hover { color: var(--accent); }

  .login-btn {
    background: var(--accent);
    color: white;
    padding: 0.5rem 1.2rem;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    transition: opacity 0.2s;
  }

  .login-btn:hover { opacity: 0.8; }

  /* Hero Section */
  .hero {
    height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 0 1rem;
  }

  .hero h1 {
    font-size: 4rem;
    font-weight: 800;
    letter-spacing: -2px;
    margin-bottom: 1rem;
  }

  .hero p {
    color: var(--text-muted);
    font-size: 1.25rem;
    max-width: 600px;
    margin-bottom: 2rem;
  }

  .cta-group {
    display: flex;
    gap: 1rem;
  }

  .btn-main {
    padding: 0.8rem 2rem;
    border-radius: 8px;
    border: 1px solid var(--accent);
    background: var(--accent);
    color: white;
    text-decoration: none;
    font-weight: 600;
  }

  .btn-outline {
    padding: 0.8rem 2rem;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: transparent;
    color: var(--text-main);
    text-decoration: none;
    font-weight: 600;
    transition: background 0.2s;
  }

  /* Container do link de perfil */
.user-profile-link {
    display: flex !important;
    align-items: center; /* Alinha verticalmente no centro */
    gap: 8px; /* Espaço entre o ícone e o texto */
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
}

/* Estilização do SVG */
.user-profile-link svg {
    display: block;
    color: #febd69; /* Cor de destaque (mesma do seu botão) */
    vertical-align: middle;
}

/* Ajuste do texto ao lado do ícone */
.user-name-text {
    font-weight: 600;
    line-height: 1; /* Remove alturas de linha extras que empurram o texto */
    margin-top: 2px; /* Ajuste fino se necessário */
}

/* Efeito ao passar o mouse */
.user-profile-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
}

  .btn-outline:hover { background: var(--border); }
`;
document.head.appendChild(styleSheet);

// --- Estrutura HTML ---
document.querySelector("#app").innerHTML = `
  <nav class="navbar">
    <div class="logo">MINIMAL.</div>
    
    <a href="../app/view/pages/login.html" class="login-btn">Log in</a>
    <a href="../app/view/pages/profile.html" class="navbar-item user-profile-link">
    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
    </svg>
    <span class="user-name-text">My Account</span>
</a>
  </nav>
  <main>5
    <section class="hero">
    </section>
  </main>
`;
