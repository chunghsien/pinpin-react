import React, { useState, useEffect, useRef } from 'react';
import { connect } from "react-redux";
import { useTranslation } from 'react-i18next';
import {
  CRow, CCol, CLabel,
  CCard,
  CInvalidFeedback,
  CTabContent, CTabPane,
  CTextarea
} from '@coreui/react'
import { useForm } from "react-hook-form";
import Form from '../Form';
import CKEditor from 'ckeditor4-react';
CKEditor.editorUrl = 'https://cdn.ckeditor.com/4.15.0/full-all/ckeditor.js';

const DocumentsContentForm = (props) => {
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

  const count = 0;
  const {formRows} = props;
  const {documents_content} = formRows;
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
  }, [documents_content, count]);

  const formRef = useRef();
  const {basePath} = window.pageConfig;
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

  const document_id = location.pathname.match(/\d+$/)[0];

  return (
    <>
      <CTabContent>
        <CTabPane data-tab="documents-content-form">
          <CCard className="tab-card">
            <Form
              innerRef={formRef}
              href={href}
              griduse {...methods}
              remainderChange={remainderChange}
              setMaxLength={setMaxLength}
              defaultEditorContent={{
                content: documents_content ? documents_content.content: ''
              }}
              editor={{content:contentRef}}
              {...props}
            >
              <input type="hidden" name="id" ref={register()} defaultValue={documents_content ? documents_content.id : ''} />
              <input type="hidden" name="documents_id" ref={register()} defaultValue={document_id} />
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
                        data={documents_content ? documents_content.content : ''}
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

export default connect(mapStateToProps)(DocumentsContentForm);
