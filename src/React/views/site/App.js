
import React,{ Component, Suspense } from 'react';
//import { withTranslation } from 'react-i18next';
import Home from './pages/Home';

class App extends Component {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <Suspense fallback="loading...">
                <Home />
            </Suspense>
        );
    }
}

export default App;