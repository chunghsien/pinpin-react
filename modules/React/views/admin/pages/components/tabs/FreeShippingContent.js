
import React from 'react';
import {
    CCard,CCardBody, CTabPane, CTabContent
} from '@coreui/react'

const FreeShippingContent = (props) => {
    return (
        <>
            <CTabContent>
                <CTabPane data-tab="free-shipping-form">
                    <CCard className="tab-card">
                        <CCardBody>
                            {props.children}
                        </CCardBody>
                    </CCard>
                </CTabPane>
            </CTabContent>
        </>
    );
}

export default FreeShippingContent;