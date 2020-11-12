const selectStockStatusOptions = (t) => {
    var request = new XMLHttpRequest();
    request.open('GET', '/api/admin/products_spec/getStockStatus', false);
    request.send();

    return JSON.parse(request.responseText).data.options.stock_status.map((item, key) => {
        return { key: 'stock_status_'+key, value: item.value, label: t(item.label) };
    });

}

export default selectStockStatusOptions;