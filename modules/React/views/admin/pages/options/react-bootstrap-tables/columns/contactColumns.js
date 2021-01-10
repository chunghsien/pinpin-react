//import React from 'react';
import { textFilter, selectFilter, dateFilter } from 'react-bootstrap-table2-filter';
import { Type } from 'react-bootstrap-table2-editor';
import actions from './components/actions';
import selectYesOrNoOptions from './components/selectYesOrNoOptions';

/* begin: 自定義清除搜尋 */
let display_name_filter;
let full_name_filter;
let email_filter;
let subject_filter;
let is_reply_filter;
let publish_filter;
let created_at_filter;

const clickClearFilter = (e, state) => {
  e.preventDefault();
  if (Object.keys(state).length) {
    if (Object.keys(state.filters).length) {
      if (full_name_filter && typeof state.filters.full_name == 'object') {
        full_name_filter('');
      }
      if (display_name_filter && typeof state.filters.display_name == 'object') {
        display_name_filter('');
      }
      if (email_filter && typeof state.filters.email == 'object') {
        email_filter('');
      }
      if (subject_filter && typeof state.filters.subject == 'object') {
        subject_filter('');
      }

      if (is_reply_filter && typeof state.filters.is_reply == 'object') {
        is_reply_filter('');
      }
      if (publish_filter && typeof state.filters.publish == 'object') {
        publish_filter({ comparator: "", date: "" });
      }

      if (created_at_filter && typeof state.filters.created_at == 'object') {
        created_at_filter({ comparator: "", date: "" });
      }
    }
  }
}
/* end */

const contactColumns = (t, smColumn) => {
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
      dataField: 'full_name',
      text: t('columns-full_name'),
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
      text: t('columns-email'),
      sort: true,
      editable: false,
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter name'),
        getFilter: (filter) => {
          email_filter = filter;
        },
      }),
    },
    {
      dataField: 'subject',
      text: t('columns-subject'),
      sort: true,
      editable: false,
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter name'),
        getFilter: (filter) => {
          subject_filter = filter;
        },
      }),
    },

    {
      dataField: 'is_reply',
      text: t('columns-is_reply'),
      sort: true,
      editor: {
        type: Type.CHECKBOX,
        value: '1:0',
      },
      classes: 'allow-cell-edit',
      headerClasses: 'sixPfiveRem',
      filter: selectFilter({
        className: 'form-control-sm',
        options: selectYesOrNoOptions(t),
        placeholder: t('isBoolOptionsDefault'),
        getFilter: (filter) => {
          is_reply_filter = filter;
        },
      }),
      headerStyle: { width: '10rem' },
      formatter: (cell, row) => {
        return (
          row.is_reply == 1
            ? t('is_type_1')
            : t('is_type_0')
        );
      }
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
  contactColumns,
  clickClearFilter
};