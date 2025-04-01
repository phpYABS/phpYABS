import stylisticJs from "@stylistic/eslint-plugin-js"
import sort from "eslint-plugin-simple-import-sort";
import * as tseslint from "typescript-eslint";
import * as eslint from "typescript-eslint";
import pluginVue from 'eslint-plugin-vue'
import globals from 'globals'
export default tseslint.config(
    eslint.configs.recommended,
    tseslint.configs.strict,
    tseslint.configs.stylistic,
    ...pluginVue.configs['flat/recommended'],
    {
        files: ["assets/**/*.ts", "assets/**/*.tsx", "assets/**/*.vue"],
        languageOptions: {
            globals: {
                ...globals.browser
            }
        },
        plugins: {
            "simple-import-sort": sort,
            "@stylistic/js": stylisticJs,
        },
        rules: {
            "@stylistic/js/object-curly-spacing": ["error", "always"],
            "@typescript-eslint/no-non-null-assertion": ["warn"],
            "camelcase": ["error", { "properties": "never" }],
            "comma-spacing": ["error", { "before": false, "after": true }],
            "curly": "error",
            "keyword-spacing": ["error", { "before": true, "after": true }],
            "quotes": ["error", "double"],
            "simple-import-sort/exports": "error",
            "simple-import-sort/imports": "error",
            "space-infix-ops": "error",
        }
    }
);
