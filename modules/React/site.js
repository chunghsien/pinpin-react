import React from 'react';
import ReactDOM from 'react-dom';
//import { BrowserRouter, Switch, Route, Link } from "react-router-dom";
import { Provider } from 'react-redux'
import { createStore } from 'redux';
import reducerCounter from './views/site/reducers/reducreCounter';

const store = createStore(reducerCounter);

import App from './views/site/App';
import './i18n';
import './scss/site/site.scss';

 ReactDOM.render(
    <Provider store={store}>
        <App />
    </Provider>, 
    document.getElementById('root')
);