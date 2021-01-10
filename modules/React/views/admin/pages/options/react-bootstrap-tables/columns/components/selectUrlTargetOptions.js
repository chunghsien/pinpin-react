
const selectUrlTargetOptions = (t) => {
    return [
        { value: '_self', label: t('_self') },
        { value: '_blank', label: t('_blank') },
    ];
}

export default selectUrlTargetOptions;