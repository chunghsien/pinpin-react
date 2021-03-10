import React, { useEffect, useState } from 'react';
import {Redirect} from "react-router-dom";
import { useForm } from "react-hook-form";
import Cookies from 'js-cookie';
import { useTranslation } from 'react-i18next';

const LoginComponent = () => {
    const {CContainer, CRow, CCol, CCard, CCardBody, CForm, CInputGroup, CInput, CButton } = require('@coreui/react');
    const basePath = window.pageConfig.basePath;
    let fullScreenBg = (`${basePath}/assets/images/yarenci-hdz-TmMWhXIeh_4-unsplash.jpg`).replace(/^\/{2,}/, '/');
    const backundgroundFullScreenStyle = {
        backgroundImage: `url(${fullScreenBg})`,
        height: '100%',
        backgroundPosition: 'center',
        backgroundRepeat: 'no-repeat',
        backgroundSize: 'cover',
    };
    
    const [siteName, setSiteName] = useState('');
    const lgooIcon = (`${basePath}${pageConfig.system_settings.system.children.comp_logo_icon.value}`).replace(/^\/{2,}/, '/');

    function formSubmit(data) {
        if(data.password && data.account) {
            document.getElementById('registForm').submit();
        }
    }

    function passwordViewToggle() {
        const PwdToggleIcon = document.getElementById('pwd-toggle-icon');
        let className = PwdToggleIcon.className;
        let password_dom = document.getElementById('password');
        if (className.match(/fa\-eye\-slash$/)) {
            className = className.replace(/fa\-eye\-slash/, 'fa-eye');
            password_dom.type = 'text';
        } else {
            className = className.replace(/fa\-eye/, 'fa-eye-slash');
            password_dom.type = 'password';
        }
        PwdToggleIcon.className = className;
    }

    const { register, handleSubmit, errors } = useForm();
    document.body.className = 'header-fixed sidebar-lg-show sidebar-fixed aside-menu-fixed aside-menu-off-canvas';
    const NO_CHANGE = 0;
    useEffect(() => {
        const CSRF = document.getElementsByName('csrf-token')[0].content;
        document.getElementById('__csrf').value = CSRF;
        const error = Cookies.get('error');
        
        if(error) {
            const admin_root = location.pathname.replace(/(\/){1}[a-z|0-9|-]+$/, '');
            Cookies.remove('error',{path: admin_root})
            const alertify = require('alertifyjs/build/alertify');
            alertify.notify(t('laminas-validator:The input is not valid'), 'error', 5);
        }
        const php_lang = document.documentElement.lang.replace('-', '_');
        setSiteName(pageConfig.system_settings.site_info[php_lang].children.name.value);
        //setLgooIcon();
        //return () => {return;}
    }, [NO_CHANGE]);
    
    //console.log(location);
    
    const logined = Cookies.get('admin');
    if(logined) {
        const pathname = location.pathname;
        const redirect = pathname.replace(/\-login$/, '/dashboard')
        return (<Redirect from={pathname} to={redirect} />);
    }
    
    //
    const { t } = useTranslation(['translation', 'admin-login', 'laminas-validator']);
    return (
        <div className="app app-login flex-row align-items-center" style={backundgroundFullScreenStyle}>
            <CContainer>
                <CRow className="justify-content-center">
                    <CCol md="6">
                        <img id="login-icon" src={lgooIcon} />
                        <CCard>
                            <CCardBody>
                                <input id="error-info-provider" type="hidden" />
                                <CForm id="registForm" method="post" onSubmit={handleSubmit(formSubmit)}>
                                    <h2>{/*t('admin-login:Admin system name')*/}{siteName + ' ' + t('admin-login:Admin system name')}</h2>
                                    <p className="text-muted clearfix">{t('admin-login:Sign in to your account')}</p>
                                    <CInputGroup className="mb-3">
                                        <div className="input-group-prepend">
                                            <span className="input-group-text">
                                                <i className="fas fa-user" />
                                            </span>
                                        </div>
                                        <input id="__csrf" type="hidden" name="__csrf" type="hidden" ref={register()} />
                                        <CInput id="account" name="account" type="text" className={errors.account && 'is-invalid'} placeholder={t('admin-login:Account')} innerRef={register({ required: true })} />
                                        {errors.account && <div className="invalid-feedback h6">{t('laminas-validator:The input is an empty string')}</div>}
                                    </CInputGroup>
                                    <CInputGroup className="mb-4">
                                        <div className="input-group-prepend">
                                            <span className="input-group-text">
                                                <i className="fas fa-unlock" />
                                            </span>
                                        </div>
                                        <CInput id="password" name="password" type="password" className={errors.password && 'is-invalid'} placeholder={t('admin-login:Password')} innerRef={register({ required: true })} />
                                        <div className="input-group-prepend" onClick={passwordViewToggle}>
                                            <span type="button" className="input-group-text">
                                                <i id="pwd-toggle-icon" className="text-success fas fa-eye-slash"></i>
                                            </span>
                                        </div>
                                        {errors.password && <div className="invalid-feedback h6">{t('laminas-validator:The input is an empty string')}</div>}
                                    </CInputGroup>
                                    
                                    <CRow>
                                        <CCol md="6"><CButton type="submit" color="info" shape="square">{t('admin-login:Login')}</CButton></CCol>
                                        <CCol md="6" className="text-right"></CCol>
                                    </CRow>
                                </CForm>
                            </CCardBody>
                        </CCard>
                    </CCol>
                </CRow>
            </CContainer>
            <div className="fixed-bottom text-right text-white m-2">
                <span className="mr-1">Photo by</span>
                <a className="text-muted" href="https://unsplash.com/@pekeshorked?utm_source=unsplash&amp;utm_medium=referral&amp;utm_content=creditCopyText">Yarenci Hdz</a>
                <span className="ml-1 mr-1">on</span>
                <a className="text-muted" href="https://unsplash.com/t/wallpapers?utm_source=unsplash&amp;utm_medium=referral&amp;utm_content=creditCopyText">Unsplash</a>
            </div>
        </div>

    );
}

export default function Login() {
 return (
    <>
      <LoginComponent />
    </>
  );    
};