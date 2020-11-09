
import React, { useState, useRef, useEffect } from 'react';
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

const ManagerRolesForm = (props) => {

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
                        {...props}
                    >
                        <input type="hidden" name="id" ref={register()} />
                        <input type="hidden" name="module_id" ref={register()} />
                        <input type="hidden" name="parent_id" ref={register()} />
                        <input type="hidden" name="depth" ref={register()} />
                        <CRow className="mt-2">
                            <CCol>
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

export default ManagerRolesForm;