import React, { useState, useRef, useEffect } from 'react';
import { connect } from "react-redux";
import { useTranslation } from 'react-i18next';
import {
  CRow, CCol, CFormGroup, CLabel,
  CCard,
  CTabContent, CTabPane
} from '@coreui/react'

import { useForm } from "react-hook-form";
import Form from '../Form';

import Select from 'react-select';
import axios from 'axios';

const BannerHasDocumentsForm = (props) => {
  const basePath = window.pageConfig.basePath;
  const { t } = useTranslation(['translation']);
  const methods = useForm({ mode: 'all' });
  const { register } = methods;
  const matcher = location.pathname.match(/\/\d+$/);
  let href = props.href;
  if (location.pathname.match(/\/\d+$/)) {
    href = href.replace(/\/$/, '') + matcher[0];
  }

  let id_value = '';
  if (matcher && matcher[0]) {
    id_value = matcher[0].replace(/^\//, '');
  }


  const [reactSelectOptions, setReactSelectOptions] = useState({
    options: {},
    values: {}
  });

  const selectMenuStyles = {
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
    }
  };

  const formRef = useRef();

  const [bannerImages, setBannerImages] = useState();

  const getBannerImage = (options) => {
    let uri = location.pathname.replace(/\d+/, '').replace(/\/$/, '').replace('admin/documents', 'api/admin/banner');
    let ids = [];
    if (options && options.length && options.banner) {
      options.forEach((item) => {
        ids.push(item.value);
      });
      axios.get(uri, { params: { ids: ids, method: props.bannerType } }).then((response) => {
        const data = response.data.data;
        setBannerImages(data);
      });
    } else {
      setBannerImages([]);
    }
  }

  const NC = 0;
  const { formRows } = props;
  const bannerHasDocuments = (formRows && formRows.banner_has_documents) ? formRows.banner_has_documents : null;
  useEffect(() => {
    //setReactSelectOptions
    if (bannerHasDocuments) {
      setReactSelectOptions({
        options: bannerHasDocuments.options,
        values: bannerHasDocuments.values
      });
      getBannerImage(bannerHasDocuments.values.banner);
    }

  }, [bannerHasDocuments, NC]);

  const moreToMoreChange = (options) => {
    let value = [];
    if (options) {
      options.forEach((item) => {
        value.push(item.value);
      });
      formRef.current.elements[parent_id_name].value = value.join(',');
    } else {
      formRef.current.elements[parent_id_name].value = '';
    }
    setReactSelectOptions((reactSelectOptions) => {
      let values = {};
      values[parent] = options;
      return {
        ...reactSelectOptions,
        values: values
      }
    });
    if (reactSelectOptions.options[parent]) {
      getBannerImage(options);
    }
  }


  const isMulti = props.isMulti ? props.isMulti : true;
  const { self, parent } = props.classRelation;
  const self_id_name = self + '_id';
  const parent_id_name = parent + '_id';

  let selectOptionProps = {};
  if (reactSelectOptions.values && reactSelectOptions.values[parent] && reactSelectOptions.values[parent].length) {
    selectOptionProps.value = reactSelectOptions.values[parent];
  }
  return (
    <CTabContent>
      <CTabPane data-tab={props.tab}>
        <CCard className="tab-card">
          <Form
            innerRef={formRef}
            href={href}
            griduse
            {...methods}
            {...props}
            apiProps={{ type: props.bannerType }}
            getBannerImage={getBannerImage}
            setReactSelectOptions={setReactSelectOptions}
          >
            <input type="hidden" name={self_id_name} ref={register()} value={id_value} />
            <CRow className="mt-2">
              <CCol>
                <CFormGroup>
                  <CLabel>{t('columns-' + parent_id_name)}</CLabel>
                  <Select
                    styles={selectMenuStyles}
                    name={parent + '_container'}
                    placeholder={t("isUseOptionsDefault")}
                    isMulti={isMulti}
                    options={reactSelectOptions.options[parent]}
                    onChange={moreToMoreChange}
                    value={reactSelectOptions.values[parent]}
                  />
                  <input
                    name={parent_id_name}
                    type="hidden"
                    ref={register()}
                  />
                </CFormGroup>
              </CCol>
            </CRow>
            {
              (bannerImages && bannerImages.map) &&
              bannerImages.map((item) => {
                const itemImage = (basePath + item.image).replace(/^\/{2,}/, '/');
                const itemBgImage = (basePath + item.bg_image).replace(/^\/{2,}/, '/');
                return (
                  <CRow className="mt-2" key={item.id}>
                    {
                      (item.image && item.bg_image) &&
                      <>
                        <CCol md="6" sm="12"><img className="img-fluid" src={itemImage} /></CCol>
                        <CCol md="6" sm="12"><img className="img-fluid" src={itemBgImage} /></CCol>
                      </>
                    }
                    {
                      (item.image && !item.bg_image) &&
                      <CCol><img className="img-fluid" src={item.image} /></CCol>
                    }
                    {
                      (!item.image && item.bg_image) &&
                      <CCol><img className="img-fluid" src={item.bg_image} /></CCol>
                    }
                  </CRow>
                );
              })
            }
          </Form>
        </CCard>
      </CTabPane>
    </CTabContent>
  );
}
const mapStateToProps = (state) => {
  return {
    dispatch: state.dispatch,
    formRows: state.formRows,
  };
};

export default connect(mapStateToProps)(BannerHasDocumentsForm);