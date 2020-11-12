const selectPayMethodOptions = (t) => {
    const namespace = pageConfig.third_party_service.logistics.manufacturer;
    var options = {}
    pageConfig.pay_method_options.forEach((option) => {
        var key = option.value;
        var label = t(namespace+':'+option.label);
        options[key] = label;
    });
    return options;
}
export default selectPayMethodOptions;