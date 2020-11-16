
import React from 'react';
import {
    CCard,CCardBody, CTabPane, CTabContent
} from '@coreui/react'

const CommonMainContent = (props) => {
    return (
        <>
            <CTabContent>
                <CTabPane data-tab="default-form">
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

export default CommonMainContent;