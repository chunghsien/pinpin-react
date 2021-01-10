import { Fragment } from "react";
import "../assets/scss/styles.scss";
import { Provider } from "react-redux";
import { ToastProvider } from "react-toast-notifications";
import { persistStore } from "redux-persist";
import { PersistGate } from "redux-persist/integration/react";
import withReduxStore from "../lib/with-redux-store";
import { initLanguage } from "../redux/actions/languageActions";
//import { changeScreenOverlay } from "../redux/actions/screenOverlayActions";
import Head from 'next/head'

function MyApp({ Component, pageProps, reduxStore, router }) {
  const persistor = persistStore(reduxStore);
  reduxStore.dispatch(initLanguage(router.route));
  return (
    <Fragment>
      <Head>
        <title>Pinpin React 網站快速開發工具</title>
      </Head>
      <ToastProvider placement="bottom-left">
        <Provider store={reduxStore}>
          <PersistGate loading={<Component {...pageProps} />} persistor={persistor}>
            <Component {...pageProps} />
          </PersistGate>
        </Provider>
      </ToastProvider>
    </Fragment>
  )
}

export default withReduxStore(MyApp);