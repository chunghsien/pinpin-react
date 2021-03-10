import React, { useState, useEffect, useRef } from 'react';
import { connect } from "react-redux";
import { useTranslation } from 'react-i18next';
import {
  CRow, CCol, CLabel,
  CCard,
  CInput, CSelect,
  CTabContent, CTabPane
} from '@coreui/react'
import { useForm } from "react-hook-form";
import Form from '../Form';
import { useParams } from "react-router-dom";

const ProductsSpecVolumeForm = (props) => {
  const { t } = useTranslation(['translation']);
  const methods = useForm({ mode: 'all' });
  const { register } = methods;
  let href = props.href;

  const table = props.table;

  const { method_or_id } = useParams();

  const [units, setUnits] = useState({
    dimensions_unit: [],
    weight_unit: [],
    volume_unit: [],
  });
  const NC = 0;
  const { formRows } = props;
  const products_spec_volume = (formRows && formRows.products_spec_volume) ? formRows.products_spec_volume : undefined;
  const basePath = window.pageConfig.basePath;
  const [formSelected, setFormSelected] = useState({
    dimensions_unit: '',
    weight_unit: '',
    volume_unit: ''
  });
  useEffect(() => {
    formRef.current.elements.forEach((dom) => {
      const name = dom.name;
      if (name == 'products_spec_id') {
        dom.value = method_or_id;
      }
    });
    var request = new XMLHttpRequest();
    const api = `${basePath}/${SYS_LANG}/api/admin/products_spec_volume/getUnit`.replace(/\/{2,}/, '/');
    request.open('GET', api, false);
    request.send();
    const unitContainer = JSON.parse(request.responseText).data;
    setUnits(unitContainer);
    if (products_spec_volume) {
      setFormSelected({
        dimensions_unit: products_spec_volume.dimensions_unit ? products_spec_volume.dimensions_unit : '',
        weight_unit: products_spec_volume.weight_unit ? products_spec_volume.weight_unit : '',
        volume_unit: products_spec_volume.volume_unit ? products_spec_volume.volume_uni : ''
      });
    }

  }, [products_spec_volume, NC]);
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
    <>
      <CTabContent>
        <CTabPane data-tab="products-spec-volume-form">
          <CCard className="tab-card">
            <Form
              setFormSelected={setFormSelected}
              innerRef={formRef}
              href={href}
              griduse
              {...methods}
              table={table}
            >
              <input type="hidden" name="products_spec_id" ref={register()} />
              <input
                type="hidden"
                name="id"
                ref={register()}
                defaultValue={products_spec_volume && products_spec_volume.id}
              />
              <CRow>
                <CCol md="3" sm="12" className="mt-2">
                  <CLabel>{t('columns-width')}</CLabel>
                  <CInput
                    type="number"
                    name="width"
                    innerRef={register()}
                    defaultValue={products_spec_volume && products_spec_volume.width}
                  />
                </CCol>
                <CCol md="3" sm="12" className="mt-2">
                  <CLabel>{t('columns-height')}</CLabel>
                  <CInput
                    type="number"
                    name="height"
                    innerRef={register()}
                    defaultValue={products_spec_volume && products_spec_volume.height}
                  />
                </CCol>
                <CCol md="3" sm="12" className="mt-2">
                  <CLabel>{t('columns-distance')}</CLabel>
                  <CInput
                    type="number"
                    name="distance"
                    innerRef={register()}
                    defaultValue={products_spec_volume && products_spec_volume.distance}
                  />
                </CCol>
                <CCol md="3" sm="12" className="mt-2">
                  <CLabel>{t('columns-dimensions_unit')}</CLabel>
                  <CSelect
                    innerRef={register()}
                    name="dimensions_unit"
                    custom
                    value={formSelected.dimensions_unit}
                    onChange={selectOnChange}
                  >
                    <option value="">{t("no options")}</option>
                    {
                      units.dimensions_unit.map((item, key) => {
                        return (
                          <option key={'dimensions_unit_' + key} value={item}>
                            {t(item)}
                          </option>
                        );
                      })
                    }
                  </CSelect>
                </CCol>
              </CRow>
              <CRow>
                <CCol md="6" sm="12" className="mt-2">
                  <CLabel>{t('columns-weight')}</CLabel>
                  <CInput
                    type="number"
                    name="weight"
                    innerRef={register()}
                    defaultValue={products_spec_volume && products_spec_volume.weight}
                  />
                </CCol>
                <CCol md="6" sm="12" className="mt-2">
                  <CLabel>{t('columns-weight_unit')}</CLabel>
                  <CSelect
                    innerRef={register()}
                    name="weight_unit"
                    custom
                    value={formSelected.weight_unit}
                    onChange={selectOnChange}
                  >
                    <option value="">{t("no options")}</option>
                    {
                      units.weight_unit.map((item, key) => {
                        return (
                          <option key={'volume_unit' + key} value={item}>
                            {t(item)}
                          </option>
                        );
                      })
                    }
                  </CSelect>
                </CCol>

              </CRow>
              <CRow>
                <CCol md="6" sm="12" className="mt-2">
                  <CLabel>{t('columns-volume')}</CLabel>
                  <CInput
                    type="number"
                    name="volume"
                    innerRef={register()}
                    defaultValue={products_spec_volume && products_spec_volume.volume}
                  />
                </CCol>
                <CCol md="6" sm="12" className="mt-2">
                  <CLabel>{t('columns-volume_unit')}</CLabel>
                  <CSelect
                    innerRef={register()}
                    name="volume_unit"
                    custom
                    value={formSelected.volume_unit}
                    onChange={selectOnChange}
                  >
                    <option value="">{t("no options")}</option>
                    {
                      units.volume_unit.map((item, key) => {
                        return (
                          <option key={'volume_unit' + key} value={item}>
                            {t(item)}
                          </option>
                        );
                      })
                    }
                  </CSelect>
                </CCol>

              </CRow>
            </Form>
          </CCard>
        </CTabPane>
      </CTabContent>
    </>
  );
}

const mapStateToProps = (state) => {
  return {
    //dispatch: state.dispatch,
    formRows: state.formRows
  };
};

export default connect(mapStateToProps)(ProductsSpecVolumeForm);