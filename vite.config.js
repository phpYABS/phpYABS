import { defineConfig } from "vite";
import symfonyPlugin from "vite-plugin-symfony";
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        symfonyPlugin({
            stimulus: true,
            viteDevServerHostname: "localhost",
        }),
        vue(),
    ],
    build: {
        rollupOptions: {
            input: {
                app: "./assets/app.ts"
            },
        }
    },
    server: {
        host: "0.0.0.0"
    }
});
