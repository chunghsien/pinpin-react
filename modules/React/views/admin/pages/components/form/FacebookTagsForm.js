import React, { useState, useEffect, useRef } from 'react';
import { connect } from "react-redux";
import { useTranslation } from 'react-i18next';
import {
  CRow, CCol, CFormGroup, CLabel,
  CCard,
  CInput,
  CTextarea,
  CInputFile,
  CInputGroup, CInputGroupAppend, CInputGroupText,
  CPopover,
  CInvalidFeedback,
  CTabContent, CTabPane
} from '@coreui/react'

import { useForm } from "react-hook-form";
import Form from '../Form';



const FacebookTagsForm = (props) => {

  const { t } = useTranslation(['translation', 'facebook']);
  const methods = useForm({ mode: 'all' });
  const { register, errors } = methods;
  let href = props.href;
  const matcher = location.pathname.match(/\/\d+$/);
  if (location.pathname.match(/\/\d+$/)) {
    href = href.replace(/\/$/, '') + matcher[0];
  } else {
    href += 'add';
  }
  const [remaining, setRemaining] = useState({});
  const [maxLength, setMaxLength] = useState({});
  const [mediaState, setMediaState] = useState({});
  const [fileRequire, setFileRequire] = useState(true);

  const singleFileOnChange = (e) => {
    let reader = new FileReader();
    let dom = null;
    if (e && typeof e.preventDefault == 'function') {
      e.preventDefault();
      dom = e.target;
    } else {
      dom = e;
    }
    const file = e.target.files[0];
    reader.readAsDataURL(file);
    reader.onload = () => {
      let imgEle = null;
      if (dom.nextElementSibling.tagName.toLowerCase() == 'img') {
        imgEle = dom.nextElementSibling;
      }
      if (!imgEle) {
        if (dom.nextElementSibling.nextElementSibling.tagName.toLowerCase() == 'img') {
          imgEle = dom.nextElementSibling.nextElementSibling;
        }
      }
      if (imgEle) {
        imgEle.src = reader.result;
        imgEle.classList.remove('d-none');
      }

    }
  }


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
  const { formRows } = props;
  const facebook_tags = formRows && formRows.facebook_tags ? formRows.facebook_tags : null;
  useEffect(() => {
    formRef.current.elements.forEach((dom) => {
      let name = dom.name;
      let _maxLength = dom.maxLength;
      if (_maxLength && _maxLength > 0) {
        let obj = {};
        obj[name] = _maxLength;
        setMaxLength((maxLength) => ({ ...maxLength, ...obj }));
      }
    });
  }, [facebook_tags, count]);

  const formRef = useRef();

  return (
    <CTabContent>
      <CTabPane data-tab="facebook_tag-form">
        <CCard className="tab-card">
          <Form innerRef={formRef} href={href} griduse {...methods} {...props} remainderChange={remainderChange} setFileRequire={setFileRequire}>
            <input type="hidden" name="id" ref={register()} defaultValue={facebook_tags ? facebook_tags.id : ''} />
            <input type="hidden" name="table" ref={register()} defaultValue={facebook_tags ? facebook_tags.table : ''} />
            <input type="hidden" name="table_id" ref={register()} defaultValue={facebook_tags ? facebook_tags.table_id : ''} />
            <CRow>
              <CCol md="6" sm="12" className="mt-2">
                <CFormGroup>
                  <CLabel>
                    {t('facebook_tags-fb_colon_app_id')}
                    <CPopover content={t('facebook:fb_colon_app_id_popver')}>
                      <i className="fas fa-question-circle ml-1" />
                    </CPopover>
                  </CLabel>
                  <CInputGroup>
                    <CInput
                      name="fb_colon_app_id"
                      maxLength="64"
                      innerRef={register()}
                      readOnly
                      defaultValue={facebook_tags ? facebook_tags.fb_colon_app_id : ''} />
                    <CInputGroupAppend>
                      <CInputGroupText className="text-muted">{remaining.fb_colon_app_id ? remaining.fb_colon_app_id : 0}/{maxLength.fb_colon_app_id}</CInputGroupText>
                    </CInputGroupAppend>
                  </CInputGroup>
                </CFormGroup>
              </CCol>
              <CCol md="6" sm="12" className="mt-2">
                <CFormGroup className={errors.keyword && 'is-invalid'}>
                  <CLabel>
                    {t('facebook_tags-og_colon_title')}
                    <CPopover content={t('facebook:og_colon_title_popver')}>
                      <i className="fas fa-question-circle ml-1" />
                    </CPopover>
                  </CLabel>
                  <CInputGroup className={errors.og_colon_title && 'is-invalid'}>
                    <CInput
                      name="og_colon_title"
                      invalid={errors.og_colon_title ? true : false}
                      maxLength="64"
                      onChange={remainderChange}
                      innerRef={register({ required: true })}
                      defaultValue={facebook_tags ? facebook_tags.og_colon_title : ''}
                    />
                    <CInputGroupAppend>
                      <CInputGroupText className="text-muted">{remaining.og_colon_title ? remaining.og_colon_title : 0}/{maxLength.og_colon_title}</CInputGroupText>
                    </CInputGroupAppend>
                  </CInputGroup>
                  <CInvalidFeedback>{(errors.og_colon_title && errors.og_colon_title.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                </CFormGroup>
              </CCol>
              <CCol md="12" sm="12" className="mt-2">
                <CFormGroup>
                  <CLabel>
                    {t('facebook_tags-og_colon_url')}
                    <CPopover content={t('facebook:og_colon_url_popver')}>
                      <i className="fas fa-question-circle ml-1" />
                    </CPopover>
                  </CLabel>
                  <CInputGroup className={errors.og_colon_url && 'is-invalid'}>
                    <CInput
                      name="og_colon_url"
                      invalid={errors.og_colon_url ? true : false}
                      maxLength="192"
                      onChange={remainderChange}
                      innerRef={register({ required: true })}
                      defaultValue={facebook_tags ? facebook_tags.og_colon_url : ''}
                    />
                    <CInputGroupAppend>
                      <CInputGroupText className="text-muted">{remaining.og_colon_url ? remaining.og_colon_url : 0}/{maxLength.og_colon_url}</CInputGroupText>
                    </CInputGroupAppend>
                  </CInputGroup>
                  <CInvalidFeedback>{(errors.og_colon_url && errors.og_colon_url.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                </CFormGroup>
              </CCol>
              <CCol md="12" sm="12" className="mt-2">
                <div className="textarea-group">
                  <CLabel>
                    {t('facebook_tags-og_colon_description')}
                    <CPopover content={t('facebook:og_colon_description_popver')}>
                      <i className="fas fa-question-circle ml-1" />
                    </CPopover>
                  </CLabel>
                  <div className="textarea-group">
                    <CTextarea
                      className="noresize"
                      invalid={errors.og_colon_description ? true : false}
                      defaultValue={facebook_tags ? facebook_tags.og_colon_description : ''}
                      name="og_colon_description" maxLength="255" onChange={remainderChange}
                      rows="5"
                      innerRef={register({ required: true })}
                    />
                    <p className="text-right text-muted">{remaining.og_colon_description ? remaining.og_colon_description : 0}/{maxLength.og_colon_description}</p>
                    <CInvalidFeedback className="textarea-invild-feefback">{(errors.og_colon_description && errors.og_colon_description.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                  </div>
                </div>
              </CCol>
              {/*
                            <CCol md="12" sm="12" className="mt-2">
                                <CFormGroup>
                                    <CLabel>
                                        {t('facebook_tags-og_colon_type')}
                                        <CPopover content={t('facebook:og_colon_type_popver')}>
                                            <i className="fas fa-question-circle ml-1" />
                                        </CPopover>
                                    </CLabel>
                                    <div className="textarea-group">
                                        <CTextarea className="noresize" name="og_colon_type" rows="12" maxLength="255" onChange={remainderChange} innerRef={register()} />
                                        <p className="text-right text-muted">{remaining.og_colon_type ? remaining.og_colon_type : 0}/{maxLength.og_colon_type}</p>
                                    </div>
                                </CFormGroup>
                            </CCol>
                            */}
              <CCol md="6" sm="12" className="mt-2">
                <CFormGroup>
                  <CLabel>
                    {t('facebook_tags-og_colon_locale')}
                    <CPopover content={t('facebook:og_colon_locale_popver')}>
                      <i className="fas fa-question-circle ml-1" />
                    </CPopover>
                  </CLabel>
                  <CInputGroup>
                    <CInput
                      name="og_colon_locale"
                      defaultValue={facebook_tags ? facebook_tags.og_colon_locale : ''}
                      maxLength="96"
                      readOnly
                      onChange={remainderChange}
                      innerRef={register()}
                    />
                    <CInputGroupAppend>
                      <CInputGroupText className="text-muted">{remaining.og_colon_locale ? remaining.og_colon_locale : 0}/{maxLength.og_colon_locale}</CInputGroupText>
                    </CInputGroupAppend>
                  </CInputGroup>
                </CFormGroup>
              </CCol>
              <CCol md="6" sm="12" className="mt-2" />
              <CCol md="6" sm="12" className="mt-2">
                <CFormGroup>
                  <CLabel>
                    {t('facebook_tags-og_colon_image')}
                    <CPopover content={t('facebook:og_colon_image_popver')}>
                      <i className="fas fa-question-circle ml-1" />
                    </CPopover>
                  </CLabel>
                  <CInputFile invalid={errors.og_colon_image ? true : false} name="og_colon_image" onChange={singleFileOnChange} innerRef={register({ required: fileRequire })} />
                  <CInvalidFeedback>{(errors.og_colon_image && errors.og_colon_image.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                  <img
                    className={'mt-2 img-fluid form-thumbnail ' + (facebook_tags ? '' : 'd-none')}
                    src={facebook_tags ? facebook_tags.og_colon_image : ''} />

                </CFormGroup>
              </CCol>
              <CCol md="6" sm="12" className="mt-2">
                <CFormGroup>
                  <CLabel>
                    {t('facebook_tags-og_colon_image_colon_secure_url')}
                    <CPopover content={t('facebook:og_colon_image_colon_secure_url_popver')}>
                      <i className="fas fa-question-circle ml-1" />
                    </CPopover>
                  </CLabel>
                  <CInputGroup>
                    <CInput
                      name="og_colon_image_colon_secure_url"
                      defaultValue={facebook_tags ? facebook_tags.og_colon_image_colon_secure_url : ''}
                      readOnly
                      maxLength="255"
                      onChange={remainderChange}
                      innerRef={register()}
                    />
                    <CInputGroupAppend>
                      <CInputGroupText className="text-muted">{remaining.og_colon_image_colon_secure_url ? remaining.og_colon_image_colon_secure_url : 0}/{maxLength.og_colon_image_colon_secure_url}</CInputGroupText>
                    </CInputGroupAppend>
                  </CInputGroup>
                </CFormGroup>
              </CCol>
              <CCol md="4" sm="12" className="mt-2">
                <CFormGroup>
                  <CLabel>
                    {t('facebook_tags-og_colon_image_colon_type')}
                    <CPopover content={t('facebook:og_colon_image_colon_type_popver')}>
                      <i className="fas fa-question-circle ml-1" />
                    </CPopover>
                  </CLabel>
                  <CInputGroup>
                    <CInput
                      name="og_colon_image_colon_type"
                      defaultValue={facebook_tags ? facebook_tags.og_colon_image_colon_type : ''}
                      maxLength="48"
                      readOnly
                      onChange={remainderChange}
                      innerRef={register()}
                    />
                    <CInputGroupAppend>
                      <CInputGroupText className="text-muted">{remaining.og_colon_image_colon_type ? remaining.og_colon_image_colon_type : 0}/{maxLength.og_colon_image_colon_type}</CInputGroupText>
                    </CInputGroupAppend>
                  </CInputGroup>
                </CFormGroup>
              </CCol>
              <CCol md="4" sm="6" className="mt-2">
                <CFormGroup>
                  <CLabel>
                    {t('facebook_tags-og_colon_image_colon_width')}
                    <CPopover content={t('facebook:og_colon_image_colon_width_popver')}>
                      <i className="fas fa-question-circle ml-1" />
                    </CPopover>
                  </CLabel>
                  <CInputGroup>
                    <CInput
                      name="og_colon_image_colon_width"
                      defaultValue={facebook_tags ? facebook_tags.og_colon_image_colon_width : ''}
                      maxLength="32"
                      readOnly
                      onChange={remainderChange}
                      innerRef={register()}
                    />
                    <CInputGroupAppend>
                      <CInputGroupText className="text-muted">{remaining.og_colon_image_colon_width ? remaining.og_colon_image_colon_width : 0}/{maxLength.og_colon_image_colon_width}</CInputGroupText>
                    </CInputGroupAppend>
                  </CInputGroup>
                </CFormGroup>
              </CCol>
              <CCol md="4" sm="6" className="mt-2">
                <CFormGroup>
                  <CLabel>
                    {t('facebook_tags-og_colon_image_height')}
                    <CPopover content={t('facebook:og_colon_image_colon_height_popver')}>
                      <i className="fas fa-question-circle ml-1" />
                    </CPopover>
                  </CLabel>
                  <CInputGroup>
                    <CInput
                      name="og_colon_image_colon_height"
                      defaultValue={facebook_tags ? facebook_tags.og_colon_image_colon_height : ''}
                      maxLength="32"
                      readOnly
                      onChange={remainderChange}
                      innerRef={register()}
                    />
                    <CInputGroupAppend>
                      <CInputGroupText className="text-muted">{remaining.og_colon_image_colon_height ? remaining.og_colon_image_colon_height : 0}/{maxLength.og_colon_image_colon_height}</CInputGroupText>
                    </CInputGroupAppend>
                  </CInputGroup>
                </CFormGroup>
              </CCol>
              <CCol md="6" sm="12" className="mt-2">
                <CFormGroup>
                  <CLabel>
                    {t('facebook_tags-og_colon_video')}
                    <CPopover content={t('facebook:og_colon_video_popver')}>
                      <i className="fas fa-question-circle ml-1" />
                    </CPopover>
                  </CLabel>
                  <CInputFile name="og_colon_video" maxLength="255" onChange={singleFileOnChange} innerRef={register()} accept="video/*" />
                </CFormGroup>
              </CCol>
              <CCol md="6" sm="12" className="mt-2">
                <CFormGroup>
                  <CLabel>
                    {t('facebook_tags-og_colon_video_colon_secure_url')}
                    <CPopover content={t('facebook:og_colon_video_colon_secure_url_popver')}>
                      <i className="fas fa-question-circle ml-1" />
                    </CPopover>
                  </CLabel>
                  <CInputGroup>
                    <CInput
                      name="og_colon_video_colon_secure_url"
                      defaultValue={facebook_tags ? facebook_tags.og_colon_video_colon_secure_url : ''}
                      readOnly
                      type="text"
                      maxLength="255"
                      onChange={remainderChange}
                      innerRef={register()}
                    />
                    <CInputGroupAppend>
                      <CInputGroupText className="text-muted">{remaining.og_colon_video_colon_secure_url ? remaining.og_colon_video_colon_secure_url : 0}/{maxLength.og_colon_video_colon_secure_url}</CInputGroupText>
                    </CInputGroupAppend>
                  </CInputGroup>
                </CFormGroup>
              </CCol>
              <CCol md="4" sm="12" className="mt-2">
                <CFormGroup>
                  <CLabel>
                    {t('facebook_tags-og_colon_video_colon_type')}
                    <CPopover content={t('facebook:og_colon_video_colon_type_popver')}>
                      <i className="fas fa-question-circle ml-1" />
                    </CPopover>
                  </CLabel>
                  <CInputGroup>
                    <CInput
                      name="og_colon_video_colon_type"
                      defaultValue={facebook_tags ? facebook_tags.og_colon_video_colon_type : ''}
                      maxLength="48"
                      onChange={remainderChange}
                      innerRef={register()}
                    />
                    <CInputGroupAppend>
                      <CInputGroupText className="text-muted">{remaining.og_colon_video_colon_type ? remaining.og_colon_video_colon_type : 0}/{maxLength.og_colon_video_colon_type}</CInputGroupText>
                    </CInputGroupAppend>
                  </CInputGroup>
                </CFormGroup>
              </CCol>
              <CCol md="4" sm="6" className="mt-2">
                <CFormGroup>
                  <CLabel>
                    {t('facebook_tags-og_colon_video_colon_width')}
                    <CPopover content={t('facebook:og_colon_video_colon_width_popver')}>
                      <i className="fas fa-question-circle ml-1" />
                    </CPopover>
                  </CLabel>
                  <CInputGroup>
                    <CInput
                      name="og_colon_video_colon_width"
                      defaultValue={facebook_tags ? facebook_tags.og_colon_video_colon_width : ''}
                      maxLength="32"
                      onChange={remainderChange}
                      innerRef={register()}
                    />
                    <CInputGroupAppend>
                      <CInputGroupText className="text-muted">{remaining.og_colon_video_colon_width ? remaining.og_colon_video_colon_width : 0}/{maxLength.og_colon_video_colon_width}</CInputGroupText>
                    </CInputGroupAppend>
                  </CInputGroup>
                </CFormGroup>
              </CCol>
              <CCol md="4" sm="6" className="mt-2">
                <CFormGroup>
                  <CLabel>
                    {t('facebook_tags-og_colon_video_colon_height')}
                    <CPopover content={t('facebook:og_colon_video_colon_height_popver')}>
                      <i className="fas fa-question-circle ml-1" />
                    </CPopover>
                  </CLabel>
                  <CInputGroup>
                    <CInput
                      name="og_colon_video_colon_height"
                      defaultValue={facebook_tags ? facebook_tags.og_colon_video_colon_height : ''}
                      maxLength="32"
                      onChange={remainderChange}
                      innerRef={register()}
                    />
                    <CInputGroupAppend>
                      <CInputGroupText className="text-muted">{remaining.og_colon_video_colon_height ? remaining.og_colon_video_colon_height : 0}/{maxLength.og_colon_video_colon_height}</CInputGroupText>
                    </CInputGroupAppend>
                  </CInputGroup>
                </CFormGroup>
              </CCol>
            </CRow>
          </Form>
        </CCard>
      </CTabPane>
    </CTabContent>
  );
}

const mapStateToProps = (state) => {
  return {
    dispatch: state.dispatch,
    formRows: state.formRows,
  };
};

export default connect(mapStateToProps)(FacebookTagsForm);
