import React, { useState, useEffect, useRef, Fragment } from 'react';
import { useTranslation } from 'react-i18next';
import {
  CRow,
  CCard,
  CCardHeader,
  CCardBody,
  CCardFooter,
  CCol,
  CInputGroup,
  CInputGroupAppend,
  CInputGroupText,
  CForm,
  CFormGroup,
  CLabel,
  CInput,
  CTabContent,
  CTabPane,
  CSelect,
  CButton,
  CSwitch,
  CInvalidFeedback
} from '@coreui/react';
import { useForm } from "react-hook-form";
import formBreadItemRename from '../formBreadItemRename';
import axios from 'axios';
import { notify, toConfirm } from '../alertify';
//import Loading from 'react-fullscreen-loading';
import LayoutZonesChild from './commons/LayoutZonesChild';

const DocumentsLayoutForm = (/*props*/) => {
  const { t } = useTranslation(['translation']);
  const methods = useForm({ mode: 'all' });
  const { register, errors, handleSubmit } = methods;
  const [widgetName, setWidgetName] = useState('');
  const [list, setList] = useState();
  const requiredMapper = {
    name: true,
    uri: false,
    target: false,
    image: false
  };
  const [maxLength, setMaxLength] = useState({
    uri: 255
  });
  const [remaining, setRemaining] = useState({});
  const [documentsLink, setDocumentsLink] = useState({});

  const url = location.pathname.replace(/admin/, 'api/admin');
  const loadingBackgroundDom = document.getElementById('loading-background');

  const finalNotify = (data) => {
    if (data.code == 0) {
      notify('success', t(data.notify.join("\n")), 3);
    } else {
      if (data.code == -2) {
        notify('error', t('admin-session-fail'), 3);
      }
      notify('warning', t(data.notify.join("\n")), 3);
    }
  }


  const onSubmit = (data) => {
    var formData = new FormData(formRef.current);
    if (!data.visible) {
      formData.set('visible', 0);
    }

    let parent_id = url.match(/\/\d+/)[0].replace(/^\//, '');
    formData.append('parent_id', parent_id);
    loadingBackgroundDom.classList.remove('d-none');
    axios.post(url, formData).then((response) => {
      const DATA = response.data.data;
      setList(DATA.list);
      loadingBackgroundDom.classList.add('d-none');
      finalNotify(response.data);
      formRef.current.reset();
      setLinkType('');
      setRemaining((remaining) => {
        let res = {
          ...remaining,
          ...{ name: 0 }
        }
        return res;
      });

    });
  }
  const onKeyUp = (e) => {
    e.preventDefault();
    const target = e.currentTarget;
    const length = target.value.length;
    const name = target.name;
    let obj = {};
    obj[name] = length;
    setRemaining((remaining) => {
      let res = {
        ...remaining,
        ...obj
      }
      return res;
    });
  }

  var NC = 0;
  useEffect(() => {
    formRef.current.elements.forEach((dom) => {
      const name = dom.name;
      const _maxLength = dom.maxLength;
      if (_maxLength && _maxLength > 0) {
        let obj = {};
        obj[name] = _maxLength;
        setMaxLength((maxLength) => ({ ...maxLength, ...obj }));
      }
      const navTo = location.pathname.replace(/\/(add|\d+)$/, '');
      Array.from(document.querySelectorAll('a.c-sidebar-nav-link')).forEach((elm) => {
        if (elm.getAttribute('href') == navTo) {
          elm.classList.add('c-active');
        }
      });
    });
    if (!list) {
      axios.get(url).then((response) => {
        var data = response.data.data;
        setWidgetName(data.name);
        setDocumentsLink(data.docuemnts_link);
        setList(data.list);
      });
    }
  }, [NC]);

  const [linkType, setLinkType] = useState('');
  const typeSiwtch = (e) => {
    e.preventDefault();
    setLinkType(e.currentTarget.value);
  }

  const resetItem = (e) => {
    e.preventDefault();
    const id = e.currentTarget.dataset.id;
    const key = e.currentTarget.dataset.key;
    const trEle = document.getElementById(`list-${id}`);
    trEle.querySelectorAll('input').forEach((ele) => {
      if (ele.type == 'checkbox') {
        ele.checked = (list[key].visible == 1);
      } else {
        const name = ele.name;
        if (name == 'sort') {
          ele.value = list[key][name] == 16777215 ? '' : list[key][name];
        } else {
          ele.value = list[key][name];
        }

      }
    });
  }
  const saveItem = (e) => {
    e.preventDefault();
    const id = e.currentTarget.dataset.id;
    const trEle = document.getElementById(`list-${id}`);
    var data = {};
    trEle.querySelectorAll('input').forEach((ele) => {
      //console.log(ele.name);
      const name = ele.name;
      var value = ele.value;
      if (ele.type == 'checkbox') {
        value = ele.checked ? 1 : 0;
      }
      data[name] = value
    });
    data.id = id;
    const uri = url.replace(/\/\d+$/, '');
    loadingBackgroundDom.classList.remove('d-none');
    axios.put(uri, data).then((response) => {
      loadingBackgroundDom.classList.add('d-none');
      const data = response.data;
      finalNotify(data);
      if(data.data.list) {
        setList(data.data.list);
      }
    });

  }

  const deleteItem = (e) => {
    e.preventDefault();
    const currentTarget = e.currentTarget;
    const id = currentTarget.dataset.id;
    const uri = url.replace(/\/\d+$/, '');
    toConfirm(() => {
      loadingBackgroundDom.classList.remove('d-none');
      axios.delete(`${uri}/${id}`).then((response) => {
        loadingBackgroundDom.classList.add('d-none');
        const data = response.data;
        finalNotify(data);
        setList(data.data.list);
      });
    }, t);
  }

  const resetBaseForm = () => {
    setLinkType('');
  }

  const formRef = useRef();
  return (
    <>
      <CTabContent>
        <CTabPane data-tab="default-form">
          <CCard className="tab-card">
            <CCardHeader>
              <b className="mr-2 text-black">{widgetName}</b>
            </CCardHeader>
            <CCardBody className="p-4">
              <CForm
                method="post"
                innerRef={formRef}
                onSubmit={handleSubmit(onSubmit)}
              >
                <input type="hidden" name="depth" defaultValue={1} ref={register()} />
                <CCard accentColor="primary">
                  <CCardHeader>{t('level-category')}</CCardHeader>
                  <CCardBody>
                    <CRow>
                      <CCol lg="6" sm="12" >
                        <CFormGroup>
                          <CLabel>{t('columns-name')}</CLabel>
                          <CInputGroup className={errors.name && 'is-invalid'}>
                            <CInput
                              invalid={errors.name ? true : false}
                              name="name"
                              innerRef={register({ required: requiredMapper.name })}
                              maxLength={128}
                              onKeyUp={onKeyUp}
                            />
                            <CInputGroupAppend>
                              <CInputGroupText className="text-muted">
                                {remaining.name ? remaining.name : 0}/{maxLength.name}
                              </CInputGroupText>
                            </CInputGroupAppend>
                          </CInputGroup>
                          <CInvalidFeedback>
                            {(errors.name && errors.name.type == 'required') && t('The input is an empty string')}
                          </CInvalidFeedback>
                        </CFormGroup>
                      </CCol>
                      <CCol lg="6" sm="12" >
                        <CFormGroup>
                          <CLabel>{t('columns-url')}</CLabel>
                          <CRow>
                            <CCol lg="3" md="6" sm="9">
                              <CSelect
                                name="type"
                                onChange={typeSiwtch}
                                innerRef={register({})}
                              >
                                <option value="not_use">{t('not_use')}</option>
                                <option value="categories_container">{t('categories_container')}</option>
                                <option value="document_container">{t('document_container')}</option>
                                <option value="external_link">{t('external_link')}</option>
                                <option value="internal_link">{t('internal_link')}</option>
                              </CSelect>
                            </CCol>
                            {
                              !!(linkType == 'categories_container') &&
                              <CCol lg="9" sm="6">
                                <CSelect
                                  name="uri"
                                  innerRef={register({})}
                                >
                                  {
                                    documentsLink.category.map((item) => {
                                      return (
                                        <option key={`category-${item.id}`} value={item.route}>{item.name}</option>
                                      )
                                    })
                                  }
                                </CSelect>
                              </CCol>
                            }
                            {
                              !!(linkType == 'document_container') &&
                              <CCol lg="9" sm="6">
                                <CSelect
                                  name="uri"
                                  innerRef={register({})}
                                >
                                  {
                                    documentsLink.normal.map((item) => {
                                      return (
                                        <option key={`normal-${item.id}`} value={item.route}>{item.name}</option>
                                      )
                                    })
                                  }
                                </CSelect>
                              </CCol>
                            }
                            {
                              !!(linkType == 'external_link' || linkType == 'internal_link') &&
                              <CCol lg="9" sm="6">
                                <CInputGroup className={errors.uri && 'is-invalid'}>
                                  <CInput
                                    name="uri"
                                    innerRef={register({ required: requiredMapper.uri })}
                                    maxLength={255}
                                    onKeyUp={onKeyUp}
                                  />
                                  <CInputGroupAppend>
                                    <CInputGroupText className="text-muted">
                                      {remaining.uri ? remaining.uri : 0}/{maxLength.uri}
                                    </CInputGroupText>
                                  </CInputGroupAppend>
                                </CInputGroup>
                              </CCol>
                            }
                          </CRow>
                        </CFormGroup>
                      </CCol>
                      {/*
                      <CCol lg="6" sm="12" >
                        <CFormGroup>
                          <CLabel>{t(`columns-target-open`)}</CLabel>
                          <CSelect
                            name="target"
                            innerRef={register({ required: requiredMapper.target })}
                            maxLength={32}
                          >
                            <option value="_self">{t('_self')}</option>
                            <option value="_blank">{t('_blank')}</option>
                          </CSelect>
                        </CFormGroup>
                      </CCol>
                      */}
                      <CCol lg="6" sm="12" >
                        <CFormGroup>
                          <CLabel>{t(`columns-visible`)}</CLabel>
                          <div className="d-blobk">
                            <CSwitch
                              value={1}
                              name="visible"
                              variant="opposite"
                              color="primary"
                              innerRef={register()}
                            />
                          </div>
                        </CFormGroup>
                      </CCol>
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
                      onClick={resetBaseForm}
                    >
                      <i className="fas fa-undo-alt mr-1"></i>
                      <span>{t('form-reset')}</span>
                    </CButton>
                  </CCardFooter>
                </CCard>
              </CForm>
              {
                !!(list && list.length) &&
                <CRow>
                  <CCol>
                    <hr />
                    <table className="table table-striped table-bordered">
                      <thead className="thead-dark">
                        <tr>
                          <th scope="col" width="55%">{t('columns-name')}</th>
                          {/*<th scope="col" width="30%">{t('external_link')}</th>*/}
                          {/*<th scope="col" width="30%">{t('columns-image')}</th>*/}
                          <th scope="col" width="10%">{t('columns-visible')}</th>
                          <th scope="col" width="20%">{t('columns-sort')}</th>
                          <th scope="col" width="15%">{t('Actions')}</th>
                        </tr>
                      </thead>
                      <tbody>
                        {

                          list.map((item, key) => {
                            return (
                              <Fragment key={`list-${item.id}`}>
                                <tr id={`list-${item.id}`}>
                                  <td>
                                    <CRow>
                                      <div style={{ width: '5%' }}>
                                        {
                                          !!(item.type == 'not_use') &&
                                          <i
                                            className="fas fa-caret-down mt-2 position-absolute"
                                            style={{ left: '2%' }}
                                          />
                                        }
                                      </div>
                                      <div style={{ width: '90%' }}>
                                        <input
                                          className="form-control"
                                          data-id={item.id}
                                          type="text"
                                          defaultValue={item.name}
                                          name="name"
                                        />
                                      </div>
                                    </CRow>
                                  </td>
                                  <td>
                                    <CSwitch
                                      value={1}
                                      name="visible"
                                      variant="opposite"
                                      color="primary"
                                      defaultChecked={item && item.visible == 1}
                                      data-id={item.id}
                                      data-key={key}
                                    />
                                  </td>
                                  <td>
                                    <input
                                      className="form-control"
                                      type="number"
                                      name="sort"
                                      defaultValue={item.sort == 16777215 ? '' : item.sort}
                                      data-id={item.id}
                                      min="1"
                                      max="99"
                                    />
                                  </td>
                                  <td>
                                    <div className="btn-group" role="group">
                                      <CButton size="sm" color="primary" type="button" onClick={saveItem} data-id={item.id}>
                                        <i className="fas fa-save mr-1" />
                                        {t('form-save')}
                                      </CButton>
                                      <CButton
                                        className="text-white"
                                        size="sm"
                                        color="warning"
                                        type="button"
                                        onClick={resetItem}
                                        data-id={item.id}
                                        data-key={key}
                                      >
                                        <i className="fas fa-undo-alt mr-1" />
                                        {t('form-reset')}
                                      </CButton>

                                      <CButton size="sm" color="danger" type="button" onClick={deleteItem} data-id={item.id}>
                                        <i className="fas fa-trash-alt mr-1" />
                                        {t('form-delete')}
                                      </CButton>
                                    </div>
                                  </td>
                                </tr>
                                <LayoutZonesChild
                                  item={item}
                                  t={t}
                                  requiredMapper={requiredMapper}
                                />
                              </Fragment>
                            )
                          })
                        }
                      </tbody>
                    </table>
                  </CCol>
                </CRow>
              }
            </CCardBody>
          </CCard>
        </CTabPane>
      </CTabContent>
    </>
  );
}

export default DocumentsLayoutForm;