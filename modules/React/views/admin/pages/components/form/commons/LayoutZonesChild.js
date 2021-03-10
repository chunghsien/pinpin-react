import React, { useState, useEffect, useRef } from 'react';
import {
  CRow,
  CCardFooter,
  CCol,
  CInputGroup,
  CInputGroupAppend,
  CInputGroupText,
  CForm,
  CInputFile,
  CFormGroup,
  CLabel,
  CInput,
  CSelect,
  CButton,
  CSwitch,
  CInvalidFeedback
} from '@coreui/react';
import Accordion from 'react-bootstrap/Accordion'
import Card from 'react-bootstrap/Card'
import Button from 'react-bootstrap/Button';
import { useForm } from "react-hook-form";
import axios from 'axios';
import { alertify, notify, toConfirm } from '../../alertify';

const LayoutZonesChild = ({ item, t, requiredMapper }) => {
  const url = location.pathname.replace(/admin/, 'api/admin');
  const [documentsLink, setDocumentsLink] = useState({});
  const methods = useForm({ mode: 'all' });
  const [mediaState, setMediaState] = useState({});
  const { register, errors, handleSubmit } = methods;
  const [remaining, setRemaining] = useState({});

  const loadingBackgroundDom = document.getElementById('loading-background');
  const onSubmit = (/*data*/) => {
    let uri = url.replace(/\/\d$/, '');
    loadingBackgroundDom.classList.remove('d-none');
    uri += '?is_children=1';
    var formData = new FormData(formRef.current);
    if (formRef.current.visible.checked == false) {
      formData.set('visible', 0);
    }
    axios.post(uri, formData).then((response) => {
      const data = response.data;
      loadingBackgroundDom.classList.add('d-none');
      if (data.code == 0) {
        setChildrens(data.data.children);
        setDocumentsLink(data.data.documents_link);
        setMediaState({ image: false });
        notify('success', t(data.notify.join("\n")), 3);
        setLinkType('document_container');
        formRef.current.reset();
        setRemaining((remaining) => {
          let res = {
            ...remaining,
            ...{ name: 0 }
          }
          return res;
        });

      } else {
        if (response.data.code == -2) {
          notify('error', t('admin-session-fail'), 3);
        }
        notify('error', t(data.notify.join("\n")));
      }
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

  const modifyUpload = (e) => {
    let reader = new FileReader();
    //let dom = null;
    e.preventDefault();
    const files = e.target.files;
    const key = e.target.dataset.key;
    reader.readAsDataURL(files[0]);
    reader.onload = () => {
      var img = document.getElementById(`img-${key}`);
      img.src = reader.result;
      img.style.height = '50px';
      img.classList.remove("d-none");
    }
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

  const [linkType, setLinkType] = useState('document_container');
  const typeSiwtch = (e) => {
    e.preventDefault();
    setLinkType(e.currentTarget.value);
  }


  const NC = 0;
  const [childrens, setChildrens] = useState([]);
  useEffect(() => {
    getChildren();
  }, [NC]);

  const getChildren = () => {
    let apiUri = url.replace(/\/\d+$/, '');
    axios.get(apiUri, {
      params: {
        is_children: 1,
        parent_id: item.id
      }
    }).then((response) => {
      const data = response.data.data;
      setChildrens(data.children);
      setDocumentsLink(data.documents_link);
    });
  }
  const sucessNotify = (data) => {
    if (data.code == 0) {
      notify('success', t(data.notify.join("\n")), 3);
    } else {
      if (data.code == -2) {
        notify('error', t('admin-session-fail'), 3);
      }
      if (data.code == 1) {
        notify('warning', t(data.notify.join("\n")));
      } else {
        notify('error', t(data.notify.join("\n")));
      }


    }

  }

  const saveItem = (e) => {
    e.preventDefault();
    const target = e.currentTarget;
    //const key = target.dataset.key;
    let id = target.dataset.id;
    var formData = new FormData();
    formData.append('id', id);
    document.getElementById(`children-${id}`).querySelectorAll('input').forEach((ele) => {
      const name = ele.name;
      var value = null;
      if (ele.type == 'file') {
        if (ele.value) {
          value = ele.files[0];
        }
      } else {
        value = ele.value;
        if (ele.type == 'number' && value == '') {
          value = 16777215;
        }
      }
      if (value) {
        formData.append(name, value);
      }
    });
    let apiUri = url.replace(/\/\d+$/, '');
    axios.post(`${apiUri}?put=1`, formData, { headers: { "Content-Type": "multipart/form-data" } }).then((response) => {
      const data = response.data;
      sucessNotify(data);
      if (data.code == 0) {
        setChildrens(data.data.list);
      }
    });

  }

  const itemReset = (e) => {
    e.preventDefault();
    const target = e.currentTarget;
    const key = target.dataset.key;
    const id = target.dataset.id;
    changeInputValue(id, key, childrens);
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
        sucessNotify(data);
        setChildrens(data.data.list);
      });
    }, t);
  }

  const changeInputValue = (id, key, data) => {
    document.getElementById(`children-${id}`).querySelectorAll('input').forEach((ele) => {
      const name = ele.name;
      if (ele.type == 'text' || ele.type == 'number') {
        let value = '';
        if (ele.type == 'number') {
          value = data[key][name] == 16777215 ? '' : data[key][name];
        } else {
          value = data[key][name]
        }
        ele.value = value;
      }
      if (ele.type == 'checkbox') {
        let value = data[key][name] == 1 ? true : false;
        ele.checked = value;
      }
      if (ele.type == 'file') {
        ele.value = null;
        var img = document.getElementById(`img-${key}`);
        img.src = data[key][name];
        img.style.height = '50px';
        if (data[key][name]) {
          img.classList.remove("d-none");
        } else {
          img.classList.add("d-none");
        }
      }
    });
    
  }

  const onManualReset = (e) => {
    e.preventDefault();
    formRef.current.reset();
    setLinkType('document_container');
  }
  const formRef = useRef();
  return (
    <>
      {
        !!(item.type == 'not_use') &&
        <tr>
          <td colSpan="5">
            <Accordion defaultActiveKey="1">
              <Card className="mb-0">
                <Card.Header className="bg-success">
                  <Accordion.Toggle className="text-white" as={Button} variant="link" eventKey="0">子分類新增</Accordion.Toggle>
                </Card.Header>
                <Accordion.Collapse eventKey="0">
                  <CForm
                    method="post"
                    innerRef={formRef}
                    onSubmit={handleSubmit(onSubmit)}
                  >
                    <input type="hidden" name="depth" defaultValue={2} ref={register()} />
                    <Card.Body>
                      <CRow>
                        <CCol md="6" sm="12" >
                          <CFormGroup>
                            <CLabel>{t('columns-name')}</CLabel>
                            <CInput type="hidden" name="parent_id" defaultValue={item.id} innerRef={register()} />
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
                                  {remaining.name ? remaining.name : 0}/128
                                </CInputGroupText>
                              </CInputGroupAppend>
                            </CInputGroup>
                            <CInvalidFeedback>
                              {(errors.name && errors.name.type == 'required') && t('The input is an empty string')}
                            </CInvalidFeedback>
                          </CFormGroup>
                        </CCol>
                        <CCol md="6" sm="12" >
                          <CFormGroup>
                            <CLabel>{t('columns-url')}</CLabel>
                            <CRow>
                              <CCol sm="3">
                                <CSelect
                                  name="type"
                                  onChange={typeSiwtch}
                                  innerRef={register({})}
                                >
                                  <option value="document_container">{t('document_container')}</option>
                                  <option value="internal_link">{t('internal_link')}</option>
                                  <option value="external_link">{t('external_link')}</option>
                                </CSelect>
                              </CCol>
                              {
                                (linkType == 'document_container' && documentsLink.normal) &&
                                <CCol sm="9">
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
                                (linkType == 'external_link' && documentsLink.normal) &&
                                <CCol sm="9">
                                  <CInputGroup className={errors.uri && 'is-invalid'}>
                                    <CInput
                                      name="uri"
                                      innerRef={register({ required: requiredMapper.uri })}
                                      maxLength={255}
                                      onKeyUp={onKeyUp}
                                    />
                                    <CInputGroupAppend>
                                      <CInputGroupText className="text-muted">
                                        {remaining.uri ? remaining.uri : 0}/255
                                      </CInputGroupText>
                                    </CInputGroupAppend>
                                  </CInputGroup>
                                </CCol>
                              }
                            </CRow>
                          </CFormGroup>
                        </CCol>
                        <CCol md="6" sm="12" >
                          <CFormGroup>
                            <CLabel>{t(`columns-visible`)}</CLabel>
                            <div className="d-blobk">
                              <CSwitch value={1} name="visible" variant="opposite" color="primary" innerRef={register()} />
                            </div>
                          </CFormGroup>
                        </CCol>
                        <CCol md="6" sm="12" >
                          <CFormGroup>
                            <CLabel>{t(`columns-image`)}</CLabel>
                            <CInputFile
                              className="w-auto"
                              name="image"
                              innerRef={register({ required: requiredMapper.image })}
                              onChange={singleFileOnChange}
                              accept="image/*"
                            />
                            {
                              mediaState.image &&
                              <img
                                id={`img-${key}`}
                                style={{ height: "2.5rem" }}
                                id="image_image-preview"
                                className={'mt-2 form-thumbnail ' + (mediaState.image ? '' : 'd-none')}
                                src={mediaState.image && mediaState.image.path}
                              />
                            }

                          </CFormGroup>
                        </CCol>
                      </CRow>
                    </Card.Body>
                    <CCardFooter>
                      <CButton size="sm" color="primary" type="submit" className="mr-1">
                        <i className="fas fa-check mr-1"></i>
                        <span>{t('form-submit')}</span>
                      </CButton>
                      <CButton className="text-white" size="sm" color="warning" type="button" onClick={onManualReset}>
                        <i className="fas fa-undo-alt mr-1"></i>
                        <span>{t('form-reset')}</span>
                      </CButton>
                    </CCardFooter>
                  </CForm>
                </Accordion.Collapse>
              </Card>
              <Card className="mb-0">
                <Card.Header className="bg-info">
                  <Accordion.Toggle className="text-white" as={Button} variant="link" eventKey="1">子分類列表</Accordion.Toggle>
                </Card.Header>
                <Accordion.Collapse eventKey="1">
                  <Card.Body>

                    {
                      !!(childrens) &&
                      <table className="table table-striped table-bordered">
                        <thead className="thead-dark">
                          <tr>
                            <th scope="col" width="25%">{t('columns-name')}</th>
                            <th scope="col" width="20%">{t('uri')}</th>
                            <th scope="col" width="20%">{t('columns-image')}</th>
                            <th scope="col" width="10%">{t('columns-visible')}</th>
                            <th scope="col" width="10%">{t('columns-sort')}</th>
                            <th scope="col" width="15%">{t('Actions')}</th>
                          </tr>
                        </thead>
                        <tbody>
                          {
                            childrens.map((item, key) => {
                              return (
                                <tr key={`children-${item.id}`} id={`children-${item.id}`}>
                                  <td>
                                    <input
                                      className="form-control"
                                      data-id={item.id}
                                      type="text"
                                      name="name"
                                      defaultValue={item.name}
                                    />
                                  </td>
                                  <td>
                                    {
                                      !!(item.type == 'external_link') &&
                                      <input
                                        className="form-control"
                                        data-id={item.id}
                                        type="text"
                                        defaultValue={item.uri}
                                        name="uri"
                                      />
                                    }
                                    {
                                      (item.type !== 'external_link') &&
                                      <>{item.uri}</>
                                    }
                                  </td>
                                  <td>
                                    <img
                                      id={`img-${key}`}
                                      src={item.image}
                                      height="50"
                                      className={`mb-1 ${item.image ? item.image : 'd-none'}`}
                                    />
                                    <CInput
                                      data-id={item.id}
                                      data-key={key}
                                      type="file"
                                      onChange={modifyUpload}
                                      name="image"
                                    />
                                  </td>
                                  <td>
                                    <CSwitch
                                      value={1}
                                      name="visible"
                                      variant="opposite"
                                      color="primary"
                                      defaultChecked={item.visible === 1}
                                      data-id={item.id}
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
                                      <CButton
                                        size="sm"
                                        color="primary"
                                        type="button"
                                        data-id={item.id}
                                        data-key={key}
                                        onClick={saveItem}
                                      >
                                        <i className="fas fa-save mr-1" />
                                        {t('form-save')}
                                      </CButton>
                                      <CButton
                                        className="text-white"
                                        size="sm"
                                        color="warning"
                                        type="reset"
                                        data-id={item.id}
                                        data-key={key}
                                        onClick={itemReset}
                                      >
                                        <i className="fas fa-undo-alt mr-1" />
                                        {t('form-reset')}
                                      </CButton>

                                      <CButton
                                        size="sm"
                                        color="danger"
                                        type="button"
                                        data-id={item.id}
                                        onClick={deleteItem}
                                      >
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

export default LayoutZonesChild;