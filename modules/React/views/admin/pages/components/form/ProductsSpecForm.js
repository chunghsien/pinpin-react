
import React, { useState, useEffect, useRef } from 'react';
import { connect } from "react-redux";
//import { useParams } from "react-router-dom";
import { useTranslation } from 'react-i18next';
import {
  CRow, CCol, CFormGroup, CLabel,
  CCard,
  CSelect, CInput,
  CInvalidFeedback,
  CTabContent, CTabPane
} from '@coreui/react'
//import AsyncSelect from 'react-select/async';
import { useForm } from "react-hook-form";
import Form from '../Form';
//import { dialog } from '../alertify';

const ProductsSpecForm = (props) => {
  const { t } = useTranslation(['translation']);
  const methods = useForm({ mode: 'all' });
  const { register, errors } = methods;
  //const basePath = window.pageConfig.basePath;

  const NC = 0;
  const { formRows } = props;
  const { products_spec } = formRows ? formRows : {};
  useEffect(() => {
  }, [NC, formRows]);

  const formRef = useRef();

  return (
    <>
      <CTabContent>
        <CTabPane data-tab="default-form">
          <CCard className="tab-card">
            <Form
              innerRef={formRef}
              href="/admin/products_spec"
              table="products_spec"
              griduse
              {...methods}
            >
              {
                products_spec &&
                <>
                  <input type="hidden" name="id" ref={register()} defaultValue={products_spec.id} />
                  <CRow>
                    <CCol md="4" sm="12" className="mt-2">
                      <CFormGroup>
                        <CLabel>{t('columns-language_has_locale')}</CLabel>
                        <h6 className="font-weight-bold">{products_spec.language_has_locale}</h6>
                      </CFormGroup>

                    </CCol>
                    <CCol md="4" sm="12" className="mt-2">
                      <CLabel>{t('columns-products_id')}</CLabel>
                      <h6 className="font-weight-bold">{products_spec.model}</h6>
                    </CCol>
                    <CCol md="4" sm="12" className="mt-2">
                      <CLabel>{t('columns-products-spec-group-name')}</CLabel>
                      <h6 className="font-weight-bold">{products_spec.group_name}</h6>
                    </CCol>

                    <CCol md="12" sm="12" className="mt-2">
                      <CFormGroup>
                        <CLabel>{t('columns-name')}</CLabel>
                        <h6 className="font-weight-bold">{products_spec.name}</h6>
                      </CFormGroup>
                    </CCol>
                    {/*
                    <CCol md="6" sm="12" className="mt-2">
                      <CFormGroup>
                        <CLabel>{t('columns-main_photo')}</CLabel>
                        <CInputFile
                          name="main_photo"
                          innerRef={register()}
                          invalid={errors.main_photo ? true : false}
                          onChange={singleFileOnChange}
                          accept="image/*"
                        />
                        <CInvalidFeedback>{(errors.main_photo && errors.main_photo.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                        <img
                          id="assets-image-preview"
                          className={'mt-2 img-fluid form-thumbnail ' + (products_spec.main_photo ? '' : 'd-none')}
                          src={products_spec.main_photo}
                        />
                      </CFormGroup>
                    </CCol>
                    <CCol md="6" sm="12" className="mt-2">
                      <CFormGroup>
                        <CLabel>{t('columns-sub_photo')}</CLabel>
                        <CInputFile
                          name="sub_photo"
                          innerRef={register()}
                          invalid={errors.sub_photo ? true : false}
                          onChange={singleFileOnChange}
                          accept="image/*"
                        />
                        <CInvalidFeedback>{(errors.sub_photo && errors.sub_photo.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                        <img
                          id="assets-image-preview"
                          className={'mt-2 img-fluid form-thumbnail ' + (products_spec.sub_photo ? '' : 'd-none')}
                          src={products_spec.sub_photo}
                        />
                      </CFormGroup>
                    </CCol>
                    */}
                    <CCol md="4" sm="12" className="mt-2">
                      <CFormGroup>
                        <CLabel>{t('columns-stock')}</CLabel>
                        <CInput
                          invalid={errors.stock ? true : false}
                          name="stock"
                          type="number"
                          min="0"
                          innerRef={register()}
                          defaultValue={products_spec.stock}
                        />
                        <CInvalidFeedback>{errors.sort && t('The input is an empty string')}</CInvalidFeedback>
                      </CFormGroup>
                    </CCol>
                    <CCol md="6" sm="12" className="mt-2">
                      <CFormGroup>
                        <CLabel>{t('columns-stock_status')}</CLabel>
                        <CSelect
                          name="stock_status"
                          custom
                          innerRef={register({ required: true })}
                          defaultValue={products_spec.stock_status}
                        >
                          {
                            pageConfig.stock_status.map((item, key) => {
                              return <option key={'stoc_status_' + key} value={item.value}>{t(item.label)}</option>
                            })
                          }
                        </CSelect>
                      </CFormGroup>
                    </CCol>
                  </CRow>
                </>
              }

            </Form>
          </CCard>
        </CTabPane>
      </CTabContent>
    </>
  );
}
const mapStateToProps = (state) => {
  return {
    //dispatch: state.dispatch,
    formRows: state.formRows
  };
};

export default connect(mapStateToProps)(ProductsSpecForm);