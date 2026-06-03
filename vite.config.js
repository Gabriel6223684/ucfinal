import { defineConfig } from "vite";
import { resolve, dirname } from "node:path";
import { fileURLToPath } from "node:url";
import fs from "node:fs";

const __dirname = dirname(fileURLToPath(import.meta.url));

function writeHotFilePlugin() {
  return {
    name: "jaiminho-write-hot-file",
    apply: "serve",
  };
}

export default defineConfig(({ command }) => ({
  base: "/ucfinal/",

  build: {
    outDir: "dist",
    emptyOutDir: true,
    manifest: true,

    rollupOptions: {
      input: {
        app: resolve(__dirname, "resources/js/app.js"),
      },
    },
  },

  server: {
    host: "0.0.0.0",
    port: 5173,
  },

  plugins: [writeHotFilePlugin()],
}));
