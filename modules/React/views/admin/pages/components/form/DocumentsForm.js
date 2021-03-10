import React, { useState, useEffect, useRef } from 'react';
import { connect } from "react-redux";
import { useTranslation } from 'react-i18next';
import {
  CRow, CCol, CFormGroup, CLabel,
  CCard,
  CSelect, CInput,
  CInputGroup, CInputGroupAppend, CInputGroupText,
  CInvalidFeedback,
  CTabContent, CTabPane
} from '@coreui/react'
import { useForm } from "react-hook-form";
import Form from '../Form';

const DocumentsForm = (props) => {
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
  const count = 0;
  const [formSelected, setFormSelected] = useState({});
  const { formRows } = props;
  const documents = formRows && formRows.documents ? formRows.documents : null; 
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
      if (documents) {
        setFormSelected({
          language_has_locale: documents.language_has_locale,
        });
      }
      
    });
  }, [documents, count]);
  const selectOnChange = (e) => {
    let document = {
      language_has_locale: e.target.value,
    };
    setFormSelected(document);
  }
  
  const formRef = useRef();
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
              setMaxLength={setMaxLength}
              setFormSelected={setFormSelected}
            >
              <input type="hidden" name="id" ref={register()} defaultValue={documents ? documents.id : ''} />
              <CRow>
                <CCol md="4" sm="12" className="mt-2">
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
                <CCol md="4" sm="12" className="mt-2">
                  <CFormGroup>
                    <CLabel>{t('columns-name')}</CLabel>
                    <CInputGroup className={errors.name && 'is-invalid'}>
                      <CInput
                        invalid={errors.name ? true : false}
                        name="name"
                        maxLength="128"
                        onChange={remainderChange}
                        innerRef={register({ required: true })}
                        defaultValue={documents ? documents.name : ''}
                      />
                      <CInputGroupAppend>
                        <CInputGroupText className="bg-light text-muted">{remaining.name ? remaining.name : 0}/{maxLength.name}</CInputGroupText>
                      </CInputGroupAppend>
                    </CInputGroup>
                    <CInvalidFeedback>{(errors.name && errors.name.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                  </CFormGroup>
                </CCol>
                <CCol md="4" sm="12" className="mt-2">
                  <CFormGroup>
                    <CLabel>{t('suffix uri')}</CLabel>
                    <CInputGroup className={errors.route && 'is-invalid'}>
                      <CInput
                        invalid={errors.route ? true : false}
                        name="route"
                        readOnly
                        maxLength="255"
                        onChange={remainderChange}
                        innerRef={register()}
                        defaultValue={documents ? documents.route : ''}
                      />
                      <CInputGroupAppend>
                        <CInputGroupText className="bg-light text-muted">{remaining.route ? remaining.route : 0}/{maxLength.route}</CInputGroupText>
                      </CInputGroupAppend>
                    </CInputGroup>
                    <CInvalidFeedback>{(errors.route && errors.route.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
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

//export default DocumentsForm;
const mapStateToProps = (state) => {
  return {
    dispatch: state.dispatch,
    formRows: state.formRows,
  };
};

export default connect(mapStateToProps)(DocumentsForm);
