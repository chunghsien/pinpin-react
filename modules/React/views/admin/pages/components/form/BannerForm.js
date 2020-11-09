
import React, { useState, useEffect, useRef } from 'react';
import { useTranslation } from 'react-i18next';
import {
    CRow, CCol, CFormGroup, CLabel,
    CCard,
    CInput, CSwitch, CSelect, CInputFile,
    CInputGroup, CInputGroupAppend, CInputGroupText,
    CInvalidFeedback,
    CTabContent, CTabPane
} from '@coreui/react'

import { useForm } from "react-hook-form";
import Form from '../Form';

const BannerForm = (props) => {

    const { t } = useTranslation(['translation']);
    const methods = useForm({ mode: 'all' });
    const { register, errors } = methods;
    const matcher = location.pathname.match(/\/\d+$/);
    let href = props.href;
    if (location.pathname.match(/\/\d+$/)) {
        href = href.replace(/\/$/, '') + matcher[0];
    } else {
        href += 'add';
    }
    const [maxLength, setMaxLength] = useState({});
    const [remaining, setRemaining] = useState({});
    const [mediaState, setMediaState] = useState({});
    const [fileRequire, setFileRequire] = useState(true);

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


    const singleFileOnChange = (e) => {
        let reader = new FileReader();
        let dom = null;
        if (e && typeof e.preventDefault == 'function') {
            e.preventDefault();
            dom = e.target;
        } else {
            dom = e;
        }
        let name = dom.name;
        const file = e.target.files[0];
        reader.readAsDataURL(file);
        reader.onload = () => {
            let obj = {};
            obj[name] = {
                path: reader.result,
                mime: 'image/*',
            };
            
            setMediaState((mediaState) => {
                return { ...mediaState, ...obj };
            });
        }
    }

    const count = 0;
    useEffect(() => {
        formRef.current.elements.forEach((dom) => {
            const name = dom.name;
            const _maxLength = dom.maxLength;
            if (_maxLength && _maxLength > 0) {
                let obj = {};
                obj[name] = _maxLength;
                setMaxLength((maxLength) => ({ ...maxLength, ...obj }));
            }
        });

    }, [count]);

    const formRef = useRef();

    return (
        <CTabContent>
            <CTabPane data-tab="banner-form">
                <CCard className="tab-card">
                    <Form innerRef={formRef} href={href} griduse {...methods} {...props} remainderChange={remainderChange} setFileRequire={setFileRequire} setMediaState={setMediaState}>
                        <input type="hidden" name="id" ref={register()} />
                        <input type="hidden" name="table" ref={register()} />
                        <input type="hidden" name="table_id" ref={register()} />
                        <input type="hidden" name="language_id" ref={register()} />
                        <input type="hidden" name="locale_id" ref={register()} />
                        <CRow className="mt-2">
                            <CCol md="6" sm="12">
                                <CFormGroup>
                                    <CLabel>{t('columns-name')}</CLabel>
                                    <CInputGroup className={errors.name && 'is-invalid'}>
                                        <CInput
                                            name="name"
                                            maxLength="64"
                                            onChange={remainderChange}
                                            invalid={errors.name ? true : false}
                                            innerRef={register({ required: true })}
                                        />
                                        <CInputGroupAppend>
                                            <CInputGroupText className="text-muted">{remaining.name ? remaining.name : 0}/{maxLength.name}</CInputGroupText>
                                        </CInputGroupAppend>
                                    </CInputGroup>
                                    <CInvalidFeedback>{(errors.name && errors.name.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                                </CFormGroup>
                            </CCol>
                        </CRow>
                        <CRow className="mt-2">
                            <CCol md="6" sm="12">
                                <CFormGroup>
                                    <CLabel>{t('columns-main_photo')}</CLabel>
                                    <CInputFile
                                        name="main_photo"
                                        innerRef={register({ required: fileRequire })}
                                        invalid={errors.main_photo ? true : false}
                                        onChange={singleFileOnChange}
                                        accept="image/*"
                                     />
                                    <CInvalidFeedback>{(errors.main_photo && errors.main_photo.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                                    <img id="banner-main_photo-preview" className={'mt-2 img-fluid form-thumbnail ' + (mediaState.main_photo ? '': 'd-none') } src={mediaState.main_photo && mediaState.main_photo.path} />
                                </CFormGroup>
                            </CCol>
                            <CCol md="6" sm="12">
                                <CFormGroup>
                                    <CLabel>{t('columns-sub_photo')}</CLabel>
                                    <CInputFile name="sub_photo" type="file" innerRef={register()} onChange={singleFileOnChange} accept="image/*" />
                                    <img id="banner-sub_photo-preview" className={'mt-2 form-thumbnail ' + (mediaState.sub_photo ? '': 'd-none') } src={mediaState.sub_photo && mediaState.sub_photo.path} />
                                </CFormGroup>
                            </CCol>
                        </CRow>
                        <CRow>
                            <CCol md="6" sm="12" className="mt-2">
                                <CFormGroup>
                                    <CLabel>{t('columns-target')}</CLabel>
                                    <CSelect name="target" innerRef={register()}>
                                        <option value="_self">{t('href-self')}</option>
                                        <option value="_blank">{t('href-blank')}</option>
                                    </CSelect>
                                </CFormGroup>
                            </CCol>
                            <CCol md="12" sm="12" className="mt-2">
                                <CFormGroup>
                                    <CLabel>{t('columns-target_link')}</CLabel>
                                    <CInputGroup className={errors.target_link && 'is-invalid'}>
                                        <CInput
                                            name="target_link"
                                            onChange={remainderChange}
                                            innerRef={register()}
                                            maxLength="255"
                                        />
                                        <CInputGroupAppend>
                                            <CInputGroupText className="text-muted">{remaining.target_link ? remaining.target_link : 0}/{maxLength.target_link}</CInputGroupText>
                                        </CInputGroupAppend>
                                    </CInputGroup>
                                </CFormGroup>
                            </CCol>
                            <CCol md="6" sm="12" className="mt-2">
                                <CFormGroup>
                                    <CLabel>{t('columns-is_show')}</CLabel>
                                    <div className="d-blobk">
                                        <CSwitch value={1} defaultChecked name="is_show" variant="opposite" color="primary" innerRef={register()} />
                                    </div>
                                </CFormGroup>
                            </CCol>
                        </CRow>
                    </Form>
                </CCard>
            </CTabPane>
        </CTabContent>
    );
}

export default BannerForm;