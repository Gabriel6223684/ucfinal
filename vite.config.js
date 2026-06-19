import { defineConfig } from "vite";
import { resolve, dirname } from "node:path";
import { fileURLToPath } from "node:url";

const __dirname = dirname(fileURLToPath(import.meta.url));

export default defineConfig({
  base: "/",

  build: {
    outDir: "dist",
    emptyOutDir: true,
    manifest: true,
  },

  resolve: {
    alias: {
      "@": resolve(__dirname, "resources/js"),
    },
  },

  server: {
    host: "0.0.0.0",
    port: 5173,
  },
});
