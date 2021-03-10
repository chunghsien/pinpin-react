import React, { Component, Suspense } from 'react';
import { BrowserRouter, Route, Switch } from 'react-router-dom';
import '../../scss/admin/admin.scss';
//為了SSR取代React.lazy
import loadable from '@loadable/component';
//import { withTranslation } from 'react-i18next';

const loading = (
  <div className="pt-3 text-center">
    <div className="sk-spinner sk-spinner-pulse"></div>
  </div>
)

// Containers
const TheLayout = loadable(() => import('./pages/layout/TheLayout'));

// Pages
const Login = loadable(() => import('./pages/Login'));
const Register = loadable(() => import('./pages/Register'));
const Page404 = loadable(() => import('./pages/Page404'));
const Page500 = loadable(() => import('./pages/Page500'));

//console.log(SYS_LANG);

class App extends Component {
  constructor(props) {
    super(props);
    this.state = { isLoading: true, count: 0 };
  }
  render() {
    const basePath = window.pageConfig.basePath;
    const loginPath = `${basePath}/${SYS_LANG}/admin-login`.replace(/^\/{2,}/, '/');
    const registerPath = `${basePath}/${SYS_LANG}/admin-register`.replace(/^\/{2,}/, '/');
    const notFoundPath = `${basePath}/${SYS_LANG}/admin-404`.replace(/^\/{2,}/, '/');
    const errorPath = `${basePath}/${SYS_LANG}/admin-500`.replace(/^\/{2,}/, '/');
    const homePath = `${basePath}/${SYS_LANG}/admin/`.replace(/^\/{2,}/, '/');
    return (
      <BrowserRouter>
        <Suspense fallback={loading}>
          <Switch>
            <Route exact path={loginPath} name="Login Page" render={props => <Login {...props} />} />
            <Route exact path={registerPath} name="Register Page" render={props => <Register {...props} />} />
            <Route exact path={notFoundPath} name="Page 404" render={props => <Page404 {...props} />} />
            <Route exact path={errorPath} name="Page 500" render={props => <Page500 {...props} />} />
            <Route path={homePath} name="Home" render={props => <TheLayout {...props} />} />
          </Switch>
        </Suspense>
      </BrowserRouter>
    );
  }
}

export default App;
