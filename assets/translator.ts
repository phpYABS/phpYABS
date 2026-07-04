import { createTranslator, getDefaultLocale } from "@symfony/ux-translator";

import { localeFallbacks, messages } from "../var/translations";

const { trans, setLocale } = createTranslator({
  messages,
  locale: getDefaultLocale(),
  localeFallbacks,
});

export { setLocale, trans };
