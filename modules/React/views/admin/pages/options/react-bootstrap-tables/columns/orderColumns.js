//import React from 'react';
import { textFilter, selectFilter, dateFilter } from 'react-bootstrap-table2-filter';
import { Type } from 'react-bootstrap-table2-editor';
import actions from './components/actions';
import selectPayMethodOptions from './components/selectPayMethodOptions';

/* begin: 自定義清除搜尋 */
let display_name_filter;
let serial_filter;
let full_name_filter;
let email_filter;
let cellphone_filter;
let pay_method_filter;
let status_filter;
let member_full_name_filter;
let logistics_name_filter;
let created_at_filter;

const clickClearFilter = (e, state) => {
  e.preventDefault();
  if (Object.keys(state).length) {
    if (Object.keys(state.filters).length) {
      if (display_name_filter && typeof state.filters.display_name == 'object') {
        display_name_filter('');
      }
      if (serial_filter && typeof state.filters.serial == 'object') {
        serial_filter('');
      }
      if (full_name_filter && typeof state.filters.full_name == 'object') {
        full_name_filter('');
      }
      if (email_filter && typeof state.filters.email == 'object') {
        email_filter('');
      }
      if (cellphone_filter && typeof state.filters.cellphone == 'object') {
        cellphone_filter('');
      }

      if (pay_method_filter && typeof state.filters.pay_method == 'object') {
        pay_method_filter('');
      }
      if (status_filter && typeof state.filters.status == 'object') {
        pay_method_filter('');
      }
      if (member_full_name_filter && typeof state.filters.member_full_name == 'object') {
        member_full_name_filter('');
      }
      if (logistics_name_filter && typeof state.filters.logistics_name == 'object') {
        logistics_name_filter('');
      }
      if (created_at_filter && typeof state.filters.created_at == 'object') {
        created_at_filter({ comparator: "", date: "" });
      }
    }
  }
}
/* end */

const orderColumns = (t, smColumn) => {
  let fullColumns = [
    {
      dataField: 'id',
      text: 'id',
      hidden: true,
    },
    {
      dataField: 'member_id',
      text: t('columns-member_id'),
      hidden: true,
    },
    {
      dataField: 'logistics_id',
      text: t('columns-logistics_id'),
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
      text: t('columns-language_has_locale'),
      sort: true,
      editable: false,
      filter: textFilter({
        className: 'form-control-sm',
        //&#46 = . 保留字改用編碼替代
        placeholder: t('bootstrap-table-Enter language_has_locale'),
        getFilter: (filter) => {
          display_name_filter = filter;
        },
      }),
    },
    {
      dataField: 'serial',
      text: t('columns-order_serial'),
      sort: true,
      editable: false,
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter name'),
        getFilter: (filter) => {
          serial_filter = filter;
        },
      }),
    },
    {
      dataField: 'logistics_name',
      text: t('columns-shipping'),
      sort: true,
      editable: false,
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter name'),
        getFilter: (filter) => {
          logistics_name_filter = filter;
        },
      }),
    },
    {
      dataField: 'member_full_name',
      text: t('columns-member_full_name'),
      sort: true,
      editable: false,
      hidden: true,
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter name'),
        getFilter: (filter) => {
          member_full_name_filter = filter;
        },
      }),
    },

    {
      dataField: 'full_name',
      text: t('columns-order-full_name'),
      sort: true,
      editable: false,
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter name'),
        getFilter: (filter) => {
          full_name_filter = filter;
        },
      }),
    },
    {
      dataField: 'email',
      text: t('columns-order-email'),
      sort: false,
      editable: false,
      hidden: true,
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter name'),
        getFilter: (filter) => {
          email_filter = filter;
        },
      }),
    },
    {
      dataField: 'cellphone',
      text: t('columns-order-cellphone'),
      sort: true,
      editable: false,
      headerStyle: { width: '12rem' },
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter name'),
        getFilter: (filter) => {
          cellphone_filter = filter;
        },
      }),
    },
    {
      dataField: 'pay_method',
      text: t('columns-pay_method'),
      sort: true,
      /*editor: {
          type: Type.SELECT,
          options: selectPaymethodOptions(t),
      },*/
      classes: 'allow-cell-edit',
      headerClasses: 'sixPfiveRem',
    },
    {
      dataField: 'status',
      text: t('columns-order-status'),
      sort: true,
      classes: 'allow-cell-edit',
      headerClasses: 'sixPfiveRem',
    },

    {
      dataField: 'created_at',
      text: t('columns-created_at'),
      sort: true,
      editable: false,
      headerStyle: { width: '10rem' },
      filter: dateFilter({
        dateClassName: 'form-control-sm',
        comparatorClassName: "form-control-sm",
        defaultValue: { date: null, comparator: "" },
        getFilter: (filter) => {
          created_at_filter = filter;
        },
      }),
    },
    actions(t)
  ];
  return fullColumns;
};


export {
  orderColumns,
  clickClearFilter
};