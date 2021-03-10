import React, { useEffect, useState, useRef } from 'react';
import { connect } from "react-redux";
import { useTranslation } from 'react-i18next';
import {
  CRow, CCol, CFormGroup, CLabel,
  CCard,
  CInvalidFeedback,
  CTabContent, CTabPane
} from '@coreui/react'

import { useForm } from "react-hook-form";
import Form from '../Form';

import Select from 'react-select';

const ClassRelationForm = (props) => {

  const { t } = useTranslation(['translation']);
  const methods = useForm({ mode: 'all' });
  const { register, errors } = methods;
  const matcher = location.pathname.match(/\/\d+$/);
  let href = props.href;
  if (location.pathname.match(/\/\d+$/)) {
    href = href.replace(/\/$/, '') + matcher[0];
  } else {
    href += 'add';
  }
  let self_id_value = '';
  if (matcher && matcher[0]) {
    self_id_value = matcher[0].replace(/^\//, '');
  }


  const [remaining, setRemaining] = useState({});

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

  const [reactSelectOptions, setReactSelectOptions] = useState({
    options: {},
    values: {}
  });

  const NC = 0;
  const { self, parent, bind } = props.classRelation;
  const self_id_name = self + '_id';
  const parent_id_name = parent + '_id';
  const { formRows } = props;
  const bindRow = formRows ? formRows[bind] : null;
  const [selectDefaultValue, setSelectDefaultValue] = useState([]);
  useEffect(() => {
    if (bindRow) {
      setReactSelectOptions({
        options: bindRow.options,
        values: bindRow.values
      });
      var _selectDefaultValue = [];
      bindRow.values[parent].forEach((item) => {
        _selectDefaultValue.push(item.value);
      });
      setSelectDefaultValue(_selectDefaultValue);
    }
  }, [bindRow, NC]);
  const formRef = useRef();

  const parentClassChange = (options) => {
    if(!Array.isArray(options)) {
      options = [options];
    }
    
    var value = [];
    if (options) {
      options.forEach((item) => {
        value.push(item.value);
      });
      setSelectDefaultValue(value);
      //formRef.current.elements[parent_id_name].value = value.join(',');
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

  }
  //console.log(reactSelectOptions.values);
  const isMulti = typeof props.isMulti != 'undefined' ? props.isMulti : true;
  return (
    <CTabContent>
      <CTabPane data-tab="class-releation-form">
        <CCard className="tab-card">
          <Form
            innerRef={formRef}
            href={href}
            griduse
            {...methods}
            {...props}
            remainderChange={remainderChange}
            setReactSelectOptions={setReactSelectOptions}
            selectOnChanges={[parentClassChange]}
          >
            <input type="hidden" name={self_id_name} ref={register()} value={self_id_value} />
            <CRow className="mt-2">
              <CCol>
                <CFormGroup>
                  <CLabel>{t('columns-name')}</CLabel>
                  <Select
                    styles={selectMenuStyles}
                    name={parent + '_container'}
                    placeholder={t("isUseOptionsDefault")}
                    isMulti={isMulti}
                    options={reactSelectOptions.options[parent]}
                    value={reactSelectOptions.values[parent]}
                    onChange={parentClassChange}

                  />
                  <input
                    className={errors[parent_id_name] && 'is-invalid'}
                    name={parent_id_name}
                    type="hidden"
                    ref={register()}
                    defaultValue={selectDefaultValue.join(",")}
                  />
                  <CInvalidFeedback>{
                    (
                      errors[parent_id_name] &&
                      errors[parent_id_name].type == 'required') && t('The input is an empty string')}</CInvalidFeedback>
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
    formRows: state.formRows
  };
};

export default connect(mapStateToProps)(ClassRelationForm);