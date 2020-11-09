
//操作成功及操作失敗的訊息

const notify = (type, message, wait, callback) => {
    const alertify = require('alertifyjs/build/alertify');
    if(typeof wait == 'undefined' || !wait) {
        wait = 3;
    }
    const func = alertify[type];
    if(typeof callback == 'undefined' || !callback) {
        callback = () => {};
    }
    
    func(message, wait, callback);
}

export default notify;