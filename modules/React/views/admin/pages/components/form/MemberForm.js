
import React, { useState, useRef, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import {
    CRow, CCol, CFormGroup, CLabel,
    CCard,
    CSelect, CInput,
    CInputGroup, CInputGroupAppend, CInputGroupText,
    CInvalidFeedback,
    CTabContent, CTabPane

} from '@coreui/react'

import { useForm } from "react-hook-form";
import Form from '../Form';
import twZipcode from './commons/twZipcode';

const { customFilterStandard } = require('../react-bootstrap-tables/customFilterStandard').default;

const MemberForm = (props) => {
    const { t } = useTranslation(['translation']);
    const methods = useForm({ mode: 'all' });
    const { register, errors, watch } = methods;
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
        language_has_locale: null,
        county: null,
        district: null,
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

    //const [countyValue, setCountyValue] = useState('基隆市');
    const [districtOptions, setDistrictOptions] = useState(twZipcode['基隆市']);
    const onCounty = (e) => {
        var county = '';
        if (typeof e == 'string') {
            county = e;
        } else {
            county = e.currentTarget.value;
        }
        var district = twZipcode[county];
        setDistrictOptions(district);
        var firstDistrict = Object.keys(district)[0];
        setDistrictOptions(twZipcode[county]);
        onDistrict(firstDistrict);
    }

    const [twZipValue, setTwZipValue] = useState(twZipcode['基隆市']['仁愛區']);
    const onDistrict = (e) => {
        var district = '';
        if (typeof e == 'string') {
            district = e;
        } else {
            district = e.currentTarget.value;
        }
        var county = document.getElementById('tw-zipcode-county').value;
        var zip = twZipcode[county][district];
        setTwZipValue(zip);
    }

    const twZipPacakge = {
        data: twZipcode,
        states: {
            setDistrictOptions: setDistrictOptions,
            setTwZipValue: setTwZipValue
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
                //使用字數初始化
                remainderChange(dom);
                setMaxLength((maxLength) => ({ ...maxLength, ...obj }));
            }
        });
    }, [count]);

    const [passwordRequire, setPasswordRequire] = useState(true);

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
                        setPasswordRequire={setPasswordRequire}
                        setSelectDefaultValues={setSelectDefaultValues}
                        selectDefaultValues={selectDefaultValues}
                        twZipPacakge={twZipPacakge}
                        {...props}
                    >
                        <input type="hidden" name="id" ref={register()} />
                        <CRow>
                            <CCol md="6" sm="12" className="mt-2">
                                <CFormGroup>
                                    <CLabel>{t('columns-language_has_locale')}</CLabel>
                                    <CSelect name="language_has_locale" custom innerRef={register()}>
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
                                    <CLabel>{t('columns-email')}</CLabel>
                                    <CInputGroup className={errors.email && 'is-invalid'}>
                                        <CInput
                                            invalid={errors.email ? true : false}
                                            name="email"
                                            maxLength="384"
                                            onChange={remainderChange}
                                            innerRef={register({ required: true, pattern: customFilterStandard.email })}
                                        />
                                        <CInputGroupAppend>
                                            <CInputGroupText className="bg-light text-muted">{remaining.email ? remaining.email : 0}/{maxLength.email}</CInputGroupText>
                                        </CInputGroupAppend>
                                    </CInputGroup>
                                    <CInvalidFeedback>{errors.email && t('The input is not a valid email address(dot) Use the basic format local-part@hostname')}</CInvalidFeedback>
                                </CFormGroup>
                            </CCol>
                            <CCol md="6" sm="12" className="mt-2">
                                <CFormGroup>
                                    <CLabel>{t('columns-full_name')}</CLabel>
                                    <CInputGroup className={errors.full_name && 'is-invalid'}>
                                        <CInput
                                            invalid={errors.full_name ? true : false}
                                            name="full_name"
                                            maxLength="10"
                                            onChange={remainderChange}
                                            innerRef={register({ required: true })}
                                        />
                                        <CInputGroupAppend>
                                            <CInputGroupText className="bg-light text-muted">{remaining.full_name ? remaining.full_name : 0}/{maxLength.full_name}</CInputGroupText>
                                        </CInputGroupAppend>
                                    </CInputGroup>
                                    <CInvalidFeedback>{errors.full_name && t('The input is an empty string')}</CInvalidFeedback>
                                </CFormGroup>
                            </CCol>
                            <CCol md="6" sm="12" className="mt-2">
                                <CFormGroup>
                                    <CLabel>{t('columns-cellphone')}</CLabel>
                                    <CInputGroup className={errors.cellphone && 'is-invalid'}>
                                        <CInput
                                            invalid={errors.cellphone ? true : false}
                                            name="cellphone"
                                            maxLength="20"
                                            onChange={remainderChange}
                                            innerRef={register({ required: true, pattern: customFilterStandard.cellphone })}
                                        />
                                        <CInputGroupAppend>
                                            <CInputGroupText className="bg-light text-muted">{remaining.cellphone ? remaining.cellphone : 0}/{maxLength.cellphone}</CInputGroupText>
                                        </CInputGroupAppend>
                                    </CInputGroup>
                                    <CInvalidFeedback>{errors.cellphone && t('Invalid type given(dot) String or integer expected')}</CInvalidFeedback>
                                </CFormGroup>
                            </CCol>
                            <CCol md="4" sm="4" className="mt-2">
                                <CFormGroup>
                                    <CLabel>{t('columns-zip')}</CLabel>
                                    <p id="tw-zipcode-zip" className="tw-zipcode-package">{twZipValue}</p>
                                    <input type="hidden" name="zip" value={twZipValue} ref={register()} />
                                </CFormGroup>
                            </CCol>
                            <CCol md="4" sm="4" className="mt-2">
                                <CFormGroup>
                                    <CLabel>{t('columns-county')}</CLabel>
                                    <CSelect
                                        id="tw-zipcode-county"
                                        invalid={errors.county ? true : false}
                                        custom
                                        name="county"
                                        onChange={onCounty}
                                        defaultValue={selectDefaultValues.county}
                                        innerRef={register({ required: true })}
                                    >
                                        {
                                            Object.keys(twZipcode).map((item, key) => {
                                                return (<option key={'county_' + key} value={item}>{item}</option>)
                                            })
                                        }
                                    </CSelect>
                                    <CInvalidFeedback>{(errors.county && errors.county.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                                </CFormGroup>
                            </CCol>
                            <CCol md="4" sm="4" className="mt-2">
                                <CFormGroup>
                                    <CLabel>{t('columns-district')}</CLabel>
                                    <CSelect
                                        id="tw-zipcode-district"
                                        className="tw-zipcode-package"
                                        invalid={errors.district ? true : false}
                                        custom
                                        name="district"
                                        onChange={onDistrict}
                                        innerRef={register({ required: true })}
                                        defaultValue={selectDefaultValues.district}
                                    >
                                        {
                                            Object.keys(districtOptions).map((item, key) => {
                                                return (<option key={'district_' + key} value={item}>{item}</option>)
                                            })
                                        }
                                    </CSelect>
                                    <CInvalidFeedback>{errors.district && t('The input is an empty string')}</CInvalidFeedback>
                                </CFormGroup>
                            </CCol>

                            <CCol sm="12" className="mt-2">
                                <CFormGroup>
                                    <CLabel>{t('columns-address')}</CLabel>
                                    <CInputGroup className={errors.address && 'is-invalid'}>
                                        <CInput
                                            invalid={errors.address ? true : false}
                                            name="address"
                                            maxLength="384"
                                            onChange={remainderChange}
                                            innerRef={register({ required: true })}
                                        />
                                        <CInputGroupAppend>
                                            <CInputGroupText className="bg-light text-muted">{remaining.address ? remaining.address : 0}/{maxLength.address}</CInputGroupText>
                                        </CInputGroupAppend>
                                    </CInputGroup>
                                    <CInvalidFeedback>{errors.address && t('The input is an empty string')}</CInvalidFeedback>
                                </CFormGroup>
                            </CCol>
                            <CCol md="6" className="mt-2">
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
                            <CCol md="6" className="mt-2">
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
                                    <CInvalidFeedback>{errors.password_confirm && t('password not equal password confirm')}</CInvalidFeedback>
                                </CFormGroup>
                            </CCol>
                        </CRow>
                    </Form>
                </CCard>
            </CTabPane>
        </CTabContent>
    );
}

export default MemberForm;