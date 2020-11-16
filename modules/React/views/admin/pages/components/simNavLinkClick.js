
const simNavLinkClick = () => {
    //fix-bug: begin (修正階層下拉選單無法開啟的錯誤)
    document.getElementsByClassName('c-active').forEach(function(element) {
        const parentUntilLast = element.parentNode.parentNode.parentNode;
        const aElement = parentUntilLast.children[0];
        if (!parentUntilLast.className.match(/c\-show/)) {
            aElement.dispatchEvent(new MouseEvent('click', {
                'view': window,
                'bubbles': true,
                'cancelable': true
            }));
        }
    });
    //fix-bug: end
}


export default simNavLinkClick;