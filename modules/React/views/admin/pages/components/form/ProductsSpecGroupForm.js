import React, { useState, useEffect, useRef, Fragment } from 'react';
import chroma from 'chroma-js';
import { useTranslation } from 'react-i18next';
import {
  CRow,
  CCard,
  CCardHeader,
  CCardBody,
  CCardFooter,
  CCol,
  CForm,
  CFormGroup,
  CLabel,
  CTabContent,
  CTabPane,
  CButton,
  CInvalidFeedback
} from '@coreui/react';
import { notify, toConfirm } from '../alertify';
import { useForm } from "react-hook-form";
//import Form from '../Form';
import Select from 'react-select';
import { useParams } from "react-router-dom";
import axios from 'axios';
import ProductsSpecSimple from './commons/ProductsSpecSimple';

const ProductsSpecGroupForm = (props) => {
  const { t } = useTranslation(['translation', 'stock_status']);
  const methods = useForm({ mode: 'all' });
  const { register, handleSubmit, errors } = methods;
  const { method_or_id } = useParams();
  const basePath = window.pageConfig.basePath;
  const url = `${basePath}/zh-TW/api/admin/products-spec-group`.replace(/\/{2,}/, '/');
  const loadingBackgroundDom = document.getElementById('loading-background');

  var selectMenuStyles = {
    menu: (provided/*, state*/) => {
      return {
        ...provided,
        fontSize: '0.95rem'
      }
    },
    container: (provided/*, state*/) => {
      return {
        ...provided,
        fontSize: '0.95rem'
      }
    },
    option: (styles, { data, isDisabled, isFocused, isSelected }) => {
      var response = {
        ...styles,
      }
      var merge = {}
      if (data.extra_name.match(/^#(?:[0-9a-fA-F]{3}){1,2}$/)) {
        const color = chroma(data.extra_name);
        merge = {
          backgroundColor: isDisabled
            ? null
            : isSelected
              ? data.extra_name
              : isFocused
                ? color.alpha(0.2).css()
                : null,
          color: isDisabled
            ? '#ccc'
            : isSelected
              ? chroma.contrast(color, 'white') > 2
                ? 'white'
                : 'black'
              : data.extra_name,
          cursor: isDisabled ? 'not-allowed' : 'default',
          ':active': {
            ...styles[':active'],
            backgroundColor:
              !isDisabled && (isSelected ? data.extra_name : color.alpha(0.3).css()),
          },
        }
      }
      return { ...response, ...merge };
    }
  }
  
  const [productsSpecGroup, setProductsSpecGroup] = useState();
  const [productsSpecGroupValue, setProductsSpecGroupValue] = useState();
  const count = 0;
  const { parent } = props.classRelation;
  const parent_id_name = parent + '_id';
  const formRef = useRef();
  
  const buildSpecGroup = () => {
    var params = { products_id: method_or_id };
    //loadingBackgroundDom.classList.remove('d-none');
    axios.get(url, { params: params }).then((response) => {
      const data = response.data.data;
      setProductsSpecGroup(data);
    });
    
  }
  
  useEffect(() => {
    buildSpecGroup();
  }, [count]);
  const reactSelectChange = (option) => {
    formRef.current.elements[parent_id_name].value = option.value;
    setProductsSpecGroupValue(option);
  }
  const productsSpecGroupFormReset = (e) => {
    e.preventDefault();
    setProductsSpecGroupValue(null);
  }

  const deleteItem = (e) => {
    e.preventDefault();
    const currentTarget = e.currentTarget;
    const id = currentTarget.dataset.id;
    const uri = `${basePath}/zh-TW/api/admin/products_spec_group`.replace(/\/{2,}/, '/');
    toConfirm(() => {
      loadingBackgroundDom.classList.remove('d-none');
      axios.delete(`${uri}/${id}`).then((response) => {
        loadingBackgroundDom.classList.add('d-none');
        const data = response.data;
        sucessNotify(data);
        buildSpecGroup();
      });
    }, t);

  }
  const sucessNotify = (data) => {
    if (data.code == 0) {
      notify('success', t(data.notify.join("\n")), 3);
    } else {
      if (data.code == -2) {
        notify('error', t('admin-session-fail'), 3);
      }
      notify('warning', t(data.notify.join("\n")));
    }
  }

  const onSubmit = (data) => {
    loadingBackgroundDom.classList.remove('d-none');
    axios.post(url, data).then((response) => {
      const DATA = response.data;
      loadingBackgroundDom.classList.add('d-none');
      sucessNotify(DATA);
      formRef.current.reset();
      buildSpecGroup();
    });
  }
  return (
    <>
      <CTabContent>
        <CTabPane data-tab="products-spec-group-form">
          <CCard className="tab-card">

            <CCardBody className="p-4">
              <CForm
                method="post"
                innerRef={formRef}
                onSubmit={handleSubmit(onSubmit)}
              >
                <CCard accentColor="primary">
                  <CCardHeader>{t('add-products-spec-group-attrs')}</CCardHeader>
                  <CCardBody>
                    <input type="hidden" name="products_id" defaultValue={method_or_id} ref={register()} />
                    <CRow className="mt-2">
                      <CCol md="12" sm="12">
                        <CFormGroup>
                          <CLabel>{t('columns-products_spec_group_attrs_id')}</CLabel>
                          <Select
                            styles={selectMenuStyles}
                            name={parent + '_container'}
                            placeholder={t("isUseOptionsDefault")}
                            options={productsSpecGroup && productsSpecGroup.options.products_spec_group}
                            value={productsSpecGroupValue || ''}
                            onChange={reactSelectChange}
                          />
                          <input name={parent_id_name} type="hidden" ref={register({required:true})} />
                          <CInvalidFeedback
                            className={errors[parent_id_name] && 'd-block'}>
                              {(errors[parent_id_name] && errors[parent_id_name].type == 'required') && t('The input is an empty string')}
                          </CInvalidFeedback>
                        </CFormGroup>
                      </CCol>
                      <CCol md="6" sm="12" />
                    </CRow>
                  </CCardBody>
                  <CCardFooter>
                    <CButton size="sm" color="primary" type="submit" className="mr-1">
                      <i className="fas fa-check mr-1"></i>
                      <span>{t('form-submit')}</span>
                    </CButton>
                    <CButton
                      className="text-white"
                      size="sm"
                      color="warning"
                      type="reset"
                      onClick={productsSpecGroupFormReset}
                    >
                      <i className="fas fa-undo-alt mr-1"></i>
                      <span>{t('form-reset')}</span>
                    </CButton>
                  </CCardFooter>
                </CCard>
              </CForm>
              <CRow>
                <CCol>
                  <hr />
                  <table className="table table-striped table-bordered">
                    <thead className="thead-dark">
                      <tr>
                        <th scope="col" style={{ width: "4em" }} className="align-middle text-center">#</th>
                        <th scope="col">{t('columns-products-spec-group-name')}</th>
                        <th scope="col">{t('columns-products-spec-group-extraname')}</th>
                        <th scope="col" style={{ width: "8.5em" }}>{t('Actions')}</th>
                      </tr>
                    </thead>
                    <tbody>
                      {
                        !!(productsSpecGroup) &&
                        productsSpecGroup.lists.map((item, key) => {
                          return (
                            <Fragment key={`group-${key}`}>
                              <tr>
                                <td className="align-middle text-center"><i className="fas fa-caret-down" /></td>
                                <td>{item.name}</td>
                                <td>{item.extra_name}</td>
                                <td>
                                  <CButton
                                    size="md"
                                    color="danger"
                                    type="button"
                                    data-id={item.id}
                                    onClick={deleteItem}
                                  >
                                    <i className="fas fa-trash-alt mr-1" />
                                    {t('form-delete')}
                                  </CButton>
                                </td>
                              </tr>
                              <ProductsSpecSimple
                                t={t}
                                item={item}
                                options={productsSpecGroup.options.products_spec}
                                buildSpecGroup={buildSpecGroup}
                                sucessNotify={sucessNotify}
                              />
                            </Fragment>
                          )
                        })
                      }
                    </tbody>
                  </table>
                </CCol>
              </CRow>
            </CCardBody>
          </CCard>
        </CTabPane>
      </CTabContent>
    </>
  );
}

export default ProductsSpecGroupForm;