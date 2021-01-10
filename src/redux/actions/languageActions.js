export const INIT_LANGUAGE = "INIT_LANGUAGE";
export const initLanguage = (route) => {
  const locale = route.replace(/^\//, '').split('/')[0];
  return {
    type: INIT_LANGUAGE,
    payload: locale
  }
};
