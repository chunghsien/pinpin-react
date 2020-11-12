
//應用在確認繼續下一步的動作
const toConfirm = (okCallback, t) => {
    const alertify = require('alertifyjs/build/alertify');
    alertify.confirm().setting({
        message: t('Are you sure you want to execute?'),
        labels: {
            ok: t('alertify-ok-btn'),
            cancel: t('alertify-cancel-btn'),
        },
        title: t('alertify-dialog-title'),
        closable: false,
        onok: okCallback
    }).show();
}

export default toConfirm;