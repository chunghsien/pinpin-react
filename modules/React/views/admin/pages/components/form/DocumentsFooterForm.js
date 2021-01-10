import React, { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import {
  CCard, CSelect, CInput, CTabContent, CTabPane, CCardBody, CSwitch, CButton
} from '@coreui/react'
import formBreadItemRename from '../formBreadItemRename';
import axios from 'axios';
import { notify } from '../alertify';

const DocumentsFooterForm = (/*props*/) => {
  const { t } = useTranslation(['translation']);
  //const methods = useForm({ mode: 'all' });

  const layout_zones_id = location.pathname.match(/\d+$/)[0];

  const NC = 0;
  const [documents, setDocuments] = useState([]);
  const url = location.pathname.replace(/admin\/documents_\w+er/, 'api/admin/layout_zones_has_documents').replace(/\/\d+$/, '');
  
  const init = () => {
    axios.get(`${url}/${layout_zones_id}`).then((response) => {
      const data = response.data.data;
      setDocuments(data);
    });
  }
  
  useEffect(() => {
    Array.from(document.getElementsByClassName('c-active')).forEach((item) => {
      const navUri = location.pathname.replace(/\/\d+$/, '').replace(/\/add$/, '');
      var verfired = new RegExp(navUri + '$');
      if (item.tagName.toLowerCase() == 'li') {
        if (verfired.test(item.children[0].href) === false) {
          item.classList.remove('c-active');
          item.children[0].classList.remove('c-active');
        }
      } else {
        if (verfired.test(item.href) === false) {
          item.classList.remove('c-active');
        }
      }
    });
    formBreadItemRename(t);
    init();

  }, [NC]);

  const onClick = (e) => {
    e.preventDefault();
    const target = e.currentTarget;
    const documents_id = target.dataset.documents;
    var params = {};
    documents.forEach((item) => {
      if (item.documents_id == documents_id) {
        params = item;
        return;
      }
    });
    if (Object.keys(params).length) {
      //params.layout_zones_id = layout_zones_id;
      axios.post(`${url}/${layout_zones_id}`, { params: params }).then((response) => {
        const data = response.data.data;
        const code = response.data.code;
        const NOTIFY = response.data.notify.join("");
        if (code == 0) {
          notify('success', t(NOTIFY), 3);
        }else {
          notify('warning', t(NOTIFY), 3);
        }
        setDocuments(data);
      }).catch((error) => {
        const NOTIFY = error.response.data.notify.join("");
         notify('error', t(NOTIFY), 3);
      });
    }
  }

  const onChange = (e) => {
    //e.preventDefault();
    const target = e.currentTarget;
    const documents_id = target.dataset.documents;
    setDocuments((state) => {
      let res = [];
      state.forEach((item) => {
        const name = target.name;
        if (item.documents_id == documents_id) {
          let value = target.value;
          if (target.type == 'checkbox') {
            value = documents_id;
          }
          if (target.type == 'checkbox' && !target.checked) {
            item.layout_zones_id = null;
          }else {
            item.layout_zones_id = layout_zones_id;
          }
          item[name] = value;
        }
        res.push(item);
      });
      return res;
    });
  }

  //const formRef = useRef();
  return (
    <>
      <CTabContent>
        <CTabPane data-tab="default-form">
          <CCard className="tab-card">
            <CCardBody>
              {/*<input type="hidden" name="layout_zones_id" defaultValue={layout_zones_id} />*/}
              <table className="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col">{t('columns-documents_name')}</th>
                    <th scope="col">{t('columns-route')}</th>
                    <th scope="col">{t('view alias')}</th>
                    <th scope="col">{t('if show childs level')}</th>
                    <th scope="col">{t('use status')}</th>
                    <th scope="col">{t('grid-control')}</th>
                  </tr>
                </thead>
                <tbody>
                  {
                    documents.map((item, key) => {
                      return (
                        <tr key={key}>
                          <td>
                            {item.name}
                          </td>
                          <td>{item.route}</td>
                          <td>
                            {
                              item.layout_zones_id == layout_zones_id &&
                              <CInput onChange={onChange} data-documents={item.documents_id} type="text" name="alias" defaultValue={item.alias} maxLength="64" />
                            }
                          </td>
                          <td>
                            {
                              !(item.layout_zones_id == layout_zones_id) &&
                              <h4>-</h4>
                            }
                            {
                              (item.layout_zones_id == layout_zones_id) &&
                              <CSelect
                                onChange={onChange}
                                data-documents={item.documents_id}
                                name="is_show_childs"
                                defaultValue={item.is_show_childs}
                              >
                                <option value="0">{t('not use childs level')}</option>
                                <option value="1">{t('use childs level1')}</option>
                                <option value="2">{t('use childs level2')}</option>
                                <option value="3">{t('use childs level3')}</option>
                              </CSelect>
                            }
                          </td>
                          <td>
                            {
                              !item.layout_zones_id &&
                              <CSwitch
                                onChange={onChange}
                                name="documents_id"
                                data-documents={item.documents_id}
                                variant="opposite"
                                color="primary" />
                            }
                            {
                              item.layout_zones_id == layout_zones_id &&
                              <CSwitch
                                onChange={onChange}
                                name="documents_id"
                                data-documents={item.documents_id}
                                defaultChecked name="is_show"
                                variant="opposite"
                                color="primary"
                              />
                            }
                          </td>
                          <td>
                            <CButton
                              data-documents={item.documents_id}
                              size="sm"
                              color="info"
                              type="submit"
                              className="mr-1"
                              onClick={onClick}>
                              <i className="fas fa-check mr-1"></i>
                              <span>{t('form-submit')}</span>
                            </CButton>
                          </td>
                        </tr>
                      )
                    })
                  }
                </tbody>
              </table>
            </CCardBody>
          </CCard>
        </CTabPane>
      </CTabContent>
    </>
  );
}

export default DocumentsFooterForm;