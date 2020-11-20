import 'react-app-polyfill/ie11'; // For IE 11 support
import 'react-app-polyfill/stable';
import './helpers/polyfill'

import React from 'react';
import ReactDOM from 'react-dom';
import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
import { Provider } from 'react-redux';

//import * as serviceWorker from './serviceWorker';

import App from './views/admin/App';
import { icons } from './assets/icons'
import './i18n';
import reducers from './views/admin/reducers/reducers';

if(typeof window.SYS_LANG == 'undefined') {
    window.SYS_LANG = 'zh-TW';
}

const initialState = {
    sidebarShow: 'responsive'
}
let store = createStore(reducers, initialState, applyMiddleware(thunk));
React.icons = icons
ReactDOM.hydrate(
    <Provider store={store}>
        <App useSuspense={false} />
    </Provider>,
    document.getElementById('root')
);
// If you want your app to work offline and load faster, you can change
// unregister() to register() below. Note this comes with some pitfalls.
// Learn more about service workers: http://bit.ly/CRA-PWA
//serviceWorker.unregister();
