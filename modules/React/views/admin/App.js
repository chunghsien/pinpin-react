import React, { Component, Suspense} from 'react';
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

class App extends Component {
    constructor(props) {
        super(props);
        this.state = { isLoading: true, count: 0 };
    }
    render() {
        
        return (
            <BrowserRouter>
                <Suspense fallback={loading}>
                    <Switch>
                        <Route exact path="/admin/login" name="Login Page" render={props => <Login {...props} />} />
                        <Route exact path="/admin/register" name="Register Page" render={props => <Register {...props} />} />
                        <Route exact path="/admin/404" name="Page 404" render={props => <Page404 {...props} />} />
                        <Route exact path="/admin/500" name="Page 500" render={props => <Page500 {...props} />} />
                        <Route path="/admin/" name="Home" render={props => <TheLayout {...props} />} />
                    </Switch>
                </Suspense>
            </BrowserRouter>
        );
    }
}

export default App;
