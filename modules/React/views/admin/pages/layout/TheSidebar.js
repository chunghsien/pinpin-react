import React from 'react'
import { useSelector, useDispatch } from 'react-redux'
import {
    CCreateElement,
    CSidebar,
    CSidebarBrand,
    CSidebarNav,
    CSidebarNavDivider,
    CSidebarNavTitle,
    CSidebarMinimizer,
    CSidebarNavDropdown,
    CSidebarNavItem,
} from '@coreui/react'

import CIcon from '@coreui/icons-react'

// sidebar nav config
//import navigation from './_nav'

const TheSidebar = () => {
    const dispatch = useDispatch()
    const show = useSelector(state => state.sidebarShow)
    const pageConfig = window.pageConfig;

    return (
        <CSidebar
            show={show}
            onShowChange={(val) => dispatch({ type: 'set', sidebarShow: val })}
        >
            <CSidebarBrand className="d-md-down-none" to="/">
                {
                    pageConfig.system_settings.system.to_config.comp_logo
                        ?
                        <img className="c-sidebar-brand-full" height="35" src={pageConfig.system_settings.system.to_config.comp_logo} />
                        :
                        <CIcon
                            className="c-sidebar-brand-full"
                            name="logo-negative"
                            height={35}
                        />

                }
                {
                    pageConfig.system_settings.system.to_config.comp_logo_responsive
                        ?
                        <img className="c-sidebar-brand-full" height="35" src={pageConfig.system_settings.system.to_config.pageConfig.system_settings.system.to_config.comp_logo_responsive} />
                        :
                        <CIcon
                            className="c-sidebar-brand-minimized"
                            name="sygnet"
                            height={35}
                        />

                }
            </CSidebarBrand>
            <CSidebarNav>

                <CCreateElement
                    items={pageConfig.admin_navigation}
                    components={{
                        CSidebarNavDivider,
                        CSidebarNavDropdown,
                        CSidebarNavItem,
                        CSidebarNavTitle
                    }}
                />
            </CSidebarNav>
            <CSidebarMinimizer className="c-d-md-down-none" />
        </CSidebar>
    )
}

export default React.memo(TheSidebar)
