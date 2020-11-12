
const dialog = (message, t) => {
    const alertify = require('alertifyjs/build/alertify');
    alertify.alert().setting({
        message: message,
        label: t('alertify-ok-btn'),
        title: t('alertify-dialog-title'),
        closable: false
    }).show();
    //alertify.alert().destroy();
}

export default dialog;