import { defineConfig } from "vite";
import symfonyPlugin from "vite-plugin-symfony";
import vuePlugin from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        vuePlugin(),
        symfonyPlugin({
            stimulus: true,
            viteDevServerHostname: "localhost",
        }),
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
