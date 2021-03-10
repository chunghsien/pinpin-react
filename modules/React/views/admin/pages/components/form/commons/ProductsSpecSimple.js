import React, { useState, useEffect, useRef } from 'react';
import { connect } from "react-redux";
import {
  CRow,
  CCardFooter,
  CCol,
  CSelect,
  //CInputGroup,
  //CInputGroupAppend,
  //CInputGroupText,
  CForm,
  CFormGroup,
  CLabel,
  CInput,
  CButton,
  CInvalidFeedback
} from '@coreui/react';
import Accordion from 'react-bootstrap/Accordion'
import Card from 'react-bootstrap/Card'
import Button from 'react-bootstrap/Button';
import { useForm } from "react-hook-form";
import axios from 'axios';
import { toConfirm } from '../../alertify';
import { FORM_ACTIVE_TAB } from "../../../../actions/formRowsAction";
import { useHistory, useParams } from "react-router-dom";


const ProductsSpecSimple = ({ item, t, options, buildSpecGroup, sucessNotify, dispatch }) => {
  const basePath = window.pageConfig.basePath;
  const history = useHistory();
  const methods = useForm({ mode: 'all' });
  const { register, handleSubmit, errors } = methods;
  const formRef = useRef();

  const url = `${basePath}/zh-TW/api/admin/products-spec`.replace(/\/{2,}/, '/');
  const loadingBackgroundDom = document.getElementById('loading-background');
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
  const {method_or_id} = useParams();
  const onClickToDetail = (e) => {
    const id = e.currentTarget.dataset.id;
    const uri = `${basePath}/zh-TW/admin/products_spec/${id}`.replace(/\/{2,}/, '/');
    let tab = {
      goal: uri,
      uri: `${basePath}/zh-TW/admin/products/${method_or_id}`.replace(/\/{2,}/, '/'),
      tab: "products-spec-group-form",
    };
    dispatch({type: FORM_ACTIVE_TAB, data:tab});
    history.push(uri);
  }

  const onSpecReset = (e) => {
    e.preventDefault();
    const idValue = e.currentTarget.dataset.id;
    const idName = `row_${idValue}`;
    const ele = document.getElementById(idName);
    const row = JSON.parse(ele.dataset.origin);

    Array.from(ele.getElementsByTagName('input')).forEach((inputEle) => {
      if (inputEle.type != 'hidden') {
        const name = inputEle.name;
        const originData = row[name];
        inputEle.value = originData;
      }
    });
    Array.from(ele.getElementsByTagName('select')).forEach((selectEle) => {
      const name = selectEle.name;
      const originData = row[name];
      selectEle.value = originData;
    });

  }

  const deleteItem = (e) => {
    e.preventDefault();
    const currentTarget = e.currentTarget;
    const id = currentTarget.dataset.id;
    const uri = `${basePath}/zh-TW/api/admin/products_spec`.replace(/\/{2,}/, '/');
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
  const onSpecSave = (e) => {
    e.preventDefault();
    const id = e.currentTarget.dataset.id;
    const idName = `row_${id}`;
    const ele = document.getElementById(idName);
    var params = {};
    Array.from(ele.getElementsByTagName('input')).forEach((inputEle) => {
      if (inputEle.type != 'hidden') {
        const name = inputEle.name;
        params[name] = inputEle.value;
      }
    });
    Array.from(ele.getElementsByTagName('select')).forEach((selectEle) => {
      const name = selectEle.name;
      params[name] = selectEle.value;
    });
    const uri = `${basePath}/zh-TW/api/admin/products_spec`.replace(/\/{2,}/, '/');
    toConfirm(() => {
      loadingBackgroundDom.classList.remove('d-none');
      axios.put(`${uri}/${id}`, params).then((response) => {
        loadingBackgroundDom.classList.add('d-none');
        const data = response.data;
        sucessNotify(data);
        buildSpecGroup();
      });
    }, t);
  }
  return (
    <>
      {
        !!(item) &&
        <tr>
          <td colSpan="5">
            <Accordion defaultActiveKey="1">
              <Card className="mb-0">
                <Card.Header className="bg-success">
                  <Accordion.Toggle className="text-white" as={Button} variant="link" eventKey="0">{t('add-products_spec')}</Accordion.Toggle>
                </Card.Header>
                <Accordion.Collapse eventKey="0">
                  <CForm
                    method="post"
                    innerRef={formRef}
                    onSubmit={handleSubmit(onSubmit)}
                  >
                    <Card.Body>
                      <CRow>
                        <CCol md="6" sm="12" >
                          <CFormGroup>
                            <CLabel>{t('columns-name')}</CLabel>
                            <CInput type="hidden" name="products_spec_group_id" defaultValue={item.id} innerRef={register()} />
                            <CInput type="hidden" name="products_id" defaultValue={item.products_id} innerRef={register()} />
                            <CSelect custom name="products_spec_attrs_id" innerRef={register({ required: true })}>
                              <option value="">{t("isOptionsDefault")}</option>
                              {
                                !!(options) &&
                                options.map((op, key) => {
                                  return (<option value={op.value} key={`${item.id}-${key}`}>{op.label}</option>)
                                })
                              }
                            </CSelect>
                            <CInvalidFeedback
                              className={errors.products_spec_attrs_id && 'd-block'}
                            >
                              {(errors.products_spec_attrs_id && errors.products_spec_attrs_id.type == 'required') && t('The input is an empty string')}
                            </CInvalidFeedback>
                          </CFormGroup>
                        </CCol>


                      </CRow>
                    </Card.Body>
                    <CCardFooter>
                      <CButton size="sm" color="primary" type="submit" className="mr-1">
                        <i className="fas fa-check mr-1"></i>
                        <span>{t('form-submit')}</span>
                      </CButton>
                      <CButton className="text-white" size="sm" color="warning" type="reset">
                        <i className="fas fa-undo-alt mr-1"></i>
                        <span>{t('form-reset')}</span>
                      </CButton>
                    </CCardFooter>
                  </CForm>
                </Accordion.Collapse>
              </Card>
              <Card className="mb-0">
                <Card.Header className="bg-info">
                  <Accordion.Toggle className="text-white" as={Button} variant="link" eventKey="1">{t('products_spec_list')}</Accordion.Toggle>
                </Card.Header>
                <Accordion.Collapse eventKey="1">
                  <Card.Body>
                    {
                      !!(item.specs && item.specs.length) &&
                      <table className="table table-striped table-bordered">
                        <thead className="thead-dark">
                          {/*/zh-TW/admin/products_spec/1*/}
                          <tr>
                            <th scope="col" >{t('columns-name')}</th>
                            <th scope="col" style={{ width: "14em" }}>{t('columns-stock_status')}</th>
                            <th scope="col" style={{ width: "8em" }}>{t('columns-stock')}</th>
                            <th scope="col" style={{ width: "6.5em" }}>{t('go-to-product-detail')}</th>
                            <th scope="col" style={{ width: "14.5em" }}>{t('Actions')}</th>
                          </tr>
                        </thead>
                        <tbody>
                          {
                            item.specs.map((row) => {
                              return (
                                <tr id={`row_${row.id}`} key={`children-${item.id}-${row.id}`} data-origin={JSON.stringify(row)}>
                                  <td>{row.name}</td>
                                  <td>
                                    <CSelect name="stock_status" defaultValue={row.stock_status}>
                                      {
                                        pageConfig.stock_status.map((status, key) => {
                                          return (
                                            <option value={status.value} key={`status_${key}`}>{t(status.label)}</option>
                                          )
                                        })
                                      }
                                    </CSelect>
                                  </td>
                                  <td>
                                    <CInput type="number" name="stock" min="0" defaultValue={row.stock} />
                                  </td>
                                  <td>
                                    <CButton
                                      color="info"
                                      shape="square"
                                      variant="outline"
                                      onClick={onClickToDetail}
                                      data-id={row.id}
                                    ><i className="fas fa-memory" /></CButton>
                                  </td>
                                  <td>
                                    <div className="btn-group btn-group-toggle">
                                      <CButton
                                        size="sm"
                                        color="primary"
                                        type="button"
                                        data-id={row.id}
                                        onClick={onSpecSave}
                                        data-id={row.id}
                                      >
                                        <i className="fas fa-save mr-1"></i>
                                        <span>{t('form-save')}</span>
                                      </CButton>
                                      <CButton
                                        className="text-white"
                                        size="sm"
                                        color="warning"
                                        type="button"
                                        data-id={row.id}
                                        onClick={onSpecReset}
                                      >
                                        <i className="fas fa-undo-alt mr-1"></i>
                                        <span>{t('form-reset')}</span>
                                      </CButton>

                                      <CButton size="sm" color="danger" type="button" onClick={deleteItem} data-id={row.id}>
                                        <i className="fas fa-trash-alt mr-1" />
                                        {t('form-delete')}
                                      </CButton>
                                    </div>
                                  </td>
                                </tr>
                              )
                            })
                          }
                        </tbody>
                      </table>
                    }
                  </Card.Body>
                </Accordion.Collapse>
              </Card>

            </Accordion>
          </td>
        </tr>
      }
    </>
  )
}
const mapStateToProps = (state) => {
  return {
    dispatch: state.dispatch,
    formActiveTab: state.formActiveTab,
  };
};

//export default Documents;
export default connect(mapStateToProps)(ProductsSpecSimple);