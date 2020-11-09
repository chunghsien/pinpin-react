import React from 'react'
import { useSelector, useDispatch } from 'react-redux'
import {
  CHeader,
  CToggler,
  CHeaderBrand,
  CHeaderNav,
  CHeaderNavItem,
  CSubheader,
  CBreadcrumbRouter,
} from '@coreui/react'
import CIcon from '@coreui/icons-react'

// routes config
import routes from '../../../../admin_routes'

import { TheHeaderDropdown }  from './index'

import { useTranslation } from 'react-i18next';

const TheHeader = () => {
  const dispatch = useDispatch()
  const sidebarShow = useSelector( (state) => {
    return state.sidebarShow;
  })

  const toggleSidebar = () => {
    const val = [true, 'responsive'].includes(sidebarShow) ? false : 'responsive'
    dispatch({type: 'toggleSideBar', sidebarShow: val})
  }

  const toggleSidebarMobile = () => {
    const val = [false, 'responsive'].includes(sidebarShow) ? true : 'responsive'
    dispatch({type: 'toggleSideBar', sidebarShow: val})
  }
  const { t } = useTranslation(['translation']);
  return (
    <CHeader withSubheader>
      <CToggler
        inHeader
        className="ml-md-3 d-lg-none"
        onClick={toggleSidebarMobile}
      />
      <CToggler
        inHeader
        className="ml-3 d-md-down-none"
        onClick={toggleSidebar}
      />
      <CHeaderBrand className="mx-auto d-lg-none" to="/">
        {/*<CIcon name="logo" height="48" alt="Logo"/>*/}
        {
            pageConfig.system_settings.system.to_config.comp_logo ?
            <img height="48" src={pageConfig.system_settings.system.to_config.comp_logo} /> :
            <CIcon name="logo" height="48" alt="Logo"/>
        }
        
      </CHeaderBrand>

      <CHeaderNav className="d-md-down-none mr-auto">
        <CHeaderNavItem className="px-3" >
          <a className="c-header-nav-link" href="/" target="_blank">
            <i className="fas fa-laptop-house mr-2"></i>
            <span>{t('translation:admin_web site')}</span>
          </a>
        </CHeaderNavItem>
      </CHeaderNav>

      <CHeaderNav className="px-3">
        <CHeaderNavItem className="mr-0" ><b>{window.pageConfig.admin.account}</b></CHeaderNavItem>
        <TheHeaderDropdown trans={t} />
      </CHeaderNav>

      <CSubheader className="px-3 justify-content-between">
        <CBreadcrumbRouter 
          className="border-0 c-subheader-nav m-0 px-0 px-md-3" 
          routes={routes} 
        />
      </CSubheader>
    </CHeader>
  )
}

export default TheHeader
