import React, { useState, useEffect, useRef } from 'react';
import { connect } from "react-redux";
import { useTranslation } from 'react-i18next';
import {
  CRow, CCol, CFormGroup, CLabel,
  CCard,
  CInput,
  CInputGroup, CInputGroupAppend, CInputGroupText,
  CInvalidFeedback,
  CSelect,
  CTabContent, CTabPane
} from '@coreui/react'
import { useForm } from "react-hook-form";
import Form from '../Form';

const ProductsSpecGroupAttrsForm = (props) => {
  const { t } = useTranslation(['translation']);
  const methods = useForm({ mode: 'all' });
  const { register, errors } = methods;
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
    setRemaining((remaining) => ({ ...remaining, ...tObj }));
    return remaining[name];
  }

  const { formRows } = props;
  const products_spec_group_attrs = formRows ? formRows.products_spec_group_attrs : null;
  const [formSelected, setFormSelected] = useState({
    language_has_locale: '',
  });
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
    if (products_spec_group_attrs) {
      setFormSelected({
        language_has_locale: products_spec_group_attrs.language_has_locale,
      });
    }

  }, [products_spec_group_attrs, count]);
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

  return (
    <>
      <CTabContent>
        <CTabPane data-tab="default-form">
          <CCard className="tab-card">
            <Form
              innerRef={formRef}
              href="/admin/products_spec_group_attrs"
              table="products_spec_group_attrs"
              griduse
              {...methods}
              remainderChange={remainderChange}
              setFormSelected={setFormSelected}
            >
              <input type="hidden" name="id" ref={register()} defaultValue={products_spec_group_attrs ? products_spec_group_attrs.id : undefined} />
              <CRow className="mt-2">
                <CCol md="6" sm="12">
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
                <CCol md="6" sm="12" />
                <CCol md="6" sm="12">
                  <CFormGroup>
                    <CLabel>{t('columns-products-spec-group-name')}</CLabel>
                    <CInputGroup className={errors.name && 'is-invalid'}>
                      <CInput
                        invalid={errors.name ? true : false}
                        name="name"
                        maxLength="128"
                        onChange={remainderChange}
                        innerRef={register({ required: true })}
                        defaultValue={products_spec_group_attrs ? products_spec_group_attrs.name : undefined}
                      />
                      <CInputGroupAppend>
                        <CInputGroupText className="bg-light text-muted">{remaining.name ? remaining.name : 0}/{maxLength.name}</CInputGroupText>
                      </CInputGroupAppend>
                    </CInputGroup>
                    <CInvalidFeedback>{(errors.name && errors.name.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                  </CFormGroup>
                </CCol>
                <CCol md="6" sm="12">
                  <CFormGroup>
                    <CLabel>{t('columns-products-spec-group-extraname')}</CLabel>
                    <CInputGroup className={errors.extra_name && 'is-invalid'}>
                      <CInput
                        invalid={errors.extra_name ? true : false}
                        name="extra_name"
                        maxLength="128"
                        onChange={remainderChange}
                        innerRef={register()}
                        defaultValue={products_spec_group_attrs ? products_spec_group_attrs.extra_name : undefined}
                      />
                      <CInputGroupAppend>
                        <CInputGroupText className="bg-light text-muted">{remaining.name ? remaining.name : 0}/{maxLength.name}</CInputGroupText>
                      </CInputGroupAppend>
                    </CInputGroup>
                    <CInvalidFeedback>{(errors.name && errors.name.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
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
const mapStateToProps = (state) => {
  return {
    dispatch: state.dispatch,
    formRows: state.formRows,

  };
};

export default connect(mapStateToProps)(ProductsSpecGroupAttrsForm);