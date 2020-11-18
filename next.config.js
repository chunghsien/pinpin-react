const { PHASE_PRODUCTION_SERVER, PHASE_DEVELOPMENT_SERVER } = require('next/constants');

module.exports = (phase, { defaultConfig }) => {
    var mergeConfig = {
        ...Object.assign({ defaultConfig }),
        ...{ env: { PUBLIC_URL: "/site/zh-TW", BASE_IMG_URL: "" } },
        reactStrictMode: true,
        i18n:{
            locales:['zh-TW'],
            defaultLocale: "zh-TW"
        }
    };
    mergeConfig.env.LOCAL_API_URL = "";
    if (phase == PHASE_DEVELOPMENT_SERVER) {
        mergeConfig.env.LOCAL_API_URL = "http://pinpin_lezada.vagrant";
    }
    if (phase == PHASE_PRODUCTION_SERVER) {
        //
    }

    return mergeConfig;
};
