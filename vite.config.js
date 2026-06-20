import { defineConfig } from "vite";
import { resolve, dirname } from "node:path";
import { fileURLToPath } from "node:url";
import fs from "node:fs";

const __dirname = dirname(fileURLToPath(import.meta.url));
const HOT_FILE = resolve(__dirname, "public/hot");

function writeHotFilePlugin() {
  return {
    name: "jaiminho-write-hot-file",
    apply: "serve",
    configureServer(server) {
      server.httpServer?.once("listening", () => {
        const address = server.httpServer.address();
        const protocol = server.config.server.https ? "https" : "http";
        const host =
          typeof address === "string"
            ? address
            : address.address === "::" || address.address === "0.0.0.0"
              ? "localhost"
              : address.address;
        const port = address.port;
        fs.writeFileSync(HOT_FILE, `${protocol}://${host}:${port}`);
      });
      const cleanup = () => {
        if (fs.existsSync(HOT_FILE)) fs.unlinkSync(HOT_FILE);
      };
      process.on("exit", cleanup);
      process.on("SIGINT", () => {
        cleanup();
        process.exit();
      });
      process.on("SIGTERM", () => {
        cleanup();
        process.exit();
      });
      process.on("SIGHUP", () => {
        cleanup();
        process.exit();
      });
    },
  };
}

function buildPageEntries() {
  const pagesDir = resolve(__dirname, "resources/js/pages");
  if (!fs.existsSync(pagesDir)) return {};

  return Object.fromEntries(
    fs
      .readdirSync(pagesDir, { recursive: true })
      .filter((file) => String(file).endsWith(".js"))
      .map((file) => {
        const normalized = String(file).replace(/\\/g, "/");
        return [
          `pages/${normalized.replace(/\.js$/, "")}`,
          resolve(pagesDir, normalized),
        ];
      }),
  );
}

export default defineConfig(({ command }) => ({
  base: command === "build" ? "/assets/" : "/",
  build: {
    manifest: "manifest.json",
    outDir: "public/assets",
    emptyOutDir: true,
    sourcemap: false,
    cssCodeSplit: true,
    rolldownOptions: {
      input: {
        style: resolve(__dirname, "resources/css/app.css"),
        app: resolve(__dirname, "resources/js/app.js"),
        ...buildPageEntries(), // Otimizado: Carrega todas as páginas dinamicamente!
      },
      output: {
        entryFileNames: "[name]-[hash].js",
        chunkFileNames: "chunks/[name]-[hash].js",
        assetFileNames: (assetInfo) => {
          const name = assetInfo.name ?? "";
          if (/\.(png|jpe?g|gif|svg|webp|ico)$/i.test(name))
            return "images/[name]-[hash][extname]";
          if (/\.(woff2?|ttf|otf|eot)$/i.test(name))
            return "fonts/[name]-[hash][extname]";
          return "assets/[name]-[hash][extname]";
        },
      },
    },
  },
  server: {
    host: "0.0.0.0",
    port: 5173,
    strictPort: true,
    cors: true,
    origin: "http://localhost:5173/ucfinal/",
    hmr: { host: "localhost", protocol: "ws" },
  },
  plugins: [writeHotFilePlugin()],
}));
