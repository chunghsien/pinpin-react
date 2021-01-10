import { useRouter } from "next/router";

const currentLocale = () => {
  const splitUri = useRouter().pathname.split('/');
  let locale = splitUri[1];
  return locale;
}

class BackendTranslator {
  constructor(allMessages) {
    this.allMessages = allMessages;
  }
  t(ns, text, plurs) {
    const allMessages = this.allMessages;
    if(typeof allMessages[ns] == 'undefined') {
      return text;
    }
    if(typeof allMessages[ns][text] == 'undefined') {
      return text;
    }
    var _text = allMessages[ns][text];
    if(typeof plurs == 'object') {
      const keys = Object.keys(plurs);
      keys.forEach((key) => {
        _text = _text.replace(new RegExp(key), plurs[key]);
      });
    }
    return _text;
  }
}

export { currentLocale ,BackendTranslator};