import { startStimulusApp, registerControllers } from "vite-plugin-symfony/stimulus/helpers"
import { registerVueControllerComponents, type VueModule } from "vite-plugin-symfony/stimulus/helpers/vue"

// register Vue components before startStimulusApp
registerVueControllerComponents(import.meta.glob<VueModule>("./vue/controllers/**/*.vue"));

const app = startStimulusApp();
registerControllers(
    app,
    import.meta.glob<StimulusControllerInfosImport>(
        "./controllers/*_controller.ts",
        {
            query: "?stimulus",
            /**
             * always true, the `lazy` behavior is managed internally with
             * import.meta.stimulusFetch (see reference)
             */
            eager: true,
        },
    ),
);
