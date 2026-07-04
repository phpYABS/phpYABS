import { defineConfig } from "cypress";

export default defineConfig({
  e2e: {
    // The app is served by the `php` container (see compose.yaml).
    baseUrl: "http://localhost:18888",
    supportFile: false,
    fixturesFolder: false,
    video: false,
  },
});
