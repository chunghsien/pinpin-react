
const formBreadItemRename = (t) => {
    var breadcrumbItemCollection = document.getElementsByClassName('breadcrumb-item')
    const collencionLastIndex = (breadcrumbItemCollection.length - 1);
    if(location.pathname.match(/\/\d+$/)) {
        breadcrumbItemCollection.item(collencionLastIndex).innerHTML = t('Data update');
    }else {
        breadcrumbItemCollection.item(collencionLastIndex).innerHTML = t('Data insert');
    }
    

}

export default formBreadItemRename;