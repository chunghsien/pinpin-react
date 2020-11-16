
//所有第一個顯示的tab

import React from 'react';
import { useTranslation } from 'react-i18next';
import { CNavItem, CNavLink } from '@coreui/react'

const MemberListTabLink = () => {
    const { t } = useTranslation(['translation']);
    return (
        <CNavItem>
            <CNavLink data-tab="default-form">
                {t('Default form')}
            </CNavLink>
        </CNavItem>
    );
}

export default MemberListTabLink;