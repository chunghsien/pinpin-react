import React, { useState, useEffect, useRef } from 'react';
import { connect } from "react-redux";
import { useTranslation } from 'react-i18next';
import {
  CRow, CCol, CFormGroup, CLabel,
  CCard,
  CTextarea, CInvalidFeedback,
  CSelect,
  CTabContent, CTabPane,
  CSwitch
} from '@coreui/react'
import { useForm } from "react-hook-form";
import Form from '../Form';

const ContactForm = (props) => {
  const { t } = useTranslation(['translation']);
  const formRef = useRef();
  const methods = useForm({ mode: 'all' });
  const { register, errors } = methods;
  const [remaining, setRemaining] = useState({});
  const [maxLength, setMaxLength] = useState({});
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

  let href = props.href;

  const { formRows } = props;
  console.log(formRows);
  return (

    <CTabContent>
      <CTabPane data-tab="default-form">
        <CCard className="tab-card">
          <Form
            innerRef={formRef}
            href={href}
            griduse {...methods}
            remainderChange={remainderChange}
            setFormRow={props.setFormRow}
          >
            {
              (formRows && formRows.contact) &&
              <>
                <input type="hidden" name="id" ref={register()} />
                <CRow>
                  <CCol>
                    <CLabel>{t('columns-language_has_locale')}</CLabel>
                    <CSelect
                      readOnly
                      disabled
                      name="language_has_locale"
                      defaultValue={formRows.contact.language_has_locale}
                      custom
                    >
                      {
                        window.pageConfig.languageOptions.map((item, index) => {
                          return (<option key={index} value={item.value}>{item.label}</option>);
                        })
                      }
                    </CSelect>

                  </CCol>
                  <CCol>
                    <CLabel>{t('columns-full_name')}</CLabel>
                    <h5 className="font-weight-bold">{formRows.contact.full_name}</h5>
                  </CCol>
                </CRow>
                <CRow className="mt-2">
                  <CCol>
                    <CLabel>{t('columns-email')}</CLabel>
                    <h5 className="font-weight-bold">{formRows.contact.email}</h5>
                  </CCol>
                </CRow>
                <CRow className="mt-2">
                  <CCol>
                    <CLabel>{t('columns-subject')}</CLabel>
                    <h5 className="font-weight-bold">{formRows.contact.subject}</h5>
                  </CCol>
                </CRow>
                <CRow className="mt-2">
                  <CCol>
                    <CLabel>{t('columns-commet')}</CLabel>
                    <pre className="font-weight-bold h5">{formRows.contact.commet}</pre>
                  </CCol>
                </CRow>

                <CRow className="mt-3">
                  <CCol md="12" sm="12">
                    <div className="textarea-group">
                      <CLabel>
                        {t('columns-reply')}
                        {/*
                                        <CPopover content={t('facebook:og_colon_description_popver')}>
                                            <i className="fas fa-question-circle ml-1" />
                                        </CPopover>
                                        */}
                      </CLabel>
                      <div className="textarea-group">
                        <CTextarea readOnly={formRows.contact.is_reply == 1} disabled={formRows.contact.is_reply == 1} defaultValue={formRows.reply} className="noresize" invalid={errors.reply ? true : false} name="reply" maxLength="1024" onChange={remainderChange} rows="5" innerRef={register({ required: true })} />
                        <p className="text-right text-muted">{remaining.reply ? remaining.reply : 0}/{maxLength.reply}</p>
                        <CInvalidFeedback className="textarea-invild-feefback">{errors.reply && t('The input is an empty string')}</CInvalidFeedback>
                      </div>
                    </div>
                  </CCol>
                </CRow>
                <CRow className="mt-3">
                  <CCol>
                    <CFormGroup>
                      <CLabel>{t('columns-is_reply')}</CLabel>
                      <div className="d-blobk">
                        <CSwitch
                          value={1}
                          name="is_reply"
                          color="primary"
                          innerRef={register()}
                          defaultChecked={formRows.contact.is_reply == 1}
                          readOnly={formRows.contact.is_reply == 1}
                        />
                      </div>
                    </CFormGroup>
                  </CCol>
                </CRow>
              </>
            }
          </Form>
        </CCard>
      </CTabPane>
    </CTabContent>

  );
}

const mapStateToProps = (state) => {
  return {
    formRows: state.formRows
  };
};

export default connect(mapStateToProps)(ContactForm);
