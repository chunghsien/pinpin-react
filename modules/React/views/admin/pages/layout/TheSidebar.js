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
                 <img className="c-sidebar-brand-full" src={pageConfig.system_settings.system.to_config.comp_logo} />
                 <img className="c-sidebar-brand-minimized" src={pageConfig.system_settings.system.to_config.comp_logo} />
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
