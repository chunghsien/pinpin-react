
import React from 'react';
import {
    CCard,CCardBody, CTabPane, CTabContent
} from '@coreui/react'

const SystemSettingDefaultContent = (props) => {
    return (
        <>
            <CTabContent>
                <CTabPane data-tab={props.tab}>
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

export default SystemSettingDefaultContent;