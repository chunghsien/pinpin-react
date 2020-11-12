
const selectUseOption = (t) => {
    return [
        { value: 0, label: t('isUseDisable') },
        { value: 1, label: t('isUseEnable') },
    ];
}

export default selectUseOption;