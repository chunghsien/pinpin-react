import React from 'react';
import { Type } from 'react-bootstrap-table2-editor';
import { textFilter, numberFilter, selectFilter } from 'react-bootstrap-table2-filter';
import selectUseOptions from './components/selectUseOptions';
import sortFormatter from './components/sortFormatter';

/* begin: 自定義清除搜尋 */
let name_filter;
let manufacturer_filter;
let is_use_filter;
let sort_filter;
let price_filter;
let display_name_filter;

const shippingClickClearFilter = (e, state) => {
  e.preventDefault();
  if (Object.keys(state).length) {
    if (Object.keys(state.filters).length) {
      if (manufacturer_filter && typeof state.filters.type == 'object') {
        manufacturer_filter('');
      }
      if (name_filter && typeof state.filters.name == 'object') {
        name_filter('');
      }
      if (price_filter && typeof state.filters.price == 'object') {
        price_filter({ comparator: "", number: "" });
      }
      if (is_use_filter && typeof state.filters.is_use == 'object') {
        is_use_filter('');
      }
      if (sort_filter && typeof state.filters.sort == 'object') {
        sort_filter('');
      }
      if (display_name_filter && typeof state.filters.display_name == 'object') {
        display_name_filter('');
      }
    }
  }
}
/* end */


const shippingColumns = (t, smColumn) => {
  let fullColumns = [
    {
      dataField: 'id',
      text: 'id',
      hidden: true,
    },
    {
      dataField: 'language_id',
      text: t('columns-language_id'),
      hidden: true,
    },
    {
      dataField: 'locale_id',
      text: t('columns-locale_id'),
      hidden: true,
    },
    {
      dataField: 'display_name',
      text: t('Display name'),
      sort: true,
      editable: false,
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter language_has_locale'),
        getFilter: (filter) => {
          display_name_filter = filter;
        },
      }),
    },
    {
      dataField: 'manufacturer',
      text: t('columns-logistics-manufacturer'),
      sort: true,
      editable: false,
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter name'),
        getFilter: (filter) => {
          manufacturer_filter = filter;
        },
      }),
    },
    {
      dataField: 'name',
      text: t('columns-name'),
      sort: true,
      editable: false,
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter name'),
        getFilter: (filter) => {
          name_filter = filter;
        },
      }),
    },
    {
      dataField: 'price',
      text: t('columns-shipping-price'),
      headerClasses: 'eightPTwoFiveRem',
      classes: 'editable-cursor-alias',
      sort: true,
      editable: true,
      filter: numberFilter({
        numberClassName: "form-control-sm",
        placeholder: t('bootstrap-table-Enter shipping price'),
        comparatorClassName: "form-control-sm",
        getFilter: (filter) => {
          price_filter = filter;
        },
      }),
    },
    {
      dataField: 'is_use',
      text: t('admin-language:is use'),
      sort: true,
      editor: {
        type: Type.CHECKBOX,
        value: '1:0',
      },
      headerClasses: 'eightPTwoFiveRem',
      classes: 'allow-cell-edit',
      filter: selectFilter({
        className: 'form-control-sm',
        options: selectUseOptions(t),
        placeholder: t('isUseOptionsDefault'),
        getFilter: (filter) => {
          is_use_filter = filter;
        },
      }),
      headerStyle: { width: '10rem' },
      formatter: (cell, row) => {
        return (
          row.is_use == 1
            ? t('admin-language:is_use_1')
            : t('admin-language:is_use_0')
        );
      }
    },

    {
      dataField: 'sort',
      text: t('columns-sort'),
      headerClasses: 'eightPTwoFiveRem',
      classes: 'editable-cursor-alias',
      sort: true,
      editable: true,
      formatter: sortFormatter,
      editorRenderer: (editorProps, value/*, row, column, rowIndex, columnIndex*/) => {
        //const ref = React.createRef();
        return (<SortRenderer {...editorProps} value={parseInt(value)} />);
      },
      headerStyle: { width: '10rem' },
      filter: numberFilter({
        numberClassName: "form-control-sm",
        placeholder: t('bootstrap-table-Enter sort'),
        comparatorClassName: "form-control-sm",
        getFilter: (filter) => {
          sort_filter = filter;
        },
      }),
    },

  ];
  return fullColumns;
};


export {
  shippingColumns,
  shippingClickClearFilter
};