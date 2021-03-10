export const LANGUAGE_TYPE = "INIT_LANGUAGE";
export const initLanguage = (route) => {
  let locale = route.replace(/^\//, '').split('/')[0];
  if(locale == 'system-maintain') {
    locale = '';
  }
  return {
    type: LANGUAGE_TYPE,
    payload: locale
  }
};
