import React, { Suspense, useEffect, useRef, useState } from 'react';
import { useTranslation } from 'react-i18next';
import axios from 'axios';
import { CForm, CCardBody, CCardFooter, CButton, CAlert } from '@coreui/react'
import formBreadItemRename from './formBreadItemRename';
import { notify } from './alertify';
import { useLocation, useHistory, useParams } from "react-router-dom";
import Loading from 'react-fullscreen-loading';

const Form = (props) => {

    //屬性型態的資料表名稱集合，indexOf有媒合要找table及table_id送出要資料
    const accessoryTables = [
        'assets', 'attributes', 'banner', 'facebook_tags', 'documents', 'seo'
    ];

    const [formFieldsValue, setFormFieldsValue] = useState({});

    const { t } = useTranslation(['translation']);
    const { method_or_id } = useParams();
    const { handleSubmit, reset } = props;
    const location = useLocation();
    const history = useHistory();
    let USE_TABLE = props.href.replace(/^\/admin\//, '').replace(/\/\d+$/, '');

    const MAIN_TABLE = location.pathname.replace(/^\/admin\//, '').replace(/\/\d+$/, '').replace(/\/add$/, '');
    const onSubmit = (data) => {
        let href = props.href;
        let method = location.pathname.match(/add$/) ? 'post' : 'put';

        if (typeof cformRef.current.elements.id !== 'undefined') {
            const idElement = cformRef.current.elements.id;
            if (idElement.value) {
                method = 'put';
            } else {
                method = 'post';
            }
        } else {
            method = 'post'
        }
        if (method == 'put') {
            href = '/'+SYS_LANG+'/api' + href;
        } else {
            href = '/'+SYS_LANG+'/api' + href;
        }
        let formData = new FormData();
        const dataKeys = Object.keys(data);
        dataKeys.forEach((name) => {
            if (data[name]) {
                if (typeof data[name] == 'object' && data[name].constructor.name == 'FileList' && data[name].length == 1) {
                    formData.append(name, data[name][0]);
                } else if (data[name].constructor.name != 'FileList') {
                    formData.append(name, data[name]);
                }
            }
        });
        const loadingBackgroundDom = document.getElementById('loading-background');
        loadingBackgroundDom.classList.remove('d-none');
        axios({
            method: 'post',
            url: method == 'post' ? href : href += '?put=1',
            headers: {
                'Content-Type': 'multipart/form-data'
            },
            data: formData,
        }).then((response) => {
            if (response.data.data && response.data.notify) {
                const DATA = response.data.data;
                const NOTIFY = response.data.notify.join("");
                if (response.data.code == 0) {
                    let control_table = props.href.replace(/^\/admin\//, '').replace(/\/\d+$/, '').replace(/\/add$/, '');
                    if (!Array.isArray(DATA) && DATA.id) {
                        const id = DATA.id;
                        //新增完填值
                        if (typeof cformRef.current.elements.id !== 'undefined' && id) {
                            cformRef.current.elements.id.value = id;
                            if (typeof props.setFileRequire == 'function') {
                                props.setFileRequire(false);
                            }
                        }
                        let storeObj = {};
                        storeObj[control_table] = response.data.data;
                        //console.log(storeObj[control_table]);
                        
                        if (typeof props.setFormRow !== 'undefined') {
                            props.setFormRow((formRow) => {
                                var item = Object.assign({}, formRow);
                                //console.log(DATA);
                                item[MAIN_TABLE] = DATA;
                                return item;
                            });
                        }
                        


                        if (typeof response.data.data.language_id !== 'undefined' && typeof response.data.data.locale_id !== 'undefined') {
                            storeObj[control_table]['language_has_locale'] = JSON.stringify({
                                locale_id: response.data.data.locale_id,
                                language_id: response.data.data.language_id
                            });
                        }
                        setFormFieldsValue((formFieldsValue) => ({ ...formFieldsValue, ...storeObj }));
                        if (typeof DATA.options !== 'undefined' && props.setReactSelectOptions) {
                            props.setReactSelectOptions((reactSelectOptions) => {
                                return {
                                    ...reactSelectOptions,
                                    ...{
                                        options: DATA.options,
                                        values: DATA.values,
                                        defaultvalues: DATA.defaultvalues,
                                    }
                                }
                            });
                        }
                        if (localStorage.getItem('copyId')) {
                            localStorage.removeItem('copyId');
                        }
                    } else {
                        if (resetAssets(response)) {
                            return;
                        }
                    }

                    notify('success', t(NOTIFY), 3, () => {
                        loadingBackgroundDom.classList.add('d-none');
                        if (props.griduse) {
                            if (control_table == MAIN_TABLE) {
                                const newHistory = location.pathname.replace(/add$/, DATA.id);
                                history.push(newHistory);
                            }
                        }
                    });
                } else {
                    if (response.data.code == -2) {
                        notify('error', t('admin-session-fail'), 3, () => {
                            location.href = '/admin/login';
                        });
                    }
                    notify('error', t(NOTIFY));
                }
            } else {
                notify('error', 'System error.');
            }
            loadingBackgroundDom.classList.add('d-none');
        }).catch((error) => {
            loadingBackgroundDom.classList.add('d-none');
            if (error.response && typeof error.response.data.notify !== 'undefined') {
                var errotNotify = error.response.data.notify.join('');
                if (errotNotify.indexOf('To many files')) {
                    let count = document.getElementsByClassName('assets-thumbnail').length;
                    notify('error', t(errotNotify, { max: 7, count: count }));
                } else {
                    notify('error', errotNotify);
                }

            } else {
                notify('error', 'System error.');
            }

        });
    };

    const resetAssets = (response) => {
        const cFormElement = cformRef.current;
        let control_table = props.href.replace(/^\/admin\//, '').replace(/\/\d+$/, '');
        if (control_table.match(/products_attrs_parent/)) {
            var split = control_table.split('/');
            control_table = split[1];
        }
        //console.log(response);
        if (/*control_table == 'assets' && */typeof props.setFormLists == 'function') {
            if (response) {
                notify('success', t('add success'), 3, () => {
                    var listsData = {};
                    listsData[control_table] = response.data.data;
                    props.setFormLists(() => {
                        return {
                            ...props.formLists,
                            ...listsData
                        }
                    });
                    if (typeof props.notReset !== 'undefined') {
                        return;
                    }
                    if (props.setMediaState) {
                        props.setMediaState({});
                    }

                    cFormElement.reset();
                });
            }
        }
        const loadingBackgroundDom = document.getElementById('loading-background');
        loadingBackgroundDom.classList.add('d-none');
        return /*control_table == 'assets' && */typeof props.setFormLists == 'function';
    }

    const formReset = (e) => {
        e.preventDefault();
        const cFormElement = cformRef.current;
        //沒有id element的狀態下(資料表沒有主鍵，通常是多對多的關聯表)，使用
        if (cFormElement && typeof cFormElement.elements.id === 'undefined') {
            //const url = '/'+SYS_LANG+'/api' + props.href;
            if (props.classRelation) {
                const parent = props.classRelation.parent;
                const parent_id_name = props.classRelation.parent + '_id';
                props.setReactSelectOptions((reactSelectOptions) => {

                    var values = [];
                    var defaultValues = typeof reactSelectOptions.defaultvalues != 'undefined' ? reactSelectOptions.defaultvalues : null;
                    if (typeof reactSelectOptions.defaultvalues != 'undefined') {
                        reactSelectOptions.defaultvalues[parent].forEach((item) => {
                            values.push(item.value);
                        });
                    }

                    if (typeof reactSelectOptions.switchDefaultValues != 'undefined') {
                        reactSelectOptions.switchValues[parent] = Object.assign({}, reactSelectOptions.switchDefaultValues[parent]);
                    }

                    if (typeof cFormElement.elements[parent_id_name] != 'undefined') {
                        cFormElement.elements[parent_id_name].value = values.join(',');
                    }


                    if (typeof props.clearErrors == 'function' && values.length > 0) {
                        props.clearErrors([parent_id_name]);
                    }


                    return {
                        ...reactSelectOptions,
                        values: defaultValues,
                    };
                });
                //getUseRow(url);
                return;
            }
        }


        if (resetAssets()) {
            return;
        }

        //清空reactSelectOptions.values
        if (typeof props.setReactSelectOptions == 'function') {
            props.setReactSelectOptions((reactSelectOptions) => {
                var values = {};
                Object.keys(reactSelectOptions.values).forEach((key) => {
                    values[key] = null;
                });
                return {
                    ...reactSelectOptions,
                    values: values
                }
            });
        }

        if (formFieldsValue[USE_TABLE]) {
            const formData = formFieldsValue[USE_TABLE];
            cFormElement.elements.forEach((elm) => {
                if (elm.tagName.toLowerCase() == 'button') {
                    return;
                }
                const name = elm.name;
                //如果還有其他元件使用react-select begin
                if (typeof props.setReactSelectOptions == 'function') {
                    props.setReactSelectOptions((reactSelectOptions) => {
                        //這邊是把defaultValues塞回去
                        if (typeof reactSelectOptions !== 'undefined' && typeof reactSelectOptions.defaultvalues[name] != 'undefined') {
                            reactSelectOptions.values[name] = reactSelectOptions.defaultvalues[name];
                            var obj = {};
                            obj[name] = reactSelectOptions.defaultvalues[name];
                            return {
                                ...reactSelectOptions,
                                values: {
                                    ...reactSelectOptions.values,
                                    ...obj
                                },
                            };
                        } else {
                            if (typeof reactSelectOptions !== 'undefined') {
                                return reactSelectOptions
                            } else {
                                return {}
                            }
                        }
                    });
                }
                //如果還有其他元件使用react-select end

                if (elm.tagName.toLowerCase() === 'input' && elm.type !== 'file') {
                    if (name != 'password') {
                        elm.value = formData[name] ? formData[name] : null;
                    }
                } else if (elm.type == 'file') {
                    //console.log(elm);
                    //elm.files = null;
                    elm.value = '';
                    //清空上傳的檔案
                    let imgObj = {};
                    if (!formData[name] && props.setMediaState) {
                        //無上傳檔案
                        //imgObj[name] = { path: '' };
                        imgObj[name] = null;
                        props.setMediaState((mediaState) => {
                            return { ...mediaState, ...imgObj };
                        });
                    } else {
                        //有上傳檔案
                        imgObj[name] = { path: formData[name] };
                        props.setMediaState((mediaState) => {
                            return { ...mediaState, ...imgObj };
                        });
                        //imgElm.src = formData[name] ? formData[name] : '';
                    }
                } else if (elm.type == 'checkbox') {
                    if (formData.id) {
                        if (formData[name] == 1) {
                            elm.checked = true;
                        } else {
                            elm.checked = false;
                        }
                    } else {
                        //無資料時
                        elm.checked = true;
                    }
                } else if (elm.tagName.toLowerCase() == 'select') {
                    if (elm.name == 'language_has_locale') {
                        elm.children.forEach((option) => {
                            option.selected = false;
                            if (name == 'language_has_locale' && typeof formData[name] != 'undefined') {
                                const parse1 = JSON.parse(formData[name]);
                                if (option.value) {
                                    const parse2 = JSON.parse(option.value);
                                    if ((parse1.language_id == parse2.language_id) && (parse1.locale_id == parse2.locale_id)) {
                                        option.selected = true;
                                    }
                                }
                            }
                        });
                    }
                    if (elm.name == 'county') {
                        if (typeof props.twZipPacakge == 'object') {
                            var twZipcode = props.twZipPacakge.data;
                            var county = props.selectDefaultValues.county;
                            if (county) {
                                var district = props.selectDefaultValues.district;
                                props.twZipPacakge.states.setTwZipValue(twZipcode[county][district]);
                                cFormElement.elements.county.value = county;
                                cFormElement.elements.district.value = district;
                                props.twZipPacakge.states.setDistrictOptions(() => twZipcode[county]);
                            }
                        }
                    }
                } else {
                    let tmpValue = formData[name];
                    if (name == 'sort' && tmpValue == 16777215) {
                        elm.value = '';
                    } else {
                        if (name != 'password') {
                            elm.value = formData[name] ? formData[name] : '';
                        }
                    }
                }


            });
        } else {
            reset();
            if (typeof props.setMediaState == 'function' && props.setMediaState) {
                props.setMediaState({});
            }

        }
        //更新剩餘字數
        cFormElement.elements.forEach((elm) => {

            if (elm.maxLength && elm.maxLength > 0) {
                const _key = elm.name;
                if (elm.className.match(/ckeditor\-content/)) {
                    if (props.defaultEditorContent) {
                        props.editor[_key].current.editor.setData(props.defaultEditorContent[_key]);
                    }

                } else {
                    props.remainderChange(cFormElement.elements[_key]);
                }
            }
        });
        if (typeof props.setFormResetState != 'undefined') {
            props.setFormResetState((value) => {
                var r = value + 1;
                return r;
            });
        }
    }


    //取得form的編輯資料
    const getUseRow = (url) => {
        const loadingBackgroundDom = document.getElementById('loading-background');
        let tableCheck = url.replace(/\/\d+$/, '').match(/\/\w+$/)[0].replace(/^\//, '');
        //console.log(tableCheck);
        var params = {};
        //console.log([accessoryTables, tableCheck, accessoryTables.indexOf(tableCheck), url]);
        if (accessoryTables.indexOf(tableCheck) > -1) {
            url += ('?table=' + props.table + '&table_id=' + method_or_id);
            url = url.replace(/\/\d+/, '');
        } else if (typeof props.classRelation == 'object') {
            url += '?' + props.classRelation.self + '_id=' + method_or_id;
            url = url.replace(/\/\d+/, '');
        } else {
            if (tableCheck == MAIN_TABLE && !localStorage.getItem('copyId')) {
                url = '/'+SYS_LANG+'/api' + location.pathname;
            } else {
                params.table_id = method_or_id;
            }
        }

        if (tableCheck.match(/_parent$/)) {
            //url+= '?parent_id=' + method_or_id;
            url = url.replace(/\/\d+$/, '');
            url += '?parent_id=' + method_or_id;
        }
        loadingBackgroundDom.classList.remove('d-none');
        axios.get(url, { params: params }).then((response) => {
            const cFormElement = cformRef.current;

            if (/*tableCheck == 'assets' && */typeof props.setFormLists == 'function') {
                var listsData = {};
                listsData[tableCheck] = response.data.data;
                props.setFormLists(() => {
                    return {
                        ...props.formLists,
                        ...listsData
                    }
                });
                return;
            }

            if (typeof response.data.data == 'object' && typeof response.data.data.options == 'object') {
                let _OPTIONS_ = response.data.data.options;
                let _VALUES_ = response.data.data.values;

                let defaultvalues = {};
                if (typeof response.data.data.defaultvalues != 'undefined') {
                    defaultvalues = response.data.data.defaultvalues;
                } else {
                    defaultvalues = _VALUES_;
                }

                Object.keys(defaultvalues).forEach((key) => {
                    var valuePush = [];
                    defaultvalues[key].map((item) => {
                        valuePush.push(item.value);
                    });
                    if (typeof cFormElement.elements[key + '_id'] != 'undefined') {
                        cFormElement.elements[key + '_id'].value = valuePush.join(',');
                    }

                });

                if (typeof props.setReactSelectOptions == 'function') {

                    var switch_values = null;
                    var switch_default_values = null;
                    if (typeof response.data.data.switch_values != 'undefined') {
                        switch_values = response.data.data.switch_values;
                        switch_default_values = Object.assign({}, response.data.data.switch_default_values);
                    }

                    props.setReactSelectOptions((reactSelectOptions) => ({
                        ...reactSelectOptions,
                        options: {
                            ...reactSelectOptions.options,
                            ..._OPTIONS_
                        },
                        values: {
                            ...reactSelectOptions.values,
                            ..._VALUES_
                        },
                        defaultvalues: defaultvalues,
                        switchValues: switch_values,
                        switchDefaultValues: switch_default_values,
                    }));
                }
            }
            const row = response.data.data;
            if (typeof row == 'undefined') {
                return;
            }
            if (typeof props.setFormRow !== 'undefined') {
                props.setFormRow((formRow) => {
                    var item = Object.assign({}, formRow);
                    item[tableCheck] = row;
                    return item;
                });
            }
            const USE_TABLE = url.replace(/\?.*$/, '').replace(/\/\d+$/, '').replace(/^\/api\/admin\//, '');
            let storeObj = {};
            storeObj[USE_TABLE] = row;
            setFormFieldsValue((formFieldsValue) => ({ ...formFieldsValue, ...storeObj }));

            if (row.id && typeof props.setFileRequire === 'function') {
                props.setFileRequire(false);
            }
            if (typeof row.language_id && typeof row.locale_id) {
                const language_has_locale = {
                    language_id: row.language_id,
                    locale_id: row.locale_id
                };
                const toStringObj = {
                    language_has_locale: JSON.stringify(language_has_locale),
                };
                storeObj[USE_TABLE] = { ...row, ...toStringObj };
                setFormFieldsValue((formFieldsValue) => ({ ...formFieldsValue, ...storeObj }));
                if (
                    cFormElement &&
                    typeof cFormElement.elements.language_has_locale != 'undefined' &&
                    typeof cFormElement.elements.language_has_locale.options != 'undefined'

                ) {

                    var zipData = null;
                    if (typeof props.twZipPacakge != 'undefined') {
                        zipData = props.twZipPacakge.data;
                    }

                    cFormElement.elements.language_has_locale.options.forEach((dom) => {
                        dom.selected = false;
                        //console.log(domValue);
                        if (dom.value) {
                            const domValue = JSON.parse(dom.value);
                            if (domValue.language_id == language_has_locale.language_id) {
                                if (domValue.locale_id == language_has_locale.locale_id) {
                                    //dom.selected = true;
                                    if (typeof props.setSelectInterlocking == 'function') {
                                        props.setSelectInterlocking((selectInterlocking) => {
                                            return {
                                                ...selectInterlocking,
                                                ...{ language_has_locale: dom.value }
                                            }
                                        });
                                    } else {
                                        cFormElement.elements.language_has_locale.value = dom.value;
                                    }
                                }
                            }
                        }
                    });
                }
            }
            if (typeof row == 'object') {
                loadingBackgroundDom.classList.add('d-none')
                const fields = Object.keys(row);
                fields.forEach((key) => {
                    if (/^password/.test(key)) {
                        props.setPasswordRequire(false);
                        return;
                    }
                    if (typeof props.setInterlockingValues !== 'undefined' && typeof props.interlockingEvents !== 'undefined') {
                        const interlockingEvents = props.interlockingEvents;
                        if (typeof interlockingEvents[key] == 'function') {
                            interlockingEvents[key](cFormElement.elements[key].value, row);
                        }
                    }

                    let value = row[key];
                    if (key == 'sort' && value == 16777215) {
                        value = '';
                    }
                    if (location.pathname.match(/\/add$/) && key == 'id') {
                        return;
                    }
                    if (cFormElement && typeof cFormElement.elements[key] != 'undefined') {
                        if (key.match(/^is_/)) {
                            //for switch component
                            if (cFormElement.elements[key].type == 'checkbox') {
                                if (value == 1) {
                                    cFormElement.elements[key].checked = true;
                                }
                                if (value == 0) {
                                    cFormElement.elements[key].checked = false;
                                }
                            }
                            //for image field
                        } else if (key.match(/(photo|path|image|video|banner|audio)$/)) {
                            if (!localStorage.getItem('copyId')) {
                                let obj = {};
                                obj[key] = {
                                    path: value
                                };
                                //console.log(obj);
                                props.setMediaState((mediaState) => ({ ...mediaState, ...obj }));
                            }
                        } else {
                            const tagname = cFormElement.elements[key].tagName.toLowerCase();
                            if (tagname == 'input' || tagname == 'textarea') {
                                cFormElement.elements[key].value = value;
                            }
                            const ignoreKeys = ['language_has_locale'];
                            if (tagname == 'select' && value) {
                                if (ignoreKeys.indexOf(key) == -1) {
                                    cFormElement.elements[key].value = value;
                                }
                                let merge = {};
                                merge[key] = value;
                                if (typeof props.setSelectDefaultValues == 'function') {
                                    props.setSelectDefaultValues((selectDefaultValues) => ({
                                        ...selectDefaultValues,
                                        ...merge
                                    }));
                                }

                                if ((new RegExp('tw-zipcode-district')).test(cFormElement.elements[key].id)) {
                                    var county = cFormElement.elements['county'].value;
                                    props.twZipPacakge.states.setDistrictOptions(zipData[county]);
                                    cFormElement.elements[key].value = value;
                                    props.twZipPacakge.states.setTwZipValue(zipData[county][value]);
                                }
                            }
                        }
                        const maxLength = cFormElement.elements[key].maxLength;
                        if (maxLength && maxLength > 0) {
                            if (typeof props.remainderChange == 'function') {
                                props.remainderChange(cFormElement.elements[key]);
                            }
                        }
                        //編輯器賦值
                        if (cFormElement.elements[key].className.match(/editor\-content/) && typeof props.editor != 'undefined') {
                            props.editor[key].current.editor.setData(value)
                        }
                    }
                });
            }

        }).catch((error) => {
            loadingBackgroundDom.classList.add('d-none');
            console.error(error);
        });

    }

    const NC = 0;
    useEffect(() => {
        formBreadItemRename(t);
        if (/^\d+$/.test(method_or_id)) {
            const url = '/'+SYS_LANG+'/api' + props.href;
            getUseRow(url);
        } else {
            if (localStorage.getItem('copyId')) {
                const copyId = localStorage.getItem('copyId');
                const url = '/'+SYS_LANG+'/api' + location.pathname.replace(/\/add$/, '/' + copyId);
                getUseRow(url);
            }
        }
        Array.from(document.getElementsByClassName('c-active')).forEach((item) => {
            const navUri = location.pathname.replace(/\/\d+$/, '').replace(/\/add$/, '');
            var verfired = new RegExp(navUri + '$');
            if (item.tagName.toLowerCase() == 'li') {
                if (verfired.test(item.children[0].href) === false) {
                    item.classList.remove('c-active');
                    item.children[0].classList.remove('c-active');
                }
            } else {
                if (verfired.test(item.href) === false) {
                    item.classList.remove('c-active');
                }
            }
        });

    }, [NC]);

    const cformRef = props.innerRef ? props.innerRef : useRef();
    const preLoading = (<Loading loading background="rgba(99,111,131,.5)" loaderColor="#321fdb" />);
    return (
        <Suspense fallback={preLoading}>
            {<CCardBody className="pb-0 mb-0">
                <CAlert color="dark" closeButton>* {t('form-tabs-alert')}</CAlert>
            </CCardBody>}
            <CForm innerRef={cformRef} onSubmit={handleSubmit(onSubmit)}>
                <CCardBody>
                    {props.children}
                </CCardBody>
                <CCardFooter>
                    <CButton size="sm" color="primary" type="submit" className="mr-1">
                        <i className="fas fa-check mr-1"></i>
                        <span>{t('form-submit')}</span>
                    </CButton>
                    {
                        typeof props.resetInvisible == 'undefined' &&
                        <CButton className="text-white" size="sm" color="warning" type="button" onClick={formReset}>
                            <i className="fas fa-undo-alt mr-1"></i>
                            <span>{t('form-reset')}</span>
                        </CButton>
                    }
                </CCardFooter>
            </CForm>
        </Suspense>
    );
}

export default Form;