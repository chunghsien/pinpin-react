
import React, { useState, useRef, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import {
    CRow, CCol, CFormGroup, CLabel,
    CCard,
    CInput,
    CInputGroup, CInputGroupAppend, CInputGroupText,
    CInvalidFeedback,
    CTabContent, CTabPane,
    CSelect

} from '@coreui/react'

import { useForm } from "react-hook-form";
import Form from '../Form';

const MemberRolesForm = (props) => {

    const { t } = useTranslation(['translation']);
    const methods = useForm({ mode: 'all' });
    const { register, errors } = methods;
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

    const [selectDefaultValues, setSelectDefaultValues] = useState({
        language_has_locale: null
    });


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

    return (
        <CTabContent>
            <CTabPane data-tab="default-form">
                <CCard className="tab-card">
                    <Form
                        innerRef={formRef}
                        href={href}
                        griduse {...methods}
                        remainderChange={remainderChange}
                        setMaxLength={setMaxLength}
                        setSelectDefaultValues={setSelectDefaultValues}
                        {...props}
                    >
                        <input type="hidden" name="id" ref={register()} />
                        <CRow className="mt-2">
                                <CCol md="6" sm="12" className="mt-2">
                                    <CFormGroup>
                                        <CLabel>{t('columns-language_has_locale')}</CLabel>
                                        <CSelect
                                            name="language_has_locale"
                                            custom
                                            innerRef={register({ required: true })}
                                        >
                                            {
                                                window.pageConfig.languageOptions.map((item, index) => {
                                                    return (<option key={index} value={item.value}>{item.label}</option>);
                                                })
                                            }
                                        </CSelect>
                                    </CFormGroup>
                                </CCol>
                            <CCol md="6" sm="12" className="mt-2">
                                <CFormGroup>
                                    <CLabel>{t('columns-name')}</CLabel>
                                    <CInputGroup className={errors.name && 'is-invalid'}>
                                        <CInput
                                            invalid={errors.name ? true : false}
                                            name="name"
                                            maxLength="64"
                                            onChange={remainderChange}
                                            innerRef={register({ required: true })}
                                        />
                                        <CInputGroupAppend>
                                            <CInputGroupText className="bg-light text-muted">{remaining.name ? remaining.name : 0}/{maxLength.name}</CInputGroupText>
                                        </CInputGroupAppend>
                                    </CInputGroup>
                                    <CInvalidFeedback>{(errors.name && errors.name.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                                </CFormGroup>
                            </CCol>
                        </CRow>
                    </Form>
                </CCard>
            </CTabPane>
        </CTabContent>
    );
}

export default MemberRolesForm;