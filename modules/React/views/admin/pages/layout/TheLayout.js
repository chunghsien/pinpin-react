import React, { Suspense } from 'react'
import {
    TheContent,
    TheSidebar,
    TheFooter,
    TheHeader
} from './index'

import Loading from 'react-fullscreen-loading';

const TheLayout = () => {
    const preLoading = (<Loading loading background="rgba(99,111,131,.5)" loaderColor="#321fdb" />);
    return (
        <Suspense fallback={preLoading}>
        <div className="c-app c-default-layout">
            <TheSidebar />
            <div className="c-wrapper">
                <TheHeader />
                <div className="c-body">
                    <TheContent />
                </div>
                <TheFooter />
            </div>
        </div>
        </Suspense>
    )
}

export default TheLayout
