
import React, { useState, useEffect, useRef } from 'react';
import { useTranslation } from 'react-i18next';
import {
    CCard,CCardBody, CTabPane, CTabContent
} from '@coreui/react'

const ShippingContent = (props) => {
    return (
        <>
            <CTabContent>
                <CTabPane data-tab="shipping-form">
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

export default ShippingContent;