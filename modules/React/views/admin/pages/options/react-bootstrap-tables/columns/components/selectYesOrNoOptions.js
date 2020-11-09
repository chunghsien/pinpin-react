
const selectYesOrNoOption = (t) => {
    return [
        { value: 0, label: t('isNo') },
        { value: 1, label: t('isYes') },
    ];
}

export default selectYesOrNoOption;