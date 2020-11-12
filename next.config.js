const path = require('path');

const {
    PHASE_DEVELOPMENT_SERVER,
    PHASE_PRODUCTION_SERVER
} = require('next/constants')

const ENV = process.env.NODE_ENV;

module.exports = (phase, { defaultConfig }) => {
    if (ENV == 'development' || phase == PHASE_DEVELOPMENT_SERVER) {
        return {
            ...defaultConfig,
            sassOptions: {
                includePaths: [path.join(__dirname, 'src/pages/styles')],
            }
        };

    }
    if (ENV == 'production' || phase == PHASE_PRODUCTION_SERVER) {
        return {
            ...defaultConfig,
            sassOptions: {
                includePaths: [path.join(__dirname, 'src/pages/styles')],
            }
        };
    }
}