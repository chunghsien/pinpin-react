const translator = (lang, ns, text) => {
    const path = './' + lang + '/' + ns;
    const t = require('' + path).default;
    const data = t();
    if(typeof data[text] == 'undefined') {
        return text;
    }
    return data[text];
}
export default translator;