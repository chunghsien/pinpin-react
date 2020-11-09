
import React, { useState, useEffect, useRef } from 'react';
import { useTranslation } from 'react-i18next';
import {
    CRow, CCol, CFormGroup, CLabel,
    CCard,
    CSelect, CInput,
    CInputGroup, CInputGroupAppend, CInputGroupText,
    CInvalidFeedback,
    CTabContent, CTabPane
} from '@coreui/react'
//import AsyncSelect from 'react-select/async';
import AsyncSelect from 'react-select';
import { useForm } from "react-hook-form";
import axios from 'axios';
import Form from '../Form';
import { dialog } from '../alertify';

const ProductsSpecGroupForm = (/*props*/) => {
    const { t } = useTranslation(['translation']);
    const methods = useForm({ mode: 'all' });
    const { register, setError, errors, clearErrors } = methods;
    //const matcher = location.pathname.match(/\/\d+$/);
    const [remaining, setRemaining] = useState({});
    const [maxLength, setMaxLength] = useState({});
    //const [fileRequire, setFileRequire] = useState(true);

    const selectMenuStyles = {
        menu: (provided/*, state*/) => {
            return {
                ...provided,
                fontSize: '0.9rem'
            }
        },
        container: (provided/*, state*/) => {
            return {
                ...provided,
                fontSize: '0.9rem'
            }
        }
    };


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
                setMaxLength((maxLength) => ({ ...maxLength, ...obj }));
            }
        });
    }, [count]);

    const formRef = useRef();
    const [reactSelectOptions, setReactSelectOptions] = useState({
        options: {
            products_id: [],
        },
        values: {
            products_id: "",
        },
        defaultvalues: {
            products_id: "",
        }
    });
    const productsIdOnChange = (option) => {
        if (!option) {
            setError('products_id', {
                type: "required",
                message: ""
            });

        } else {
            clearErrors(['products_id']);
        }

        setReactSelectOptions((reactSelectOptions) => {
            return {
                ...reactSelectOptions,
                ...{
                    values: {
                        products_id: option
                    }
                }
            };
        });
    }

    const loadProducts = (languageHasLocale, inputValue) => {
        let data = {
            ...JSON.parse(languageHasLocale),
            word: typeof inputValue == 'string' ? inputValue : '',
            isOptionsRequest: 1
        };
        axios.get('/api/admin/products', { params: data }).then((response) => {
            setReactSelectOptions((reactSelectOptions) => {
                return {
                    ...reactSelectOptions,
                    ...{
                        options: {
                            products_id: response.data.data.options
                        }
                    }
                }
            });
        });
    }

    const loadOptions = (inputValue) => {
        if (formRef.current && inputValue) {
            let languageHasLocale = formRef.current.elements.language_has_locale.value;
            if (!languageHasLocale) {
                dialog("請選擇使用語言。", t);
            } else {
                const filter = reactSelectOptions.options.products_id.filter((option) => {
                    return (new RegExp(inputValue, 'i')).test(option.label) ;
                });
                if(!filter){
                    loadProducts(languageHasLocale, inputValue);
                }
            }
        }
    }

    const focusOptions = (ele) => {
        if (formRef.current) {
            let languageHasLocale = formRef.current.elements.language_has_locale.value;
            if (!languageHasLocale) {
                dialog("請選擇使用語言。", t);
            } else {
                if(reactSelectOptions.options.products_id.length == 0) {
                    loadProducts(languageHasLocale);
                }
                //
            }
        }
    }

    const [selectDefaultValues, setSelectDefaultValues] = useState({
        language_has_locale: null
    });

    return (
        <>
            <CTabContent>
                <CTabPane data-tab="default-form">
                    <CCard className="tab-card">
                        <Form
                            innerRef={formRef}
                            href="/admin/products_spec_group"
                            table="products_spec_group"
                            griduse
                            {...methods}
                            remainderChange={remainderChange}
                            setSelectDefaultValues={setSelectDefaultValues}
                            setReactSelectOptions={setReactSelectOptions}
                        >
                            <input type="hidden" name="id" ref={register()} />
                            <CRow className="mt-2">
                                <CCol md="6" sm="12">
                                    <CFormGroup>
                                        <CLabel>{t('columns-language_has_locale')}</CLabel>
                                        <CSelect name="language_has_locale" custom innerRef={register({ required: true })} defautlvalue="">
                                            <option value="">{t("isUseOptionsDefault")}</option>
                                            {
                                                window.pageConfig.languageOptions.map((item, index) => {
                                                    return (<option key={index} value={item.value}>{item.label}</option>);
                                                })
                                            }
                                        </CSelect>
                                    </CFormGroup>
                                </CCol>
                                <CCol md="6" sm="12">
                                    <CLabel>{t('columns-products_id')}</CLabel>
                                    <AsyncSelect
                                        cacheOptions={true}
                                        onFocus={focusOptions}
                                        onInputChange={loadOptions}
                                        onChange={productsIdOnChange}
                                        styles={selectMenuStyles}
                                        placeholder={t("isUseOptionsDefault")}
                                        options={reactSelectOptions.options.products_id}
                                        value={reactSelectOptions.values.products_id}
                                        defaultValue={reactSelectOptions.defaultvalues.products_id}
                                        isClearable={true}
                                        cacheOptions={true}
                                        noOptionsMessage={() => 'nothing found'}
                                        loadingMessage={() => 'searching...'}
                                    />
                                    <input
                                        value={reactSelectOptions.values.products_id ? reactSelectOptions.values.products_id.value : ''}
                                        defaultValue={reactSelectOptions.defaultvalues.products_id.value}
                                        className={errors.products_id && 'is-invalid'}
                                        name="products_id" type="hidden" ref={register({ required: true })}
                                    />
                                    <CInvalidFeedback>{
                                        (
                                            !reactSelectOptions.values.products_id &&
                                            errors.products_id &&
                                            errors.products_id.type == 'required') && t('The input is an empty string')}
                                    </CInvalidFeedback>

                                </CCol>
                                <CCol md="6" sm="12">
                                    <CFormGroup>
                                        <CLabel>{t('columns-products-spec-group-name')}</CLabel>
                                        <CInputGroup className={errors.name && 'is-invalid'}>
                                            <CInput
                                                invalid={errors.name ? true : false}
                                                name="name"
                                                maxLength="128"
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
                                <CCol md="6" sm="12">
                                    <CFormGroup>
                                        <CLabel>{t('columns-sort')}</CLabel>
                                        <CInput invalid={errors.sort ? true : false} name="sort" type="number" innerRef={register({ min: 0, max: 16777215 })} />
                                        <CInvalidFeedback>{errors.sort && t('The input is not between \'%min%\' and \'%max%\', inclusively', { min: 0, max: 16777215 })}</CInvalidFeedback>
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

export default ProductsSpecGroupForm;