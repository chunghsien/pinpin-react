import React, { useState, useEffect, useRef } from 'react';
import { connect } from "react-redux";
import { useTranslation } from 'react-i18next';
import {
  CRow, CCol, CFormGroup, CLabel,
  CCard,
  CInput, CTextarea,
  CInputGroup, CInputGroupAppend, CInputGroupText,
  CInvalidFeedback,
  CTabContent, CTabPane  
} from '@coreui/react'

import { useForm } from "react-hook-form";
import Form from '../Form';

const SeoForm = (props) => {

  const { t } = useTranslation(['translation']);
  const methods = useForm({ mode: 'all' });
  const { register, errors } = methods;
  let href = props.href;
  const matcher = location.pathname.match(/\/\d+$/);
  if (location.pathname.match(/\/\d+$/)) {
    href = href.replace(/\/$/, '') + matcher[0];
  } else {
    href += 'add';
  }
  const [maxLength, setMaxLength] = useState({});
  const [remaining, setRemaining] = useState({});
  //const [fileRequire, setFileRequire] = useState(true);

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
  const seo = formRows && formRows.seo ? formRows.seo : null;

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

  }, [seo, count]);

  const formRef = useRef();


  return (
    <CTabContent>
      <CTabPane data-tab="seo-form">
        <CCard className="tab-card">
          <Form innerRef={formRef} href={href} griduse {...methods} {...props}>
            <input type="hidden" name="id" ref={register()} defaultValue={seo ? seo.id : ''} />
            <input type="hidden" name="table" ref={register()} defaultValue={seo ? seo.table : props.table} />
            <input type="hidden" name="table_id" ref={register()} defaultValue={seo ? seo.table_id : ''} />
            <CRow className="mt-2">
              <CCol md="12" sm="12">
                <CFormGroup>
                  <CLabel>{t('seo-title')}</CLabel>
                  <CInputGroup className={errors.title && 'is-invalid'}>
                    <CInput
                      name="title"
                      invalid={errors.title ? true : false}
                      maxLength="255"
                      onChange={remainderChange}
                      innerRef={register({ required: true })}
                      defaultValue={seo ? seo.title : ''}
                    />
                    <CInputGroupAppend>
                      <CInputGroupText className="text-muted">{remaining.title ? remaining.title : 0}/{maxLength.title}</CInputGroupText>
                    </CInputGroupAppend>
                  </CInputGroup>
                  <CInvalidFeedback>{(errors.title && errors.title.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                </CFormGroup>
              </CCol>
            </CRow>
            <CRow className="mt-2">
              <CCol md="12" sm="12">
                <CFormGroup>
                  <CLabel>{t('seo-keyword')}</CLabel>
                  <CInputGroup className={errors.keyword && 'is-invalid'}>
                    <CInput
                      name="keyword"
                      invalid={errors.keyword ? true : false}
                      type="text"
                      maxLength="255"
                      onChange={remainderChange}
                      innerRef={register({ required: true })}
                      defaultValue={seo ? seo.keyword : ''}
                    />
                    <CInputGroupAppend>
                      <CInputGroupText className="text-muted">{remaining.keyword ? remaining.keyword : 0}/{maxLength.keyword}</CInputGroupText>
                    </CInputGroupAppend>
                  </CInputGroup>
                  <CInvalidFeedback>{(errors.keyword && errors.keyword.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                </CFormGroup>
              </CCol>
            </CRow>
            <CRow className="mt-2">
              <CCol md="12" sm="12">
                <CFormGroup className={errors.name && 'is-invalid'}>
                  <CLabel>{t('seo-description')}</CLabel>
                  <div className="textarea-group">
                    <CTextarea
                      className="noresize"
                      name="description"
                      invalid={errors.description ? true : false}
                      rows="5"
                      maxLength="512"
                      onChange={remainderChange}
                      innerRef={register({ required: true })}
                      defaultValue={seo ? seo.description : ''}
                    />
                    <p className="text-right text-muted mb-1">{remaining.description ? remaining.description : 0}/{maxLength.description}</p>
                    <CInvalidFeedback className="textarea-invild-feefback">{(errors.description && errors.description.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>

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

const mapStateToProps = (state) => {
  return {
    dispatch: state.dispatch,
    formRows: state.formRows,
  };
};

export default connect(mapStateToProps)(SeoForm);
