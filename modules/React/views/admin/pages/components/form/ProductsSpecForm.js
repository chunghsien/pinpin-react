
import React, { useState, useEffect, useRef } from 'react';
import { useParams } from "react-router-dom";
import { useTranslation } from 'react-i18next';
import {
    CRow, CCol, CFormGroup, CLabel,
    CCard,
    CSelect, CInput, CInputFile,
    CInputGroup, CInputGroupAppend, CInputGroupText,
    CInvalidFeedback,
    CTabContent, CTabPane
} from '@coreui/react'
//import AsyncSelect from 'react-select/async';
import Select from 'react-select';
import { useForm } from "react-hook-form";
import axios from 'axios';
import Form from '../Form';
import { dialog } from '../alertify';

const ProductsSpecForm = (/*props*/) => {
    const { t } = useTranslation(['translation']);
    const methods = useForm({ mode: 'all' });
    const { register, errors } = methods;
    const [remaining, setRemaining] = useState({});
    const [maxLength, setMaxLength] = useState({});
    const [mediaState, setMediaState] = useState({});
    const [fileRequire, setFileRequire] = useState(true);

    const {method_or_id} = useParams();

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

    const getProductsOptions = (languageHasLocale, inputValue) => {
        let params = {
            ...JSON.parse(languageHasLocale),
            word: typeof inputValue == 'string' ? inputValue : '',
            isOptionsRequest: 1
        };
        //let params = JSON.parse(e.currentTarget.value);
        axios.get('/api/admin/products', { params: params }).then((response) => {
            if (response.data.data.options.length) {
                setReactSelectOptions((reactSelectOptions) => {
                    return {
                        ...reactSelectOptions,
                        ...{
                            options: {
                                ...reactSelectOptions.options,
                                products_id: response.data.data.options
                            }
                        }
                    }
                });
            }
        });
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
        if (method_or_id == 'add' || /\d+/.test(method_or_id)) {
            var request = new XMLHttpRequest();
            request.open('GET', '/api/admin/products_spec/getStockStatus', false);
            request.send();
            const stockStatusOptions = JSON.parse(request.responseText).data.options.stock_status;
            setReactSelectOptions((reactSelectOptions) => {
                return {
                    ...reactSelectOptions,
                    ...{
                        options: {
                            ...reactSelectOptions.options,
                            stock_status: stockStatusOptions
                        }
                    }
                }
            });
        }

    }, [count]);

    const formRef = useRef();

    const [reactSelectOptions, setReactSelectOptions] = useState({
        options: {
            products_id: [],
            products_spec_group_id: [],
            stock_status: [],
        },
        values: {
            products_id: null,
            products_spec_group_id: null,
            stock_status: null,
        },
        defaultvalues: {
            products_id: null,
            products_spec_group_id: null,
            stock_status: null,
        }
    });

    const productsIdOnChange = (option) => {
        if (option) {
            formRef.current.elements.products_id.value = option.value;
            formRef.current.elements.products_spec_group_id.value = '';
            setReactSelectOptions((reactSelectOptions) => {
                return {
                    ...reactSelectOptions,
                    ...{
                        options: {
                            ...reactSelectOptions.options,
                            products_spec_group_id: [],
                            stock_status: [],
                        }
                    },
                    ...{
                        values: {
                            products_id: option,
                            products_spec_group_id: {},
                            stock_status: {},
                        }
                    }
                }
            });
            const params = {
                isOptionsRequest: 1,
                products_id: option.value
            };
            axios.get('/api/admin/products_spec_group', { params: params }).then((response) => {
                setReactSelectOptions((reactSelectOptions) => {
                    return {
                        ...reactSelectOptions,
                        ...{
                            options: {
                                ...reactSelectOptions.options,
                                products_spec_group_id: response.data.data.options
                            }
                        }
                    }
                });
            });
        } else {
            formRef.current.elements.products_id.value = '';
            formRef.current.elements.products_spec_group_id.value = '';
            setReactSelectOptions((reactSelectOptions) => {
                return {
                    ...reactSelectOptions,
                    ...{
                        values: {
                            ...reactSelectOptions.values,
                            products_id: null,
                            products_spec_group_id: null
                        }
                    },
                    ...{
                        options: {
                            ...reactSelectOptions.options,
                            products_spec_group_id: []
                        }
                    }
                }
            });
        }
    }

    const loadProductsOptions = (inputValue) => {
        if (formRef.current && inputValue) {
            let languageHasLocale = formRef.current.elements.language_has_locale.value;
            if (!languageHasLocale) {
                dialog("請選擇使用語言。", t);
            } else {
                if (reactSelectOptions.options.products_id.length) {
                    const searchResults = reactSelectOptions.options.products_id.filter((item) => {
                        return (new RegExp(inputValue, 'i')).test(item.label);
                    });
                    if (searchResults.legth == 0) {
                        getProductsOptions(languageHasLocale, inputValue);
                    }
                } else {
                    getProductsOptions(languageHasLocale, inputValue);
                }
            }
        }
    }

    const focusProductsOptions = (/*ele*/) => {
        if (formRef.current) {
            let languageHasLocale = formRef.current.elements.language_has_locale.value;
            if (!languageHasLocale) {
                dialog("請選擇使用語言。", t);
            } else {
                if (reactSelectOptions.options.products_id.length == 0) {
                    getProductsOptions(languageHasLocale);
                }
            }
        }
    }

    const languageOnChange = (e) => {
        setReactSelectOptions((reactSelectOptions) => {
            return {
                ...reactSelectOptions,
                ...{
                    options: {
                        ...reactSelectOptions.options,
                        products_id: [],
                        products_spec_group_id: [],
                    },
                },
                ...{
                    values: {
                        products_id: null,
                        products_spec_group_id: null,
                        stock_status: null,
                    }
                }
            }
        });
        e.preventDefault();
        if (e.currentTarget.value) {
            const languageHasLocale = e.currentTarget.value;
            getProductsOptions(languageHasLocale);
        } else {
            setReactSelectOptions((reactSelectOptions) => {
                return {
                    ...reactSelectOptions,
                    ...{
                        options: {
                            ...reactSelectOptions.options,
                            products_id: [],
                            products_spec_group_id: [],
                        }
                    },
                    ...{
                        values: {
                            products_id: null,
                            products_spec_group_id: null,
                            stock_status: null,
                        }
                    }
                }
            });
        }
    }

    const productsSpecGroupOnFocus = (/*inputValue*/) => {
        if (formRef.current) {
            const products_id = formRef.current.elements.products_id.value;
            if (!products_id) {
                dialog("請選擇對應產品。", t);
            }
        }

    }

    const productsSpecGroupOnChange = (option) => {
        if (option) {
            formRef.current.elements.products_spec_group_id.value = option.value;
            setReactSelectOptions((reactSelectOptions) => {
                return {
                    ...reactSelectOptions,
                    ...{
                        values: {
                            ...reactSelectOptions.values,
                            products_spec_group_id: option,
                        }
                    }
                }
            });

        } else {
            formRef.current.elements.products_spec_group_id.value = '';
            setReactSelectOptions((reactSelectOptions) => {
                return {
                    ...reactSelectOptions,
                    ...{
                        values: {
                            ...reactSelectOptions.values,
                            products_spec_group_id: {},
                        }
                    }
                }
            });

        }
    }

    const [selectDefaultValues, setSelectDefaultValues] = useState({});

    return (
        <>
            <CTabContent>
                <CTabPane data-tab="default-form">
                    <CCard className="tab-card">
                        <Form
                            innerRef={formRef}
                            href="/admin/products_spec"
                            table="products_spec"
                            griduse
                            {...methods}
                            remainderChange={remainderChange}
                            selectDefaultValues={selectDefaultValues}
                            setSelectDefaultValues={setSelectDefaultValues}
                            setReactSelectOptions={setReactSelectOptions}
                            setMediaState={setMediaState}
                            setFileRequire={setFileRequire}
                        >
                            <input type="hidden" name="id" ref={register()} />
                            <CRow>
                                <CCol md="4" sm="12" className="mt-2">
                                    <CFormGroup>
                                        <CLabel>{t('columns-language_has_locale')}</CLabel>
                                        <CSelect
                                            className={errors.language_has_locale && 'is-invalid'}
                                            onChange={languageOnChange}
                                            name="language_has_locale"
                                            custom
                                            innerRef={register({ required: true })}
                                            defautlvalue=""
                                        >
                                            <option value="">{t("isUseOptionsDefault")}</option>
                                            {
                                                window.pageConfig.languageOptions.map((item, index) => {
                                                    return (<option key={index} value={item.value}>{item.label}</option>);
                                                })
                                            }
                                        </CSelect>
                                        <CInvalidFeedback>{
                                            (
                                                errors.language_has_locale &&
                                                errors.language_has_locale.type == 'required') && t('The input is an empty string')}
                                        </CInvalidFeedback>

                                    </CFormGroup>

                                </CCol>
                                <CCol md="4" sm="12" className="mt-2">
                                    <CLabel>{t('columns-products_id')}</CLabel>
                                    <Select
                                        onFocus={focusProductsOptions}
                                        onInputChange={loadProductsOptions}
                                        onChange={productsIdOnChange}
                                        styles={selectMenuStyles}
                                        placeholder={t("isUseOptionsDefault")}
                                        options={reactSelectOptions.options.products_id}
                                        value={reactSelectOptions.values.products_id}
                                        defaultValue={reactSelectOptions.defaultvalues.products_id}
                                        isClearable={true}
                                        noOptionsMessage={() => 'nothing found'}
                                        loadingMessage={() => 'searching...'}
                                    />
                                    <input
                                        className={(errors.products_id) && 'is-invalid'}
                                        name="products_id" type="hidden"
                                        ref={register({ required: true })}
                                    />
                                    <CInvalidFeedback>
                                        {
                                            (errors.products_id) && t('The input is an empty string')
                                        }
                                    </CInvalidFeedback>

                                </CCol>
                                <CCol md="4" sm="12" className="mt-2">
                                    <CLabel>{t('columns-products-spec-group-name')}</CLabel>
                                    <Select
                                        onFocus={productsSpecGroupOnFocus}
                                        onChange={productsSpecGroupOnChange}
                                        styles={selectMenuStyles}
                                        placeholder={t("isUseOptionsDefault")}
                                        options={reactSelectOptions.options.products_spec_group_id}
                                        value={reactSelectOptions.values.products_spec_group_id}
                                        defaultValue={reactSelectOptions.defaultvalues.products_spec_group_id}
                                        isClearable={true}
                                        noOptionsMessage={() => 'nothing found'}
                                        loadingMessage={() => 'searching...'}
                                    />
                                    <input name="products_spec_group_id" type="hidden" ref={register()} />
                                </CCol>

                                <CCol md="12" sm="12" className="mt-2">
                                    <CFormGroup>
                                        <CLabel>{t('columns-name')}</CLabel>
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
                                {/*
                                <CCol md="4" sm="12" className="mt-2">
                                    <CFormGroup>
                                        <CLabel>{t('columns-extra_name')}</CLabel>
                                        <CInputGroup className={errors.extra_name && 'is-invalid'}>
                                            <CInput
                                                invalid={errors.extra_name ? true : false}
                                                name="name"
                                                maxLength="128"
                                                onChange={remainderChange}
                                                innerRef={register({ required: true })}
                                            />
                                            <CInputGroupAppend>
                                                <CInputGroupText className="bg-light text-muted">{remaining.extra_name ? remaining.extra_name : 0}/{maxLength.extra_name}</CInputGroupText>
                                            </CInputGroupAppend>
                                        </CInputGroup>
                                        <CInvalidFeedback>{(errors.extra_name && errors.extra_name.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                                    </CFormGroup>
                                </CCol>
                                <CCol md="4" sm="12" className="mt-2">
                                    <CFormGroup>
                                        <CLabel>{t('columns-triple_name')}</CLabel>
                                        <CInputGroup className={errors.triple_name && 'is-invalid'}>
                                            <CInput
                                                invalid={errors.triple_name ? true : false}
                                                name="name"
                                                maxLength="128"
                                                onChange={remainderChange}
                                                innerRef={register({ required: true })}
                                            />
                                            <CInputGroupAppend>
                                                <CInputGroupText className="bg-light text-muted">{remaining.triple_name ? remaining.triple_name : 0}/{maxLength.triple_name}</CInputGroupText>
                                            </CInputGroupAppend>
                                        </CInputGroup>
                                        <CInvalidFeedback>{(errors.triple_name && errors.triple_name.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                                    </CFormGroup>
                                </CCol>
                                */}
                                <CCol md="6" sm="12" className="mt-2">
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
                                        <img id="assets-image-preview" className={'mt-2 img-fluid form-thumbnail ' + (mediaState.main_photo ? '' : 'd-none')} src={mediaState.main_photo && mediaState.main_photo.path} />
                                    </CFormGroup>
                                </CCol>
                                <CCol md="6" sm="12" className="mt-2">
                                    <CFormGroup>
                                        <CLabel>{t('columns-sub_photo')}</CLabel>
                                        <CInputFile
                                            name="sub_photo"
                                            innerRef={register({ required: fileRequire })}
                                            invalid={errors.sub_photo ? true : false}
                                            onChange={singleFileOnChange}
                                            accept="image/*"
                                        />
                                        <CInvalidFeedback>{(errors.sub_photo && errors.sub_photo.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                                        <img id="assets-image-preview" className={'mt-2 img-fluid form-thumbnail ' + (mediaState.sub_photo ? '' : 'd-none')} src={mediaState.sub_photo && mediaState.sub_photo.path} />
                                    </CFormGroup>
                                </CCol>
                                <CCol md="4" sm="12" className="mt-2">
                                    <CFormGroup>
                                        <CLabel>{t('columns-stock')}</CLabel>
                                        <CInput invalid={errors.stock ? true : false} name="stock" type="number" innerRef={register({ required: true })} />
                                        <CInvalidFeedback>{errors.sort && t('The input is an empty string')}</CInvalidFeedback>
                                    </CFormGroup>
                                </CCol>
                                <CCol md="4" sm="12" className="mt-2">
                                    <CFormGroup>
                                        <CLabel>{t('columns-price')}</CLabel>
                                        <CInput invalid={errors.price ? true : false} name="price" type="number" innerRef={register({ required: true })} />
                                        <CInvalidFeedback>{errors.price && t('The input is an empty string')}</CInvalidFeedback>
                                    </CFormGroup>
                                </CCol>
                                <CCol md="4" sm="12" className="mt-2">
                                    <CFormGroup>
                                        <CLabel>{t('columns-real_price')}</CLabel>
                                        <CInput invalid={errors.real_price ? true : false} name="real_price" type="number" innerRef={register({ required: true })} />
                                        <CInvalidFeedback>{errors.real_price && t('The input is an empty string')}</CInvalidFeedback>
                                    </CFormGroup>
                                </CCol>

                                <CCol md="6" sm="12" className="mt-2">
                                    <CFormGroup>
                                        <CLabel>{t('columns-stock_status')}</CLabel>
                                        <CSelect name="stock_status" custom innerRef={register({ required: true })}>
                                            {
                                                reactSelectOptions.options.stock_status.map((item, key) => {
                                                    return <option key={'stoc_status_' + key} value={item.value}>{t(item.label)}</option>
                                                })
                                            }
                                        </CSelect>
                                    </CFormGroup>
                                </CCol>
                                <CCol md="6" sm="12" className="mt-2">
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

export default ProductsSpecForm;