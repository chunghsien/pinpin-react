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
  CTextarea
} from '@coreui/react'
import { useForm } from "react-hook-form";
import Form from '../Form';
import CKEditor from 'ckeditor4-react';
CKEditor.editorUrl = 'https://cdn.ckeditor.com/4.15.0/full-all/ckeditor.js';

const NewsForm = (props) => {
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

  const count = 0;
  const { formRows } = props;
  const news = formRows ? formRows.news : undefined;
  const [formSelected, setFormSelected] = useState({});
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
  }, [news, count]);
  const selectOnChange = (e) => {
    setFormSelected({
      language_has_locale: e.target.value
    });
  }

  const formRef = useRef();
  const { basePath } = window.pageConfig;
  //https://noembed.com/embed?url={url}
  const ckeditorConfig = {
    filebrowserUploadUrl: `${basePath}/${SYS_LANG}/api/admin/editor-upload`.replace(/\/{2,}/, '/'),
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

  const contentRef = useRef();

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
              setFormSelected={setFormSelected}
              editor={{ content: contentRef }}
              defaultEditorContent={{
                content: news ? news.content : ''
              }}
            >
              <input type="hidden" name="id" ref={register()} defaultValue={news ? news.id : ''} />
              <CRow>
                <CCol md="4" sm="12" className="mt-2">
                  <CFormGroup>
                    <CLabel>{t('columns-language_has_locale')}</CLabel>
                    <CSelect
                      name="language_has_locale"
                      custom
                      innerRef={register({ required: true })}
                      value={formSelected ? formSelected.language_has_locale : ''}
                      onChange={(e) => (selectOnChange(e))}
                    >
                      {
                        window.pageConfig.languageOptions.map((item, index) => {
                          return (<option key={index} value={item.value}>{item.label}</option>);
                        })
                      }
                    </CSelect>
                  </CFormGroup>
                </CCol>
                <CCol md="4" sm="12" className="mt-2">
                  <CFormGroup>
                    <CLabel>{t('columns-title')}</CLabel>
                    <CInputGroup className={errors.title && 'is-invalid'}>
                      <CInput
                        invalid={errors.title ? true : false}
                        name="title"
                        maxLength="255"
                        onChange={remainderChange}
                        innerRef={register({ required: true })}
                        defaultValue={news ? news.title : ''}
                      />
                      <CInputGroupAppend>
                        <CInputGroupText className="bg-light text-muted">{remaining.title ? remaining.title : 0}/{maxLength.title}</CInputGroupText>
                      </CInputGroupAppend>
                    </CInputGroup>
                    <CInvalidFeedback>{(errors.title && errors.title.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                  </CFormGroup>
                </CCol>
                <CCol md="4" sm="12" className="mt-2">
                  <CFormGroup>
                    <CLabel>{t('columns-publish')}</CLabel>
                    <CInput
                      className={errors.publish && 'is-invalid'}
                      type="date"
                      invalid={errors.publish ? true : false}
                      name="publish"
                      maxLength="10"
                      onChange={remainderChange}
                      innerRef={register()}
                      defaultValue={news ? news.publish : ''}
                    />
                    <CInvalidFeedback>{(errors.publish && errors.publish.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                  </CFormGroup>
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
                        ref={contentRef}
                        config={ckeditorConfig}
                        data={news ? news.content : ''}
                        onChange={(evt) => (onEditorSetData(evt.editor.getData(), 'content'))}
                        onAfterSetData={(e) => (onEditorSetData(e.data.dataValue, 'content'))}
                        onFileUploadResponse={(e) => (onEditorFileUploadResponse(e))}
                      />
                      <CTextarea
                        className="d-none ckeditor-content"
                        invalid={errors.detail ? true : false}
                        name="content"
                        maxLength="65535"
                        onChange={remainderChange}
                        rows="20"
                        innerRef={register({ required: true })}
                      />
                      <p className="text-right text-muted">{remaining.content ? remaining.content : 0}/{maxLength.content}</p>
                      <CInvalidFeedback className="textarea-invild-feefback">{(errors.content && errors.content.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
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

export default connect(mapStateToProps)(NewsForm);