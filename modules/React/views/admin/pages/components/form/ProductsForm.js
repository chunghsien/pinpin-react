import React, { useState, useEffect, useRef } from 'react';
import { connect } from "react-redux";
import { useTranslation } from 'react-i18next';
import {
  CRow, CCol, CFormGroup, CLabel,
  CCard,
  CSelect, CInput,
  CInputGroup, CInputGroupAppend, CInputGroupText,
  CInvalidFeedback,
  CTabContent, CTabPane,
  CTextarea, CSwitch
} from '@coreui/react'
import { useForm } from "react-hook-form";
import Form from '../Form';
import CKEditor from 'ckeditor4-react';
CKEditor.editorUrl = 'https://cdn.ckeditor.com/4.15.0/full-all/ckeditor.js';

const ProductsForm = (props) => {
  const { t } = useTranslation(['translation']);
  const methods = useForm({ mode: 'all' });
  const { register, errors } = methods;
  let href = props.href;
  const matcher = location.pathname.match(/\/\d+$/);
  if (location.pathname.match(/\/\d+$/)) {
    href = href.replace(/\/$/, '') + matcher[0];
  } else {
    href += '/add';
    href = href.replace(/\/{2,}/, '/');
  }
  const [remaining, setRemaining] = useState({});
  const [maxLength, setMaxLength] = useState({});

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


  const onEditorSetData = (data, elmName) => {
    let dom = window.document.getElementsByName(elmName)[0];
    dom.value = data;
    remainderChange(dom);
  }
  const onEditorFileUploadResponse = (e) => {
    var data = e.data,
      xhr = data.fileLoader.xhr,
      response = JSON.parse(xhr.response);
    const alertify = require('alertifyjs/build/alertify');
    if (response.error) {
      e.stop();
      alertify.notify(t(response.error.message), 'error', 5);
      e.cancel();
    }

  }

  const NC = 0;
  const { formRows } = props;
  const products = (formRows && formRows.products) ? formRows.products : null;
  const [formSelected, setFormSelected] = useState({
    language_has_locale: '',
    manufactures_id: 0
  });
  const [formSwitched, setFormSwitched] = useState({
        is_show: 0,
        is_new: 0,
        is_hot: 0
  });
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
    if (products) {
      setFormSelected({
        language_has_locale: products.language_has_locale,
        manufactures_id: products.manufactures_id
      });
      setFormSwitched({
        is_show: products.is_show,
        is_new: products.is_new,
        is_hot: products.is_hot
      });
    }

  }, [products, NC]);

  const switchOnChange = (e) => {
    var name = e.target.name;
    var value = e.target.checked ? 1 : 0;
    var data = {};
    Object.keys(formSwitched).forEach((key) => {
      if(name == key) {
        data[key] = value;
      }else{
        data[key] = formSwitched[key];
      }
    });
    setFormSwitched(data);
  }

  const selectOnChange = (e) => {
    var elm = e.currentTarget;
    e.preventDefault();
    setFormSelected((selectedState) => {
      let responseState = {};
      let name = elm.name;
      let value = elm.value;
      responseState[name] = value;
      return {
        ...selectedState,
        ...responseState
      };
    });
  }

  const formRef = useRef();
  //https://noembed.com/embed?url={url}
  const ckeditorConfig = {
    filebrowserUploadUrl: '/' + SYS_LANG + '/api/admin/editor-upload',
    //contentsCss: 'http://eyeglad.localhost/assets/css/main.css',
    extraPlugins: ['autogrow', 'embed'],
    autoGrow_maxHeight: 480,
    autoEmbed_widget: 'customEmbed',
    embed_provider: '//ckeditor.iframe.ly/api/oembed?url={url}&callback={callback}',
    autoGrow_bottomSpace: 50,
    resize_enabled: false,
    extraAllowedContent: 'span',
    removeButtons: "Save,NewPage,Preview,Print,ExportPdf,Templates,Form,Radio,Checkbox,TextField,Textarea,Select,Button,ImageButton,HiddenField,Flash",
    removeDialogTabs: 'image:advanced;image:Link'
  };

  const introductionRef = useRef();
  const detailRef = useRef();
  //http://eyeglad.localhost/assets/css/main.css?d=1599922619
  //https://cdn.ckeditor.com/4.15.0/full-all/ckeditor.js
  //interlockings 連動的functions

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
              editor={{
                introduction: introductionRef,
                detail: detailRef,
              }}
              defaultEditorContent={{
                introduction: products ? products.introduction : '',
                detail: products ? products.detail : '',
              }}
              setFormSwitched={setFormSwitched}
              setFormSelected={setFormSelected}
            >
              <input type="hidden" name="id" ref={register()} defaultValue={products ? products.id : undefined} />
              <CRow>
                <CCol md="6" sm="12" className="mt-2">
                  <CFormGroup>
                    <CLabel>{t('columns-language_has_locale')}</CLabel>
                    <CSelect
                      name="language_has_locale"
                      custom
                      innerRef={register({ required: true })}
                      value={formSelected.language_has_locale}
                      onChange={selectOnChange}
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
                    <CLabel>{t('columns-manufactures_id')}</CLabel>
                    <CSelect
                      custom
                      name="manufactures_id"
                      innerRef={register()}
                      value={formSelected.manufactures_id}
                      onChange={selectOnChange}
                    >
                      <option value="0">{t("no_manufactures")}</option>
                      {
                        products &&
                        products.manufactures_options.map((item, index) => {
                          return (<option key={index} value={item.value}>{item.label}</option>);
                        })
                      }
                    </CSelect>
                    <CInvalidFeedback>{(errors.manufactures_id && errors.manufactures_id.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>

                  </CFormGroup>
                </CCol>
                <CCol md="6" sm="12" className="mt-2">
                  <CFormGroup>
                    <CLabel>{t('columns-model')}</CLabel>
                    <CInputGroup className={errors.model && 'is-invalid'}>
                      <CInput
                        invalid={errors.model ? true : false}
                        name="model"
                        maxLength="128"
                        onChange={remainderChange}
                        innerRef={register({ required: true })}
                        defaultValue={products ? products.model : undefined}
                      />
                      <CInputGroupAppend>
                        <CInputGroupText className="bg-light text-muted">{remaining.model ? remaining.model : 0}/{maxLength.model}</CInputGroupText>
                      </CInputGroupAppend>
                    </CInputGroup>
                    <CInvalidFeedback>{(errors.model && errors.model.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                  </CFormGroup>
                </CCol>
                <CCol md="6" sm="12" className="mt-2">
                  <CFormGroup>
                    <CLabel>{t('columns-alias')}</CLabel>
                    <CInputGroup className={errors.alias && 'is-invalid'}>
                      <CInput
                        invalid={errors.alias ? true : false}
                        name="alias"
                        maxLength="128"
                        onChange={remainderChange}
                        innerRef={register()}
                        defaultValue={products ? products.alias : undefined}
                      />
                      <CInputGroupAppend>
                        <CInputGroupText className="bg-light text-muted">{remaining.alias ? remaining.alias : 0}/{maxLength.alias}</CInputGroupText>
                      </CInputGroupAppend>
                    </CInputGroup>
                    <CInvalidFeedback>{(errors.alias && errors.alias.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                  </CFormGroup>
                </CCol>
              </CRow>
              <CRow className="mt-2">
                <CCol md="4" sm="12">
                  <CFormGroup>
                    <CLabel>{t('columns-is_new')}</CLabel>
                    <div className="d-blobk">
                      <CSwitch
                        labelOn={t("is_type_1")}
                        labelOff={t("is_type_0")}
                        size="lg"
                        value={1}
                        name="is_new"
                        variant="opposite"
                        color="primary"
                        innerRef={register()}
                        checked={formSwitched ? formSwitched.is_new == 1 : ''}
                        onChange={switchOnChange}
                      />
                    </div>
                  </CFormGroup>

                </CCol>
                <CCol md="4" sm="12">
                  <CFormGroup>
                    <CLabel>{t('columns-is_hot')}</CLabel>
                    <div className="d-blobk">
                      <CSwitch
                        labelOn={t("is_type_1")}
                        labelOff={t("is_type_0")}
                        size="lg" value={1}
                        name="is_hot"
                        variant="opposite"
                        color="primary"
                        innerRef={register()}
                        checked={formSwitched ? formSwitched.is_hot == 1 : ''}
                        onChange={switchOnChange}
                      />
                    </div>
                  </CFormGroup>

                </CCol>
                <CCol md="4" sm="12">
                  <CFormGroup>
                    <CLabel>{t('columns-is_show')}</CLabel>
                    <div className="d-blobk">
                      <CSwitch
                        labelOn={t("is_type_1")}
                        labelOff={t("is_type_0")}
                        size="lg"
                        value={1}
                        name="is_show"
                        variant="opposite"
                        color="primary"
                        innerRef={register()}
                        checked={formSwitched ? formSwitched.is_show == 1 : ''}
                        onChange={switchOnChange}
                      />
                    </div>
                  </CFormGroup>

                </CCol>
              </CRow>
              <CRow className="mt-2">
                <CCol>
                  <div className="textarea-group">
                    <CLabel>
                      {t('columns-introduction')}
                    </CLabel>
                    <div className="textarea-group">
                      {/*editor*/}
                      <CKEditor
                        ref={introductionRef}
                        config={ckeditorConfig}
                        data={products ? products.introduction : ''}
                        onChange={(evt) => (onEditorSetData(evt.editor.getData(), 'introduction'))}
                        onAfterSetData={(e) => (onEditorSetData(e.data.dataValue, 'introduction'))}
                        onFileUploadResponse={(e) => (onEditorFileUploadResponse(e))}
                      />

                      <CTextarea
                        className="d-none ckeditor-content"
                        invalid={errors.introduction ? true : false}
                        name="introduction" maxLength="1024"
                        onChange={remainderChange}
                        innerRef={register({ required: true })}
                      />
                      <p className="text-right text-muted">{remaining.introduction ? remaining.introduction : 0}/{maxLength.introduction}</p>
                      <CInvalidFeedback className="textarea-invild-feefback">{(errors.introduction && errors.introduction.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                    </div>
                  </div>

                </CCol>
              </CRow>
              <CRow className="mt-2">
                <CCol>
                  <div className="textarea-group">
                    <CLabel>
                      {t('columns-detail')}
                    </CLabel>
                    <div className="textarea-group">
                      <CKEditor
                        ref={detailRef}
                        config={ckeditorConfig}
                        data={products ? products.detail : ''}
                        onChange={(evt) => (onEditorSetData(evt.editor.getData(), 'detail'))}
                        onAfterSetData={(e) => (onEditorSetData(e.data.dataValue, 'detail'))}
                      />
                      <CTextarea
                        className="d-none ckeditor-content"
                        invalid={errors.detail ? true : false}
                        name="detail"
                        maxLength="65535"
                        onChange={remainderChange}
                        rows="20"
                        innerRef={register({ required: true })}
                      />
                      <p className="text-right text-muted">{remaining.detail ? remaining.detail : 0}/{maxLength.detail}</p>
                      <CInvalidFeedback className="textarea-invild-feefback">{(errors.detail && errors.detail.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                    </div>
                  </div>

                </CCol>
              </CRow>
            </Form>
          </CCard>
        </CTabPane>
      </CTabContent>
    </>
  );
}
const mapStateToProps = (state) => {
  return {
    dispatch: state.dispatch,
    formRows: state.formRows,

  };
};

export default connect(mapStateToProps)(ProductsForm);
