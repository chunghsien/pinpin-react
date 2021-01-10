
import React, { useState, useEffect, useRef } from 'react';
import { useTranslation } from 'react-i18next';
import {
  CRow, CCol, CFormGroup, CLabel,
  CCard,
  CInput, CSwitch, CSelect, CInputFile,
  CInputGroup, CInputGroupAppend, CInputGroupText,
  CInvalidFeedback,
  CTabContent, CTabPane, CTextarea
} from '@coreui/react'

import { useForm } from "react-hook-form";
import Form from '../Form';
import CKEditor from 'ckeditor4-react';
CKEditor.editorUrl = 'https://cdn.ckeditor.com/4.15.0/full-all/ckeditor.js';
const CarouselForm = (props) => {

  const { t } = useTranslation(['translation']);
  const methods = useForm({ mode: 'all' });
  const { register, errors } = methods;
  const matcher = location.pathname.match(/\/\d+$/);
  let href = props.href;
  if (location.pathname.match(/\/\d+$/)) {
    href = href.replace(/\/$/, '') + matcher[0];
  } else {
    href += 'add';
    href = href.replace(/\/{2,}/, '/');
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
  const ckeditorConfig = {
    toolbar: [
      { name: 'document', items: ['Source'] },
    ],
    enterMode:/*CKEDITOR.ENTER_BR*/ 2,
    extraPlugins: ['autogrow'],
    autoGrow_maxHeight: 90,
    height: 90,
    autoGrow_bottomSpace: 50,
    resize_enabled: false,
    extraAllowedContent: 'span',
    removeButtons: "Save,NewPage,Preview,Print,ExportPdf,Templates,Form,Radio,Checkbox,TextField,Textarea,Select,Button,ImageButton,HiddenField,Flash",
    removeDialogTabs: 'image:advanced;image:Link'
  };
  const onEditorSetData = (data, elmName) => {
    let dom = window.document.getElementsByName(elmName)[0];
    dom.value = data;
    remainderChange(dom);
  }

  const titleRef = useRef();
  const subtitleRef = useRef();
  
  //editor={{ title: titleRef, subtitle: subtitleRef }}
  //content 編輯器初始化內容
  var titleHTML = ``;
  var subtitleHTML = ``;

  return (
    <CTabContent>
      <CTabPane data-tab={props.tab ? props.tab : 'banner-form'}>
        <CCard className="tab-card">
          <Form
            innerRef={formRef}
            href={href}
            griduse {...methods}
            {...props}
            remainderChange={remainderChange}
            setFileRequire={setFileRequire}
            setMediaState={setMediaState}
            defaultEditorContent={{
              title: titleHTML,
              subtitle: subtitleHTML
            }}
            editor={{ title: titleRef, subtitle: subtitleRef }}
          >
            <input type="hidden" name="id" ref={register()} />
            <input type="hidden" name="type" ref={register()} value="carousel" />
            <input type="hidden" name="language_id" ref={register()} />
            <input type="hidden" name="locale_id" ref={register()} />
            <CRow className="mt-2">
              <CCol>
                <div className="textarea-group">
                  <CLabel>
                    {t('columns-title')}
                  </CLabel>
                  <div className="textarea-group">
                    <CKEditor
                      ref={titleRef}
                      config={ckeditorConfig}
                      data={titleHTML}
                      onChange={(evt) => (onEditorSetData(evt.editor.getData(), 'title'))}
                      onAfterSetData={(e) => (onEditorSetData(e.data.dataValue, 'title'))}
                      onFileUploadResponse={(e) => (onEditorFileUploadResponse(e))}
                    />
                    <CTextarea
                      className="d-none ckeditor-content"
                      invalid={errors.title ? true : false}
                      name="title"
                      maxLength="192"
                      onChange={remainderChange}
                      rows="2"
                      innerRef={register({ required: true })}
                    />
                    <p className="text-right text-muted">{remaining.title ? remaining.title : 0}/{maxLength.title}</p>
                    <CInvalidFeedback className="textarea-invild-feefback">{(errors.title && errors.title.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                  </div>
                </div>
              </CCol>
            </CRow>
            <CRow className="mt-2">
              <CCol>
                <div className="textarea-group">
                  <CLabel>
                    {t('columns-subtitle')}
                  </CLabel>
                  <div className="textarea-group">
                    <CKEditor
                      ref={subtitleRef}
                      config={ckeditorConfig}
                      data={subtitleHTML}
                      onChange={(evt) => (onEditorSetData(evt.editor.getData(), 'subtitle'))}
                      onAfterSetData={(e) => (onEditorSetData(e.data.dataValue, 'subtitle'))}
                      onFileUploadResponse={(e) => (onEditorFileUploadResponse(e))}
                    />
                    <CTextarea
                      className="d-none ckeditor-content"
                      invalid={errors.subtitle ? true : false}
                      name="subtitle"
                      maxLength="192"
                      onChange={remainderChange}
                      rows="2"
                      innerRef={register({ required: true })}
                    />
                    <p className="text-right text-muted">{remaining.subtitle ? remaining.subtitle : 0}/{maxLength.subtitle}</p>
                    <CInvalidFeedback className="textarea-invild-feefback">{(errors.subtitle && errors.subtitle.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                  </div>
                </div>
              </CCol>
            </CRow>

            <CRow className="mt-2">
              <CCol md="6" sm="12">
                <CFormGroup>
                  <CLabel>{t('columns-image')}</CLabel>
                  <CInputFile
                    name="image"
                    innerRef={register({ required: fileRequire })}
                    invalid={errors.image ? true : false}
                    onChange={singleFileOnChange}
                    accept="image/*"
                  />
                  <CInvalidFeedback>{(errors.image && errors.image.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                  <img id="banner-image-preview" className={'mt-2 img-fluid form-thumbnail ' + (mediaState.image ? '' : 'd-none')} src={mediaState.image && mediaState.image.path} />
                </CFormGroup>
              </CCol>
              <CCol md="6" sm="12">
                <CFormGroup>
                  <CLabel>{t('columns-bg_image')}</CLabel>
                  <CInputFile name="bg_image" type="file" innerRef={register()} onChange={singleFileOnChange} accept="image/*" />
                  <img id="banner-bg_image-preview" className={'mt-2 form-thumbnail ' + (mediaState.bg_image ? '' : 'd-none')} src={mediaState.bg_image && mediaState.bg_image.path} />
                </CFormGroup>
              </CCol>
              <CCol md="12" sm="12">
                <CFormGroup>
                  <CLabel>{t('columns-bg_color')}</CLabel>
                  <CInput name="bg_color" type="color" innerRef={register()} />
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
              <CCol md="6" sm="12" className="mt-2">
                <CFormGroup>
                  <CLabel>{t('columns-target_link')}</CLabel>
                  <CInputGroup className={errors.target_link && 'is-invalid'}>
                    <CInput
                      name="url"
                      onChange={remainderChange}
                      innerRef={register()}
                      maxLength="255"
                    />
                    <CInputGroupAppend>
                      <CInputGroupText className="text-muted">{remaining.url ? remaining.url : 0}/{maxLength.url}</CInputGroupText>
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

export default CarouselForm;