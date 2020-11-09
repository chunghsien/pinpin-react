
import React from 'react';
import {
    CCard,CCardBody, CTabPane, CTabContent
} from '@coreui/react'

const OtherLogisticsContent = (props) => {
    return (
        <>
            <CTabContent>
                <CTabPane data-tab="other-logistics-form">
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

export default OtherLogisticsContent;