import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';

import BackendAdapter from 'i18next-multiload-backend-adapter';
import HttpApi from 'i18next-http-backend';
import LanguageDetector from 'i18next-browser-languagedetector';

i18n.use(BackendAdapter).use(LanguageDetector).use(initReactI18next).init({
    lng: document.getElementsByTagName('html')[0].lang,
    fallbackLng: {
        'zh': ['zh-TW'],
        'zh-Hant': ['zh-TW'],
        'default': ['zh-TW']
    },
    //debug: true,
    backend: {
        backend: HttpApi,
        backendOption: {
            loadPath: '/locales/resources.json?lng={{lng}}&ns={{ns}}',
            allowMultiLoading: true,
            customHeaders: {
                "Content-Type": "application/json",
            }
        }
    }
});

export default i18n;

/**
    backend: [HttpApi],
    backendOption: {
        loadPath: '/locales/{{lng}}/{{ns}}.json',
        allowMultiLoading: true,
        customHeaders: {
            "content-type": "application/json",
            //authorization: document.getElementsByName('csrf-token')[0].content,
        },
    },

 */