import React, { useState, useRef, useEffect } from 'react';
import { connect } from "react-redux";
import { useTranslation } from 'react-i18next';
import {
  CRow, CCol, CFormGroup, CLabel,
  CCard,
  CSelect, CInput, CInputFile,
  CInputGroup, CInputGroupAppend, CInputGroupText,
  CInvalidFeedback,
  CTabContent, CTabPane

} from '@coreui/react'

import { useForm } from "react-hook-form";
import Form from '../Form';

const ManufacturesForm = (props) => {

  const { t } = useTranslation(['translation']);
  const methods = useForm({ mode: 'all' });
  const { register, errors } = methods;
  let href = props.href;
  const matcher = location.pathname.match(/\/\d+$/);
  if (location.pathname.match(/\/\d+$/)) {
    href = href.replace(/\/$/, '') + matcher[0];
  } else {
    href += '/add';
    href = href.replace(/\/\//, '/');
  }
  const [remaining, setRemaining] = useState({});
  const [maxLength, setMaxLength] = useState({});
  const [mediaState, setMediaState] = useState({});
  const [fileRequire, setFileRequire] = useState(true);

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
    const file = e.target.files[0];
    reader.readAsDataURL(file);
    reader.onload = () => {
      let imgEle = null;
      if (dom.nextElementSibling.tagName.toLowerCase() == 'img') {
        imgEle = dom.nextElementSibling;
      }
      if (!imgEle) {
        if (dom.nextElementSibling.nextElementSibling.tagName.toLowerCase() == 'img') {
          imgEle = dom.nextElementSibling.nextElementSibling;
        }
      }
      if (imgEle) {
        imgEle.src = reader.result;
        imgEle.classList.remove('d-none');
      }
    }
  }


  const NC = 0;
  const { formRows } = props;
  const manufactures = formRows ? formRows.manufactures : undefined;
  const [formSelected, setFormSelected] = useState({});
  useEffect(() => {
    formRef.current.elements.forEach((dom) => {
      const name = dom.name;
      const _maxLength = dom.maxLength;
      if (_maxLength && _maxLength > 0) {
        let obj = {};
        obj[name] = _maxLength;
        //使用字數初始化
        remainderChange(dom);
        setMaxLength((maxLength) => ({ ...maxLength, ...obj }));
      }
    });
    if (manufactures) {
      setFormSelected({
        language_has_locale: manufactures.language_has_locale
      });

    }
  }, [manufactures, NC]);
  const selectOnChange = (e) => {
    var elm = e.currentTarget;
    e.preventDefault();
    setFormSelected((selectedState) => {
      let responseState = selectedState;
      let name = elm.name;
      let value = elm.value;
      responseState[name] = value;
      return { ...selectedState, ...responseState };
    });
  }

  const formRef = useRef();

  return (
    <CTabContent>
      <CTabPane data-tab="default-form">
        <CCard className="tab-card">
          <Form
            innerRef={formRef}
            href={href}
            griduse {...methods}
            remainderChange={remainderChange}
            setFileRequire={setFileRequire}
            {...props}
          >
            <input type="hidden" name="id" ref={register()} defaultValue={manufactures ? manufactures.id : undefined} />
            <CRow>
              <CCol md="6" sm="12" className="mt-2">
                <CFormGroup>
                  <CLabel>{t('columns-language_has_locale')}</CLabel>
                  <CSelect
                    custom
                    name="language_has_locale"
                    custom innerRef={register()}
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
              <CCol md="6" sm="12" className="mt-2">
                <CFormGroup>
                  <CLabel>{t('columns-name')}</CLabel>
                  <CInputGroup className={errors.name && 'is-invalid'}>
                    <CInput
                      invalid={errors.name ? true : false}
                      name="name"
                      maxLength="128"
                      onChange={remainderChange}
                      innerRef={register({ required: true })}
                      defaultValue={manufactures ? manufactures.name : undefined}
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
                  <CLabel>{t('columns-image')}</CLabel>
                  <CInputFile
                    name="image"
                    innerRef={register({ required: fileRequire })}
                    invalid={errors.image ? true : false}
                    onChange={singleFileOnChange}
                    accept="image/*"
                  />
                  <CInvalidFeedback>{(errors.image && errors.image.type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
                  <img
                    id="assets-image-preview"
                    className={'mt-2 img-fluid form-thumbnail ' + (manufactures && manufactures.image) ? '' : 'd-none'}
                    src={manufactures ? manufactures.image : undefined}
                  />
                </CFormGroup>
              </CCol>
              {/*
              <CCol md="6" sm="12">
                <CFormGroup>
                  <CLabel>{t('columns-sort')}</CLabel>
                  <CInput
                    invalid={errors.sort ? true : false}
                    name="sort"
                    type="number"
                    innerRef={register({ min: 0, max: 16777215 })}
                    defaultValue={manufactures ? manufactures.sort : undefined}
                  />
                  <CInvalidFeedback>{errors.sort && t('The input is not between \'%min%\' and \'%max%\', inclusively', { min: 0, max: 16777215 })}</CInvalidFeedback>
                </CFormGroup>
              </CCol>
              */}
            </CRow>
          </Form>
        </CCard>
      </CTabPane>
    </CTabContent>
  );
}
const mapStateToProps = (state) => {
  return {
    formRows: state.formRows,
  };
};

export default connect(mapStateToProps)(ManufacturesForm);
