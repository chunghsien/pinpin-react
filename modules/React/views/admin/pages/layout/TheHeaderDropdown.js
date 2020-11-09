import React from 'react'
import {
    CDropdown,
    CDropdownItem,
    CDropdownMenu,
    CDropdownToggle
} from '@coreui/react'
//import CIcon from '@coreui/icons-react'
import { useHistory } from "react-router-dom";

const TheHeaderDropdown = (props) => {
    const t = props.trans;
    const history = useHistory();
    
    const linkToProfile = (e) => {
        e.preventDefault();
        history.push('/admin/manager_profile');
    }

    return (
        <CDropdown
            inNav
            className="c-header-nav-items mx-2"
            direction="down"
        >
            <CDropdownToggle className="c-header-nav-link" caret={false}>
                <div className="c-avatar"><i className="fas fa-user-astronaut fa-2x" /></div>
            </CDropdownToggle>
            <CDropdownMenu className="pt-0" placement="bottom-end">
                <CDropdownItem onClick={linkToProfile}>{t('Manager profile')}</CDropdownItem>
                <CDropdownItem href="/admin/logout">{t('Logout')}</CDropdownItem>
            </CDropdownMenu>
        </CDropdown>
    )
}

export default TheHeaderDropdown
