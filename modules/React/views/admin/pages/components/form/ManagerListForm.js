
import React, { useState, useEffect, useRef } from 'react';
import { useTranslation } from 'react-i18next';
import {
    CRow, CCol, CFormGroup, CLabel,
    CCard,
    CInput,
    CInputGroup, CInputGroupAppend, CInputGroupText,
    CInvalidFeedback,
    CTabContent, CTabPane
} from '@coreui/react'
import { useForm } from "react-hook-form";
import Form from '../Form';

const ManagerListForm = (props) => {
    const { t } = useTranslation(['translation']);
    const methods = useForm({ mode: 'all' });
    const { watch, register, errors } = methods;
    let href = props.href;
    const matcher = location.pathname.match(/\/\d+$/);
    if (location.pathname.match(/\/\d+$/)) {
        href = href.replace(/\/$/, '') + matcher[0];
    } else {
        href += '/add';
        href = href.replace(/\/\//, '/');
    }
    const [remaining, setRemaining] = useState({});
    const [maxLength, setMaxLength] = useState({});
    const [passwordRequire, setPasswordRequire] = useState(true);
    
    //欄位剩餘字數
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
        formRef.current.elements.forEach((dom) => {
            const name = dom.name;
            const _maxLength = dom.maxLength;
            if (_maxLength && _maxLength > 0) {
                let obj = {};
                obj[name] = _maxLength;
                //使用字數初始化
                remainderChange(dom);
                setMaxLength((maxLength) => ({ ...maxLength, ...obj }));
            }
        });
    }, [count]);

    const formRef = useRef();
    //https://noembed.com/embed?url={url}

    return (
        <>
            <CTabContent>
                <CTabPane data-tab="default-form">
                    <CCard className="tab-card">
                        <Form
                            innerRef={formRef}
                            href={href}
                            griduse {...methods}
                            remainderChange={remainderChange}
                            setMaxLength={setMaxLength}
                            setPasswordRequire={setPasswordRequire}
                        >
                            <input type="hidden" name="id" ref={register()} />
                            <input type="hidden" name="language_has_locale" value='{"locale_id":"229","language_id":"119"}' ref={register()} />
                            <CRow className="mt-2">
                                <CCol>
                                    <CFormGroup>
                                        <CLabel>{t('account')}</CLabel>
                                        <CInputGroup className={errors.account && 'is-invalid'}>
                                            <CInput
                                                invalid={errors.account ? true : false}
                                                name="account"
                                                maxLength="64"
                                                onChange={remainderChange}
                                                innerRef={register({ required: true })}
                                            />
                                            <CInputGroupAppend>
                                                <CInputGroupText className="bg-light text-muted">{remaining.account ? remaining.account : 0}/{maxLength.account}</CInputGroupText>
                                            </CInputGroupAppend>
                                        </CInputGroup>
                                        <CInvalidFeedback>{(errors.model && errors.model.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                                    </CFormGroup>
                                </CCol>
                            </CRow>
                            <CRow className="mt-2">
                                <CCol>
                                    <CFormGroup>
                                        <CLabel>{t('password')}</CLabel>
                                        <CInputGroup className={errors.password && 'is-invalid'}>
                                            <CInput
                                                invalid={errors.password ? true : false}
                                                name="password"
                                                maxLength="32"
                                                onChange={remainderChange}
                                                innerRef={register({ required: passwordRequire })}
                                            />
                                            <CInputGroupAppend>
                                                <CInputGroupText className="bg-light text-muted">{remaining.password ? remaining.password : 0}/{maxLength.password}</CInputGroupText>
                                            </CInputGroupAppend>
                                        </CInputGroup>
                                        <CInvalidFeedback>{(errors.password && errors.password.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                                    </CFormGroup>
                                </CCol>
                                <CCol>
                                    <CFormGroup>
                                        <CLabel>{t('password confirm')}</CLabel>
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
                                        <CInvalidFeedback>{errors.password_confirm  && t('password not equal password confirm')}</CInvalidFeedback>
                                    </CFormGroup>
                                </CCol>

                            </CRow>
                        </Form>
                    </CCard>
                </CTabPane>
            </CTabContent>
        </>
    );
}

export default ManagerListForm;