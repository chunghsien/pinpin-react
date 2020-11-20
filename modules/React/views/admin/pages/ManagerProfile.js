import React, { useEffect, useState, useRef } from 'react';
import { CButton, CInvalidFeedback, CRow, CCol, CCard, CCardBody, CInput, CCardHeader, CTabContent, CTabPane, CLabel, CInputGroup, CCardFooter, CNav, CTabs, CFormGroup, CInputGroupAppend, CInputGroupText } from '@coreui/react'
import { useTranslation } from 'react-i18next';
import simNavLinkClick from './components/simNavLinkClick';
import axios from 'axios';
import { useForm } from "react-hook-form";
import loadable from '@loadable/component';
import notify from '../../../helpers/alertify/notify';
const TabLink = loadable(() => import('./components/form/TabLink'));

const ManagerProfile = (/*props*/) => {

    const { t } = useTranslation(['translation']);

    const methods = useForm({ mode: 'all' });
    const { register, errors, handleSubmit, watch } = methods;

    const [account, setAccount] = useState('');
    const [maxLength, setMaxLength] = useState({});
    const [remaining, setRemaining] = useState({});

    const remainderChange = (e) => {
        let dom = null;
        if (e && typeof e.preventDefault == 'function') {
            e.preventDefault();
            dom = e.target;
        } else {
            dom = e;
        }
        let name = dom.name;
        let tObj = {};
        tObj[name] = dom.value.length;
        //setRemaining({ ...remaining, ...tObj });
        setRemaining((remaining) => ({ ...remaining, ...tObj }));
        return remaining[name];
    }


    const count = 0;
    useEffect(() => {
        //修正階層下拉選單無法開啟的錯誤
        simNavLinkClick();
        formRef.current.elements.forEach((dom) => {
            let name = dom.name;
            let _maxLength = dom.maxLength;
            if (_maxLength && _maxLength > 0) {
                let obj = {};
                obj[name] = _maxLength;
                setMaxLength((maxLength) => ({ ...maxLength, ...obj }));
            }
        });

        axios.get('/'+SYS_LANG+'/api/admin/manager_profile')
            .then((response) => {
                setAccount(response.data.data.account);
            });

    }, [count]);

    const onReset = () => {
        formRef.current.password.value = '';
        formRef.current.password_confirm.value = '';
        setRemaining({
            password: 0,
            password_confirm: 0
        });

    }

    const onupdateSubmit = (data) => {
        let params = data;
        //param.password_confirm;
        axios.post('/'+SYS_LANG+'/api/admin/manager_profile?put=1', params)
            .then((response) => {
                if (response.data.code == 0) {
                    const NOTIFY = response.data.notify.join("");
                    notify('success', t(NOTIFY), 3, () => {
                        onReset();
                    });
                }
            }).catch((error) => {
                console.warn(error);
                notify('error', 'System error.');
            });

    }


    const formRef = useRef();
    return (
        <CTabs id="tabs-root" activeTab="default-form">
            <CNav variant="tabs">
                <TabLink tab="default-form" label="Default form" />
            </CNav>

            <CTabContent>
                <CTabPane data-tab="default-form">
                    <CCard className="tab-card">
                        <CCardHeader>管理員(個人)資訊</CCardHeader>
                        <form method="post" ref={formRef} onSubmit={handleSubmit(onupdateSubmit)}>
                            <CCardBody>

                                <CRow className="mt-2">
                                    <CCol>
                                        <CLabel>帳號</CLabel>
                                        <p><b>{account}</b></p>
                                    </CCol>
                                </CRow>
                                <CRow className="mt-2">
                                    <CCol>
                                        <CFormGroup>
                                            <CLabel>密碼</CLabel>
                                            <CInputGroup className={errors.password && 'is-invalid'}>
                                                <CInput
                                                    invalid={errors.password ? true : false}
                                                    name="password"
                                                    maxLength="32"
                                                    onChange={remainderChange}
                                                    innerRef={register({ required: true, min: 8, max: 20, pattern: /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,20}/ })}
                                                />
                                                <CInputGroupAppend>
                                                    <CInputGroupText className="bg-light text-muted">{remaining.password ? remaining.password : 0}/{maxLength.password}</CInputGroupText>
                                                </CInputGroupAppend>
                                            </CInputGroup>
                                            <CInvalidFeedback>必須包含至少1個數字以及1個大寫和小寫字母，以及至少8個字符，最多可輸入20個字符</CInvalidFeedback>
                                        </CFormGroup>
                                    </CCol>
                                    <CCol>
                                        <CFormGroup>
                                            <CLabel>密碼確認</CLabel>
                                            <CInputGroup className={errors.password_confirm && 'is-invalid'}>
                                                <CInput
                                                    invalid={errors.password_confirm ? true : false}
                                                    name="password_confirm"
                                                    maxLength="32"
                                                    onChange={remainderChange}
                                                    innerRef={register({ validate: (value) => value == watch('password') })}
                                                />
                                                <CInputGroupAppend>
                                                    <CInputGroupText className="bg-light text-muted">{remaining.password_confirm ? remaining.password_confirm : 0}/{maxLength.password_confirm}</CInputGroupText>
                                                </CInputGroupAppend>
                                            </CInputGroup>
                                            <CInvalidFeedback>密碼與密碼確認不相符</CInvalidFeedback>
                                        </CFormGroup>
                                    </CCol>
                                </CRow>

                            </CCardBody>
                            <CCardFooter>
                                <CButton size="sm" color="primary" type="submit" className="mr-1">
                                    <i className="fas fa-check mr-1"></i>
                                    <span>{t('form-submit')}</span>
                                </CButton>
                                <CButton className="text-white" size="sm" color="warning" type="button" onClick={onReset}>
                                    <i className="fas fa-undo-alt mr-1"></i>
                                    <span>{t('form-reset')}</span>
                                </CButton>
                            </CCardFooter>
                        </form>
                    </CCard>
                </CTabPane>
            </CTabContent>
        </CTabs>
    );
};

export default ManagerProfile;