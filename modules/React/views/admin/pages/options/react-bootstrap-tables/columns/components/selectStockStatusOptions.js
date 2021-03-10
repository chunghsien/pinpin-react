const selectStockStatusOptions = (t) => {
    return pageConfig.stock_status.map((item, key) => {
        return { key: 'stock_status_'+key, value: item.value, label: t(item.label) };
    });
}

export default selectStockStatusOptions;