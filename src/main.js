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

  .btn-outline:hover { background: var(--border); }
`;
document.head.appendChild(styleSheet);

// --- Estrutura HTML ---
document.querySelector("#app").innerHTML = `
  <nav class="navbar">
    <div class="logo">MINIMAL.</div>
    
    <ul class="nav-links">
      <li><a href="#">Product</a></li>
      <li><a href="#">Features</a></li>
      <li><a href="#">Pricing</a></li>
    </ul>

    <a href="../app/view/pages/login.html" class="login-btn">Log in</a>
  </nav>

  <main>
    <section class="hero">
      <h1>Design simple.<br>Build fast.</h1>
      <p>A high-performance template for those who value aesthetics and speed. No clutter, just code.</p>
      
      <div class="cta-group">
        <a href="../app/view/layouts/main.html" class="btn-main">Get Started</a>
        <a href="#" class="btn-outline">View Demo</a>
      </div>
    </section>
  </main>
`;
