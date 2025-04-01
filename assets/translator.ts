import { trans, setLocaleFallbacks } from '@symfony/ux-translator';
import { localeFallbacks } from '../var/translations/configuration';

setLocaleFallbacks(localeFallbacks);

export { trans };
export * from '../var/translations';
