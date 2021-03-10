import React, { useState, useEffect, useRef } from 'react';
import { connect } from "react-redux";
import { FORM_ROWS } from "../../../actions/formRowsAction";
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

const AssetsForm = (props) => {
  const { dispatch } = props;
  const { t } = useTranslation(['translation']);
  const methods = useForm({ mode: 'all' });
  const { register, errors } = methods;
  let href = props.href;
  const matcher = location.pathname.match(/\/\d+$/);
  const basePath = window.pageConfig.basePath;
  if (location.pathname.match(/\/\d+$/)) {
    href = href.replace(/\/$/, '') + matcher[0];
  } else {
    href += 'add';
  }

  const [maxLength, setMaxLength] = useState({});
  const [remaining, setRemaining] = useState({});
  const [mediaState, setMediaState] = useState({});

  const { method_or_id } = useParams();

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
  }, [count]);

  const formRef = useRef();

  const onEleChange = (e) => {
    e.preventDefault();
    let store = {};
    const target = e.target;
    const id = target.dataset.id;

    store[id] = { id: id };
    if (target.type == 'file') {
      let reader = new FileReader();
      const file = target.files[0];
      reader.readAsDataURL(file);
      reader.onload = () => {
        target.previousElementSibling.src = reader.result;
        target.previousElementSibling.classList.remove('d-none');
      }
    }
  }

  const onListItemSave = (e) => {
    e.preventDefault();
    const saveBtnEle = e.currentTarget;

    const id = e.currentTarget.dataset.id;
    let row = null;
    props.formRows.assets.forEach((item) => {
      if (item.id == id) {
        row = item;
        return;
      }
    });
    if (row) {
      var data = {}
      Array.from(saveBtnEle.parentElement.parentElement.parentElement.querySelectorAll('input')).forEach((ele) => {
        let name = ele.name.replace(/\d+$/, '');
        if (name == 'path' && ele.files.length) {
          data[name] = ele.files[0];
        } else {
          let compare = row[name];
          if (name == 'sort' && compare == 16777215) {
            compare = '';
          }
          if (compare != ele.value && name != 'path') {
            data[name] = ele.value;
          }
          if (name == 'sort' && compare < 16777215 && ele.value == '') {
            data[name] = 16777215;
          }
        }

      });
      let verify = 0;
      Object.keys(data).forEach((field) => {
        if (data[field]) {
          if (typeof data[field] === 'object') {
            verify += 1;
          } else {
            verify += data[field].toString().length;
          }
        }
      });

      if (verify > 0) {
        var formData = new FormData();
        Object.keys(data).forEach((field) => {
          formData.set(field, data[field]);
        });
        formData.set('id', id);
        const loadingBackgroundDom = document.getElementById('loading-background');
        loadingBackgroundDom.classList.remove('d-none');
        const postApi = `${basePath}/${SYS_LANG}/api/admin/assets?put=1`.replace(/^\/{2,}/, '/');
        axios({
          method: 'post',
          url: postApi,
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          data: formData,
        }).then((response) => {
          loadingBackgroundDom.classList.add('d-none');
          const NOTIFY = response.data.notify.join("");
          if (response.data.code == 0) {
            notify('success', t(NOTIFY), 3, () => {
              if (response) {
                //var listsData = {assets: response.data.data};
                if (response) {
                  dispatch({ type: FORM_ROWS, data: { assets: response.data.data } });
                }
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
          loadingBackgroundDom.classList.add('d-none');
          if (error.response && typeof error.response.data.notify !== 'undefined') {
            var errotNotify = error.response.data.notify.join('');
            if (errotNotify.indexOf('To many files')) {
              let count = document.getElementsByClassName('assets-thumbnail').length;
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
    if (props.formRows && props.formRows.assets) {
      const assets = props.formRows.assets;
      assets.filter((item, idx) => {
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
            const src = (basePath + assets[key][name] + '?u=' + Date.now()).replace(/^\/{2,}/, '/');
            ele.previousElementSibling.src = src;
            //console.log(item);
            if (!item.path.length) {
              ele.previousElementSibling.classList.add('d-none');
            }
          } else {
            const data = assets[key][name];
            ele.value = (data == 16777215 || !data) ? '' : data;
          }
        });
        return true;
      });

    }
  }

  const onListItemDel = (e) => {
    e.preventDefault();
    const id = e.currentTarget.dataset.id;
    const loadingBackgroundDom = document.getElementById('loading-background');
    toConfirm(() => {
      loadingBackgroundDom.classList.remove('d-none');
      const deleteApi = `${basePath}/${SYS_LANG}/api/admin/assets/${id}`.replace(/^\/{2,}/, '/');
      axios({
        method: 'delete',
        url: deleteApi,
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }).then((response) => {
        loadingBackgroundDom.classList.add('d-none');
        const NOTIFY = response.data.notify.join("");
        if (response.data.code == 0) {
          notify('success', t(NOTIFY), 3, () => {
            if (response) {
              dispatch({ type: FORM_ROWS, data: { assets: response.data.data } });
            }
          });
        } else {
          if (response.data.code == -2) {
            notify('error', t('admin-session-fail'), 3, () => {
              const redirect = `${basePath}/${SYS_LANG}/admin-login`.replace(/^\/{2,}/, '/');
              location.href = redirect;
            });
          }
          notify('error', t(NOTIFY), 5);
        }

      });
    }, t);
  }

  return (
    <CTabContent>
      <CTabPane data-tab="assets-form">
        <CCard className="tab-card">
          <CCardBody>
            <CRow className="mt-2">
              <CCol md="12" sm="12">
                <table className="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th scope="col">名稱</th>
                      <th scope="col">圖片</th>
                      <th scope="col">排序</th>
                      <th scope="col">控制</th>
                    </tr>
                  </thead>
                  <tbody>
                    {
                      (props.formRows && props.formRows.assets) &&
                      props.formRows.assets.map((item, key) => {
                        const itemPath = (basePath + item.path).replace(/^\/{2,}/, '/');
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
                              />
                            </td>
                            <td>
                              <img src={itemPath} className={(item.path.length > 0) ? 'form-thumbnail-10 assets-thumbnail' : 'form-thumbnail-10 assets-thumbnail d-none'} />
                              <CInputFile
                                id={'path' + item.id}
                                key={'path' + key}
                                onChange={onEleChange}
                                name="path"
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
                      !!(!props.formRows) &&
                      <tr><td colSpan="4" className="text-center">{t('No data.')}</td></tr>
                    }
                  </tbody>
                </table>
              </CCol>
            </CRow>
          </CCardBody>
          <Form
            innerRef={formRef}
            href={href}
            griduse {...methods}
            remainderChange={remainderChange}
            setMediaState={setMediaState}
            setRemaining={setRemaining}
            {...props}
          >
            <CRow className="mt-2">
              <CCol md="6" sm="12">
                <CFormGroup>
                  <input type="hidden" name="table" value="products" ref={register({ required: true })} />
                  <input type="hidden" name="table_id" value={method_or_id} ref={register({ required: true })} />
                  <CLabel>{t('columns-name')}</CLabel>
                  <CInputGroup className={errors.name && 'is-invalid'}>
                    <CInput
                      name="name"
                      invalid={errors.name ? true : false}
                      type="text"
                      maxLength="64"
                      onChange={remainderChange}
                      innerRef={register()}
                    />
                    <CInputGroupAppend>
                      <CInputGroupText className="text-muted">{remaining.name ? remaining.name : 0}/{maxLength.name}</CInputGroupText>
                    </CInputGroupAppend>
                  </CInputGroup>
                  <CInvalidFeedback>{(errors.name && errors.name.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                </CFormGroup>
              </CCol>
              <CCol md="6" sm="12">
                <CFormGroup>
                  <CLabel>{t('columns-path')}</CLabel>
                  <CInputFile
                    name="path"
                    innerRef={register({ required: true })}
                    invalid={errors.path ? true : false}
                    onChange={singleFileOnChange}
                    accept="image/*"
                  />
                  <CInvalidFeedback>{(errors.path && errors.path.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                  <img id="assets-path-preview" className={'mt-2 img-fluid form-thumbnail ' + (mediaState.path ? '' : 'd-none')} src={mediaState.path && mediaState.path.path} />
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
    formRows: state.formRows
  };
};

//export default Documents;
export default connect(mapStateToProps)(AssetsForm);
