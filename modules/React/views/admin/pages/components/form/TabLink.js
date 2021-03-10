
import React from 'react';
import { useTranslation } from 'react-i18next';
import { CNavItem, CNavLink } from '@coreui/react'

const TabLink = (props) => {
    const { t } = useTranslation(['translation']);
    const {onClick} = props;
    const NavItemOnclick = (e) => {
      e.preventDefault();
      if(onClick) {
        onClick(e);
      }
    }
    return (
        <CNavItem onClick={NavItemOnclick}>
            <CNavLink data-tab={props.tab}>
                {t(props.label)}
            </CNavLink>
        </CNavItem>
    );
}

export default TabLink;