//const { PHASE_PRODUCTION_SERVER, PHASE_DEVELOPMENT_SERVER } = require('next/constants');

module.exports = (phase, { defaultConfig }) => {
    var mergeConfig = {
        ...Object.assign({ defaultConfig }),
        ...{ env: { PUBLIC_URL: ""} },
        reactStrictMode: true,
        generateEtags: false,
    };
     mergeConfig.env.LOCAL_API_URI = "http://pinpin_lezada.vagrant";
    return mergeConfig;
};
