
import React from 'react';
import {
    CCard,CCardBody, CTabPane, CTabContent
} from '@coreui/react'

const PaymentContent = (props) => {
    return (
        <>
            <CTabContent>
                <CTabPane data-tab="payment-form">
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

export default PaymentContent;