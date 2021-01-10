
import React, { useState, useEffect, useRef } from 'react';
import { useTranslation } from 'react-i18next';
import {
  CRow, CCol, CFormGroup, CLabel,
  CCard,
  CInput, CInputFile,
  CInputGroup, CInputGroupAppend, CInputGroupText,
  CInvalidFeedback,
  CTabContent, CTabPane, CCardBody, CButton
} from '@coreui/react'

import { useForm } from "react-hook-form";
import Form from '../Form';
import { useParams } from "react-router-dom";
import axios from 'axios';
import { notify, toConfirm } from '../alertify';

const ProductsAttrsChildForm = (props) => {

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
  const [mediaState, setMediaState] = useState({});
  //const [fileRequire, setFileRequire] = useState(true);
  const { method_or_id } = useParams();
  const [formLists, setFormLists] = useState({
    products_attrs_parent: {
      data: [],
      form: {
        parent_id: method_or_id,
        language_id: 0,
        locale_id: 0,
        table: 'products',
        table_id: 0,
      }
    }
  });

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
  }, [count/*, formLists*/]);

  const formRef = useRef();

  const [listsStore, setListsStore] = useState({});

  const onEleChange = (e) => {
    e.preventDefault();
    let store = {};
    const target = e.target;
    const id = target.dataset.id;
    const name = target.name;
    let value = target.value;

    store[id] = { id: id };
    if (target.type == 'file') {
      value = target.files[0];
      let reader = new FileReader();
      const file = target.files[0];
      reader.readAsDataURL(file);
      reader.onload = () => {
        target.previousElementSibling.src = reader.result;
      }
    }
    setListsStore((listsStore) => {
      if (typeof listsStore[id] === 'undefined') {
        listsStore[id] = {};
      }
      listsStore[id][name] = value;
      return listsStore;
    });
  }

  const onListItemSave = (e) => {
    e.preventDefault();
    //const key = e.currentTarget.dataset.key;
    const id = e.currentTarget.dataset.id;
    if (listsStore[id]) {
      const data = listsStore[id];
      let verify = 0;
      Object.keys(data).forEach((field) => {
        if (data[field]) {
          if (typeof data[field] === 'object') {
            verify += 1;
          } else {
            verify += data[field].length;
          }
        }
      });
      const orginal = formLists.products_attrs_parent.data.filter((item) => {
        if (id == item.id) {
          return true;
        }
        return false;
      });
      if (orginal.sort != 16777215 && !verify) {
        verify++
      }
      //if(verify == 0 && )
      if (verify > 0) {
        var formData = new FormData();
        Object.keys(data).forEach((field) => {
          formData.set(field, data[field]);
        });
        formData.set('id', id);
        axios({
          method: 'post',
          url: '/' + SYS_LANG + '/api/admin/products_attrs?put=1',
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          data: formData,
        }).then((response) => {
          const NOTIFY = response.data.notify.join("");
          if (response.data.code == 0) {
            notify('success', t(NOTIFY), 3, () => {
              if (response) {
                //var listsData = {assets: response.data.data};
                console
                setFormLists(() => {
                  return {
                    products_attrs_parent: response.data.data
                  }
                });
                var event = new MouseEvent('click', {
                  'view': window,
                  'bubbles': true,
                  'cancelable': true
                });
                document.getElementsByClassName('assets-rest-btn').forEach((elm) => {
                  elm.dispatchEvent(event);
                });
              }
            });
          } else {
            notify('error', t(NOTIFY), 5);
          }

        }).catch((error) => {
          if (error.response && typeof error.response.data.notify !== 'undefined') {
            var errotNotify = error.response.data.notify.join('');
            if (errotNotify.indexOf('To many files')) {
              let count = document.getElementsByClassName('attrs-thumbnail').length;
              notify('error', t(errotNotify, { max: 7, count: count }));
            } else {
              notify('error', errotNotify);
            }

          } else {
            notify('error', 'System error.');
          }
        });;
      } else {
        notify('warning', '資料無變動。', 3);
      }
    } else {
      notify('warning', '資料無變動。', 3);
    }
  }

  const onListItemReset = (e) => {
    e.preventDefault();
    const key = e.currentTarget.dataset.key;
    const id = e.currentTarget.dataset.id;
    formLists.products_attrs_parent.data.filter((item, idx) => {
      if (key != idx) {
        return false;
      }
      Object.keys(item).forEach((name) => {
        var eleId = name + id;
        var ele = document.getElementById(eleId);
        if (!ele) {
          return;
        }
        if (ele.type == 'file') {
          ele.files = null;
          ele.value = '';
          ele.previousElementSibling.src = formLists.products_attrs_parent.data[key][name] + '?u=' + Date.now();
        } else {
          const data = formLists.products_attrs_parent.data[key][name];
          ele.value = (data == 16777215 || !data) ? '' : data;
        }
      });
      setListsStore((listsStore) => {
        listsStore[id] = {};
        return listsStore;
      });
      return true;
    });
  }

  const onListItemDel = (e) => {
    e.preventDefault();
    const id = e.currentTarget.dataset.id;
    toConfirm(() => {
      axios({
        method: 'delete',
        url: '/' + SYS_LANG + '/api/admin/products_attrs/' + id,
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }).then((response) => {
        const NOTIFY = response.data.notify.join("");
        if (response.data.code == 0) {
          notify('success', t(NOTIFY), 3, () => {
            if (response) {
              //var listsData = {assets: response.data.data};
              console
              setFormLists(() => {
                return {
                  products_attrs_parent: response.data.data
                }
              });
            }
          });
        } else {
          if (response.data.code == -2) {
            notify('error', t('admin-session-fail'), 3, () => {
              //const pathname = location.pathname.replace('^\/', '');
              //const pathnameSplit = pathname.split('/');
              location.href = `/${SYS_LANG}/admin-login`;
            });
          }
          notify('error', t(NOTIFY), 5);
        }

      });
    }, t);
  }

  return (
    <CTabContent>
      <CTabPane data-tab="products-attrs-child-form">
        <CCard className="tab-card">
          <CCardBody>
            <CRow className="mt-2">
              <CCol md="12" sm="12">
                <table className="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th scope="col">名稱</th>
                      <th scope="col">內容</th>
                      <th scope="col">圖片</th>
                      <th scope="col">排序</th>
                      <th scope="col">控制</th>
                    </tr>
                  </thead>
                  <tbody>
                    {
                      formLists.products_attrs_parent.data.map((item, key) => {
                        return (
                          <tr key={key}>
                            <td>
                              <input
                                id={'name' + item.id}
                                name="name"
                                onChange={onEleChange}
                                data-id={item.id}
                                className="form-control"
                                type="text"
                                defaultValue={item.name}
                                maxLength={128}
                              />
                            </td>
                            <td>
                              <input
                                id={'value' + item.id}
                                name="value"
                                onChange={onEleChange}
                                data-id={item.id}
                                className="form-control"
                                type="text"
                                defaultValue={item.value}
                                maxLength={1024}
                              />
                            </td>
                            <td>
                              <img src={item.photo} className="form-thumbnail-5 attrs-thumbnail mb-2" />
                              <CInputFile
                                id={'photo' + item.id}
                                key={'photo' + key}
                                onChange={onEleChange}
                                name="photo"
                                accept="image/*"
                                data-id={item.id}
                              />
                            </td>
                            <td>
                              <input
                                id={'sort' + item.id}
                                key={'sort' + key}
                                className="form-control"
                                onChange={onEleChange}
                                data-id={item.id}
                                name="sort"
                                type="number"
                                min="1" max="16777215"
                                defaultValue={item.sort == '16777215' ? '' : item.sort}
                              />
                            </td>
                            <td>
                              <div className="btn-group btn-group-toggle">
                                <CButton
                                  data-key={key}
                                  color="primary"
                                  size="sm"
                                  type="button"
                                  data-id={item.id}
                                  onClick={onListItemSave}
                                >
                                  <i className="fas fa-save mr-1"></i>
                                  <span>{t('form-save')}</span>
                                </CButton>
                                <CButton
                                  data-key={key}
                                  type="button"
                                  className="text-white assets-rest-btn"
                                  color="warning"
                                  size="sm"
                                  data-id={item.id}
                                  onClick={onListItemReset}
                                >
                                  <i className="fas fa-undo-alt mr-1"></i>
                                  <span>{t('form-reset')}</span>
                                </CButton>
                                <CButton
                                  data-key={key}
                                  type="button"
                                  color="danger"
                                  size="sm"
                                  data-id={item.id}
                                  onClick={onListItemDel}
                                >
                                  <i className="fas fa-trash-alt mr-1"></i>
                                  <span>{t('form-delete')}</span>
                                </CButton>
                              </div>
                            </td>
                          </tr>
                        );
                      })
                    }
                    {
                      formLists.products_attrs_parent.data.length === 0 &&
                      <tr><td colSpan="5" className="text-center">{t('No data.')}</td></tr>
                    }
                  </tbody>
                </table>
              </CCol>
            </CRow>
          </CCardBody>
          <Form innerRef={formRef} formLists={formLists} setFormLists={setFormLists} href={href} griduse {...methods} remainderChange={remainderChange} setMediaState={setMediaState} {...props}>
            <CRow>
              <CCol md="6" sm="12" className="mt-2">
                <CFormGroup>
                  <input type="hidden" name="table" defaultValue={formLists.products_attrs_parent.form.table} ref={register({ required: true })} />
                  <input type="hidden" name="table_id" defaultValue={formLists.products_attrs_parent.form.table_id} ref={register({ required: true })} />
                  <input type="hidden" name="parent_id" defaultValue={formLists.products_attrs_parent.form.parent_id} ref={register({ required: true })} />
                  <input type="hidden" name="language_id" defaultValue={formLists.products_attrs_parent.form.language_id} ref={register({ required: true })} />
                  <input type="hidden" name="locale_id" defaultValue={formLists.products_attrs_parent.form.locale_id} ref={register({ required: true })} />
                  <CLabel>{t('columns-name')}</CLabel>
                  <CInputGroup className={errors.name && 'is-invalid'}>
                    <CInput
                      name="name"
                      invalid={errors.name ? true : false}
                      type="text"
                      maxLength="128"
                      onChange={remainderChange}
                      innerRef={register({ required: true })}
                    />
                    <CInputGroupAppend>
                      <CInputGroupText className="text-muted">{remaining.name ? remaining.name : 0}/{maxLength.name}</CInputGroupText>
                    </CInputGroupAppend>
                  </CInputGroup>
                  <CInvalidFeedback>{(errors.name && errors.name.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                </CFormGroup>
              </CCol>
              <CCol md="6" sm="12" className="mt-2">
                <CFormGroup>
                  <CLabel>{t('columns-value')}</CLabel>
                  <CInputGroup className={errors.value && 'is-invalid'}>
                    <CInput
                      name="value"
                      invalid={errors.value ? true : false}
                      type="text"
                      maxLength="1024"
                      onChange={remainderChange}
                      innerRef={register({ required: true })}
                    />
                    <CInputGroupAppend>
                      <CInputGroupText className="text-muted">{remaining.value ? remaining.value : 0}/{maxLength.value}</CInputGroupText>
                    </CInputGroupAppend>
                  </CInputGroup>
                  <CInvalidFeedback>{(errors.value && errors.value.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                </CFormGroup>
              </CCol>
              <CCol md="6" sm="12" className="mt-2">
                <CFormGroup>
                  <CLabel>{t('columns-photo')}</CLabel>
                  <CInputFile
                    name="photo"
                    innerRef={register({ required: true })}
                    invalid={errors.path ? true : false}
                    onChange={singleFileOnChange}
                    accept="image/*"
                  />
                  <CInvalidFeedback>{(errors.photo && errors.photo.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                  <img id="attributes-photo-preview" className={'mt-2 img-fluid form-thumbnail-5 ' + (mediaState.photo ? '' : 'd-none')} src={mediaState.photo && mediaState.photo.path} />
                </CFormGroup>
              </CCol>
              <CCol md="6" sm="12">
                <CFormGroup>
                  <CLabel>{t('columns-sort')}</CLabel>
                  <CInput invalid={errors.sort ? true : false} name="sort" type="number" innerRef={register({ min: 0, max: 16777215 })} />
                  <CInvalidFeedback>{errors.sort && t('The input is not between \'%min%\' and \'%max%\', inclusively', { min: 0, max: 16777215 })}</CInvalidFeedback>
                </CFormGroup>
              </CCol>

            </CRow>
          </Form>
        </CCard>
      </CTabPane>
    </CTabContent>
  );
}

export default ProductsAttrsChildForm;