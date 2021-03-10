import React, { Suspense, useEffect, useRef, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { connect } from "react-redux";
import { FORM_ROWS } from "../../actions/formRowsAction";
import axios from 'axios';
import { CForm, CCardBody, CCardFooter, CButton, CAlert } from '@coreui/react'
import formBreadItemRename from './formBreadItemRename';
import { notify } from './alertify';
import { useLocation, useHistory, useParams } from "react-router-dom";
import Loading from 'react-fullscreen-loading';

const Form = (props) => {
  const { dispatch } = props;
  //屬性型態的資料表名稱集合，indexOf有媒合要找table及table_id送出要資料
  const accessoryTables = [
    'documents_content', 'assets', 'attributes', 'facebook_tags'/*, 'documents'*/, 'seo'
  ];

  const [formFieldsValue, setFormFieldsValue] = useState({});

  const { t } = useTranslation(['translation']);
  const { method_or_id } = useParams();

  const { handleSubmit, reset } = props;
  const location = useLocation();
  const history = useHistory();
  let USE_TABLE = props.href.replace(/^\/admin\//, '').replace(/\/\d+$/, '');
  const { formRows } = props;
  const useRow = (formRows && formRows[USE_TABLE]) ? formRows[USE_TABLE] : null;
  const basePath = window.pageConfig.basePath;
  let main_table_split = location.pathname
    .replace(/\/\d+$/, '')
    .replace(/\/add$/, '').split('/');
  const MAIN_TABLE = main_table_split[main_table_split.length - 1];
  const onSubmit = (data) => {
    let href = props.href;
    let method = location.pathname.match(/add$/) ? 'post' : 'put';

    if (typeof cformRef.current.elements.id !== 'undefined') {
      const idElement = cformRef.current.elements.id;
      if (idElement.value) {
        method = 'put';
      } else {
        method = 'post';
      }
    } else {
      method = 'post'
    }
    if (href.match(SYS_LANG)) {
      if (!href.match('/api')) {
        href = href.replace('admin', 'api/admin');
      }
    } else {
      href = '/' + SYS_LANG + '/api/' + href.replace(/^\//, '');
    }
    href = (basePath + '/' + href).replace(/^\/{2,}/, '/');
    let formData = new FormData();
    const dataKeys = Object.keys(data);
    dataKeys.forEach((name) => {
      if (data[name]) {
        if (typeof data[name] == 'object' && data[name].constructor.name == 'FileList' && data[name].length == 1) {
          formData.append(name, data[name][0]);
        } else if (data[name].constructor.name != 'FileList') {
          formData.append(name, data[name]);
        }
      }
    });
    const loadingBackgroundDom = document.getElementById('loading-background');
    loadingBackgroundDom.classList.remove('d-none');
    axios({
      method: 'post',
      url: method == 'post' ? href : href += '?put=1',
      headers: {
        'Content-Type': 'multipart/form-data'
      },
      data: formData,
    }).then((response) => {
      if (response.data.data && response.data.notify) {
        var DATA = response.data.data;
        const NOTIFY = response.data.notify.join("");

        if (response.data.code >= 0) {
          let control_table = props.href.replace(/^\/admin\//, '').replace(/\/\d+$/, '').replace(/\/add$/, '');
          let dispatchData = {};
          Object.keys(DATA).forEach((field) => {
            if (field.match(/(image|photo|path|file|avater|banner)$/) && DATA[field]) {
              const dateTime = new Date().getTime();
              const timestamp = Math.floor(dateTime / 1000);
              let path = `${DATA[field]}?t=${timestamp}`;
              DATA[field] = path;
            }
          });
          if (typeof DATA.password != 'undefined') {
            props.setPasswordRequire(false);
            delete DATA.password;
          }
          if (typeof props.classRelation != 'undefined') {
            let bind = props.classRelation.bind;
            dispatchData[bind] = DATA;
          } else {
            dispatchData[control_table] = DATA;
          }

          dispatch({ type: FORM_ROWS, data: dispatchData });

          if ((!Array.isArray(DATA)) && DATA.id) {
            const id = DATA.id;
            if (typeof props.setFormSelected == 'function') {
              props.setFormSelected((state) => {
                var responseState = state;
                Object.keys(state).forEach((key) => {
                  if (DATA[key]) {
                    let dataAssign = DATA[key] ? DATA[key] : '';
                    if (typeof dataAssign == 'object') {
                      dataAssign = JSON.stringify(dataAssign);
                    }
                    responseState[key] = dataAssign;
                  }
                });
                return responseState;
              });
            }
            //新增完填值
            if (typeof cformRef.current.elements.id !== 'undefined' && id) {
              if (typeof props.setFileRequire == 'function') {
                props.setFileRequire(false);
              }
            }
            if (typeof DATA.options !== 'undefined' && props.setReactSelectOptions) {

              props.setReactSelectOptions((/*state*/) => {
                return {
                  //...state,
                  ...{
                    options: DATA.options,
                    values: DATA.values
                  }
                }
              });
            }
            if (localStorage.getItem('copyId')) {
              localStorage.removeItem('copyId');
            }
          } else {
            if (!DATA.options && DATA.values) {
              if (resetAssets(response)) {
                return;
              }
            }
          }
          loadingBackgroundDom.classList.add('d-none');
          if (response.data.code == 0) {
            notify('success', t(NOTIFY), 3, () => {
              if (props.griduse) {
                if (control_table == MAIN_TABLE) {
                  const newHistory = location.pathname.replace(/add$/, DATA.id);
                  history.push(newHistory);
                }
              }
            });
          }
          if (response.data.code == 1) {
            notify('warning', t(response.data.notify.join("")), 3);
          }
        } else {
          if (response.data.code == -2) {
            notify('error', t('admin-session-fail'), 3, () => {
              const loginPath = `${basePath}/${SYS_LANG}/admin-login`.replace(/\/{2,}/, '/');
              location.href = loginPath;
            });
          }
          notify('error', t(NOTIFY));
        }

      } else {
        if (response.data.code == -2) {
          notify('error', t('admin-session-fail'), 3, () => {
            const loginPath = `${basePath}/${SYS_LANG}/admin-login`.replace(/\/{2,}/, '/');
            location.href = loginPath;
          });
        } else {
          notify('error', 'System error.');
        }

      }
      loadingBackgroundDom.classList.add('d-none');
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

    });
  };

  const resetAssets = (response) => {
    //const cFormElement = cformRef.current;
    let control_table = props.href.replace(/^\/admin\//, '').replace(/\/\d+$/, '');
    if (control_table.match(/products_attrs_parent/)) {
      var split = control_table.split('/');
      control_table = split[1];
    }

    if (response && response.data && response.data.code == 0) {
      if (typeof props.setRemaining == 'function') {
        //重設字數
        props.setRemaining((state) => {
          let responseState = {};
          Object.keys(state).forEach((key) => {
            responseState[key] = 0;
          });
          return responseState;
        });
      }
      var listsData = {};
      listsData[control_table] = response.data.data;
      if (typeof props.setFormLists == 'fuction') {
        props.setFormLists(() => {
          return {
            ...props.formLists,
            ...listsData
          }
        });
      }
      //cFormElement.reset();
    }
    const loadingBackgroundDom = document.getElementById('loading-background');
    loadingBackgroundDom.classList.add('d-none');
    return /*control_table == 'assets' && */typeof props.setFormLists == 'function';
  }

  const formReset = (e) => {
    e.preventDefault();

    if (typeof props.twZipPacakge != 'undefined') {
      const { data, states } = props.twZipPacakge;
      const { setCountyValue, onDistrict, setDistrictOptions } = states;
      if (props.formRows) {
        const rowObject = props.formRows[USE_TABLE];
        setCountyValue(rowObject.county);
        setDistrictOptions(data[rowObject.county]);
        onDistrict(rowObject.district);
      } else {
        const countyKeys = Object.keys(data);
        setCountyValue(countyKeys[0]);
        setDistrictOptions(data[countyKeys[0]]);
        const districtKeys = Object.keys(data[countyKeys[0]]);
        onDistrict(districtKeys[0]);
      }
    }

    const cFormElement = cformRef.current;
    //以後 select element 使用的reset模式 start
    if (typeof props.setFormSelected == 'function') {
      props.setFormSelected((state) => {
        let response = state;
        Object.keys(state).forEach((key) => {
          if (useRow) {
            response[key] = useRow[key];
          } else {
            response[key] = '';
          }

        });
        return response;
      });
    }
    //以後 select element 使用的reset模式 end

    //以後 switch element 使用的reset模式 start
    if (typeof props.setFormSwitched == 'function') {
      props.setFormSwitched((state) => {
        var response = Object.assign({}, state);
        if (typeof state[USE_TABLE] == 'undefined') {
          Object.keys(state).forEach((key) => {
            if (useRow) {
              response[key] = useRow[key];
            } else {
              response[key] = 0;
            }
          });
        } else {
          response[USE_TABLE] = useRow.values;
        }
        return {
          ...state,
          ...response
        };
      });
    }
    //以後 switch element 使用的reset模式 end

    if (typeof props.getBannerImage == 'function' && useRow.values) {
      props.getBannerImage(useRow.values.banner);
    }

    if (resetAssets()) {
      return;
    }

    //清空reactSelectOptions.values
    if (typeof props.setReactSelectOptions == 'function') {
      var bind = props.classRelation.bind;
      if (formRows[bind]) {
        props.setReactSelectOptions(formRows[bind]);
      }
    }
    if (formFieldsValue[USE_TABLE]) {
      const formData = formFieldsValue[USE_TABLE];
      cFormElement.elements.forEach((elm) => {
        if (elm.tagName.toLowerCase() == 'button') {
          return;
        }
        const name = elm.name;

        if (elm.type == 'file') {
          elm.value = '';
          let imgElm = null;
          if (elm.nextElementSibling && elm.nextElementSibling.tagName.toLowerCase() == 'img') {
            imgElm = elm.nextElementSibling;
          }
          if (!imgElm) {
            if (elm.nextElementSibling && elm.nextElementSibling.nextElementSibling.tagName.toLowerCase() == 'img') {
              imgElm = elm.nextElementSibling.nextElementSibling;
            }
          }
          if (imgElm && useRow[name]) {
            imgElm.src = useRow[name];
          } else if (imgElm) {
            imgElm.classList.add('d-none');
          }
        } else if (elm.type == 'checkbox') {
          if (formData.id) {
            if (formData[name] == 1) {
              elm.checked = true;
            } else {
              elm.checked = false;
            }
          } else {
            //無資料時
            elm.checked = true;
          }
        } else if (elm.tagName.toLowerCase() == 'select') {
          if (elm.name == 'language_has_locale') {
            elm.children.forEach((option) => {
              option.selected = false;
              if (name == 'language_has_locale' && typeof formData[name] != 'undefined') {
                const parse1 = JSON.parse(formData[name]);
                if (option.value) {
                  const parse2 = JSON.parse(option.value);
                  if ((parse1.language_id == parse2.language_id) && (parse1.locale_id == parse2.locale_id)) {
                    option.selected = true;
                  }
                }
              }
            });
          }
        } else {
          let tmpValue = formData[name];
          if (name == 'sort' && tmpValue == 16777215) {
            elm.value = '';
          } else {
            if (name != 'password') {
              elm.value = formData[name] ? formData[name] : '';
            }
          }
        }
      });
    } else {
      reset();
    }
    //更新剩餘字數
    cFormElement.elements.forEach((elm) => {
      if (elm.maxLength && elm.maxLength > 0) {
        const _key = elm.name;
        if (elm.className.match(/ckeditor\-content/)) {
          if (props.editor && props.editor[_key] && props.editor[_key].current && props.editor[_key].current.editor) {
            props.editor[_key].current.editor.setData(props.defaultEditorContent[_key]);
          }

        } else {
          if (typeof props.remainderChange == 'function') {
            props.remainderChange(cFormElement.elements[_key]);
          }

        }
      }
    });
    if (typeof props.setFormResetState != 'undefined') {
      props.setFormResetState((value) => {
        var r = value + 1;
        return r;
      });
    }

    //圖片縮圖新增時隱藏 for reduces重構模式 start
    if (!useRow) {
      reset();
      cFormElement.elements.forEach((elm) => {
        if (elm.type == 'file') {
          let imgElm = null;
          if (elm.nextElementSibling.tagName.toLowerCase() == 'img') {
            imgElm = elm.nextElementSibling;
          }
          if (!imgElm) {
            if (elm.nextElementSibling.nextElementSibling.tagName.toLowerCase() == 'img') {
              imgElm = elm.nextElementSibling.nextElementSibling;
            }
          }
          if (imgElm) {
            imgElm.classList.add('d-none');
          }
        }
      });
    }
    //圖片縮圖新增時隱藏 for reduces重構模式 end
  }

  const buildForm = (url, tableCheck, row) => {
    //const cFormElement = cformRef.current;
    if (typeof props.setFormLists == 'function') {
      var listsData = {};
      listsData[tableCheck] = row;
      props.setFormLists(() => {
        return {
          ...props.formLists,
          ...listsData
        }
      });
      return;
    }

    const USE_TABLE = url
      .replace('/' + SYS_LANG + '/api/admin/', '')
      .replace(/\?.*$/, '')
      .replace(/\/\d+$/, '')
      .replace(/^([a-z]\_)/, '');
    let storeObj = {};
    storeObj[USE_TABLE] = row;
    setFormFieldsValue((formFieldsValue) => ({ ...formFieldsValue, ...storeObj }));

    if (row.id && typeof props.setFileRequire === 'function') {
      props.setFileRequire(false);
    }
    if (typeof row.language_id && typeof row.locale_id) {
      const language_has_locale = {
        locale_id: row.locale_id,
        language_id: row.language_id,
      };
      const toStringObj = {
        language_has_locale: JSON.stringify(language_has_locale),
      };
      storeObj[USE_TABLE] = { ...row, ...toStringObj };
      setFormFieldsValue((formFieldsValue) => ({ ...formFieldsValue, ...storeObj }));
    }
    if (typeof row == 'object') {
      const fields = Object.keys(row);
      fields.forEach((key) => {
        if (/^password/.test(key)) {
          return;
        }

        let value = row[key];
        if (key == 'sort' && value == 16777215) {
          value = '';
        }
        if (location.pathname.match(/\/add$/) && key == 'id') {
          return;
        }

      });
    }
  }


  //取得form的編輯資料
  const getUseRow = (url, loadingUse) => {
    var tableCheck = url.replace(/\/\d+$/, '').match(/\/\w+$/)[0].replace(/^\//, '');
    //特例
    tableCheck = tableCheck.replace(/s_banner/, 'banner');
    var params = {};
    if (accessoryTables.indexOf(tableCheck) > -1 && !localStorage.getItem('copyId')) {
      url += ('?table=' + props.table + '&table_id=' + method_or_id);
      url = url.replace(/\/\d+/, '');
    } else if (typeof props.classRelation == 'object') {
      //url += '?' + props.classRelation.self + '_id=' + method_or_id;
      url += '?' + 'self_id=' + method_or_id;
      url = url.replace(/\/\d+/, '');
    } else {
      if (tableCheck == MAIN_TABLE && !localStorage.getItem('copyId')) {
        url = `${basePath}/${SYS_LANG}/api/admin/${MAIN_TABLE}/${method_or_id}`;
        url = url.replace(/^\/{2,}/, '/');
      } else if (!localStorage.getItem('copyId')) {
        params.table_id = method_or_id;
      }
    }

    if (tableCheck.match(/_parent$/)) {
      //url+= '?parent_id=' + method_or_id;
      url = url.replace(/\/\d+$/, '');
      url += '?parent_id=' + method_or_id;
    }
    url = url.replace(/\/{2,}/, '/');
    if (props.store) {
      buildForm(url, props.store);
    } else {
      if (typeof loadingUse == 'undefined') {
        loadingUse = true;
      }
      const loadingBackgroundDom = document.getElementById('loading-background');
      if (loadingUse) {
        loadingBackgroundDom.classList.remove('d-none');
      }
      var cloneUrl = url;
      cloneUrl = ('/' + cloneUrl).replace(/\/{2,}/, '/');
      if (props.apiProps) {
        params.apiProps = props.apiProps;
      }
      if (props.table) {
        let _table = props.table;
        if (
          null == url.match(new RegExp('table=' + _table)) &&
          !params.table
        ) {
          params.table = _table;
        }
      }
      //特例
      cloneUrl = cloneUrl.replace(/s_banner/, 'banner');
      axios.get(cloneUrl, { params: params }).then((response) => {
        if (response.data.code == -2) {
          notify('error', t('admin-session-fail'), 3, () => {
            const path = `${basePath}/${SYS_LANG}/admin-login`.replace(/^\/{2,}/, '/');
            location.href = path;
          });
        }
        var row = response.data.data;
        if (localStorage.getItem('copyId')) {
          Object.keys(row).forEach((key) => {
            if (key == 'id') {
              delete row[key];
            }
            if (key.match(/banner|avater|image|phoro|file|route$/i)) {
              delete row[key];
            }
          });
        }
        let dispatchData = {};
        dispatchData[tableCheck] = row;
        dispatch({ type: FORM_ROWS, data: dispatchData });
        loadingBackgroundDom.classList.add('d-none');
        if (Array.isArray(row)) {
          return;
        }
        buildForm(url, tableCheck, row);
      }).catch((error) => {
        loadingBackgroundDom.classList.add('d-none');
        console.error(error);
      });

    }

  }

  const NC = 0;
  useEffect(() => {
    formBreadItemRename(t);
    const apiBasePath = location.pathname.replace(/\/admin/, '/api/admin');
    if (/^\d+$/.test(method_or_id)) {
      let href = props.href.replace(SYS_LANG, '');
      const url = basePath + ('/' + SYS_LANG + '/api/' + href).replace(/\/{2,}/, '/');
      getUseRow(url);
    } else {
      if (localStorage.getItem('copyId')) {
        const copyId = localStorage.getItem('copyId');
        const url = (basePath + apiBasePath.replace(/\/add$/, '/' + copyId)).replace(/\/{2,}/, '/');
        getUseRow(url, false);
      }
    }
    const navTo = location.pathname.replace(/\/(add|\d+)$/, '');
    Array.from(document.querySelectorAll('a.c-sidebar-nav-link')).forEach((elm) => {
      if (elm.getAttribute('href') == navTo) {
        elm.classList.add('c-active');
      }
    });
  }, [NC]);

  const cformRef = props.innerRef ? props.innerRef : useRef();
  const preLoading = (<Loading loading background="rgba(99,111,131,.5)" loaderColor="#321fdb" />);
  return (
    <Suspense fallback={preLoading}>
      {<CCardBody className="pb-0 mb-0">
        <CAlert color="dark" closeButton>* {t('form-tabs-alert')}</CAlert>
      </CCardBody>}
      <CForm innerRef={cformRef} onSubmit={handleSubmit(onSubmit)}>
        <CCardBody>
          {props.children}
        </CCardBody>
        <CCardFooter>
          <CButton size="sm" color="primary" type="submit" className="mr-1">
            <i className="fas fa-check mr-1"></i>
            <span>{t('form-submit')}</span>
          </CButton>
          {
            typeof props.resetInvisible == 'undefined' &&
            <CButton className="text-white" size="sm" color="warning" type="button" onClick={formReset}>
              <i className="fas fa-undo-alt mr-1"></i>
              <span>{t('form-reset')}</span>
            </CButton>
          }
        </CCardFooter>
      </CForm>
    </Suspense>
  );
}

const mapStateToProps = (state) => {
  return {
    dispatch: state.dispatch,
    formRows: state.formRows,
  };
};

//export default Documents;
export default connect(mapStateToProps)(Form);
