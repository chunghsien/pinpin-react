import { textFilter, dateFilter, numberFilter, selectFilter } from 'react-bootstrap-table2-filter';
import { Type } from 'react-bootstrap-table2-editor';
import selectYesOrNoOptions from './components/selectYesOrNoOptions';
import actions from './components/actions';


/* begin: 自定義清除搜尋 */
let display_name_filter;
let name_filter;
let code_filter;
let target_value_filter;
let use_value_filter;
let is_use_filter;
let start_filter;
let expiration_filter;
let created_at_filter;

const clickClearFilter = (e, state) => {
  e.preventDefault();
  if (Object.keys(state).length) {
    if (Object.keys(state.filters).length) {
      if (display_name_filter && typeof state.filters.display_name == 'object') {
        display_name_filter('');
      }
      if (name_filter && typeof state.filters.name == 'object') {
        name_filter('');
      }
      if (code_filter && typeof state.filters.code == 'object') {
        code_filter('');
      }
      if (target_value_filter && typeof state.filters.target_value == 'object') {
        target_value_filter('');
      }
      if (use_value_filter && typeof state.filters.use_value == 'object') {
        use_value_filter('');
      }
      if (is_use_filter && typeof state.filters.is_use == 'object') {
        use_value_filter('');
      }
      if (start_filter && typeof state.filters.start == 'object') {
        start_filter('');
      }
      if (expiration_filter && typeof state.filters.expiration == 'object') {
        expiration_filter('');
      }
      if (created_at_filter && typeof state.filters.created_at == 'object') {
        created_at_filter('');
      }
    }
  }
}
/* end */


const couponColumns = (t, smColumn) => {
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
      dataField: 'code',
      text: t('columns-code'),
      sort: true,
      editable: false,
      filter: textFilter({
        className: 'form-control-sm',
        //&#46 = . 保留字改用編碼替代
        placeholder: t('bootstrap-table-Enter name'),
        getFilter: (filter) => {
          code_filter = filter;
        },
      }),
    },
    {
      dataField: 'name',
      text: t('columns-name'),
      sort: true,
      editable: true,
      filter: textFilter({
        className: 'form-control-sm',
        //&#46 = . 保留字改用編碼替代
        placeholder: t('bootstrap-table-Enter name'),
        getFilter: (filter) => {
          name_filter = filter;
        },
      }),
    },
    {
      dataField: 'target_value',
      text: t('columns-target_value'),
      headerClasses: 'eightPTwoFiveRem',
      classes: 'editable-cursor-alias',
      sort: true,
      editable: true,
      filter: numberFilter({
        numberClassName: "form-control-sm",
        placeholder: t('bootstrap-table-Enter price'),
        comparatorClassName: "form-control-sm",
        getFilter: (filter) => {
          target_value_filter = filter;
        },
      }),
    },
    {
      dataField: 'use_value',
      text: t('columns-use_value'),
      headerClasses: 'eightPTwoFiveRem',
      classes: 'editable-cursor-alias',
      sort: true,
      editable: true,
      filter: numberFilter({
        numberClassName: "form-control-sm",
        placeholder: t('bootstrap-table-Enter price'),
        comparatorClassName: "form-control-sm",
        getFilter: (filter) => {
          use_value_filter = filter;
        },
      }),
    },
    {
      dataField: 'is_use',
      text: t('columns-is_use'),
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
          is_use_filter = filter;
        },
      }),
      headerStyle: { width: '10rem' },
      formatter: (cell, row) => {
        return (
          row.is_use == 1
            ? t('is_type_1')
            : t('is_type_0')
        );
      }
    },

    {
      dataField: 'start',
      text: t('columns-start'),
      sort: true,
      editable: true,
      headerStyle: { width: '10rem' },
      filter: dateFilter({
        dateClassName: 'form-control-sm',
        comparatorClassName: "form-control-sm",
        defaultValue: { date: null, comparator: "" },
        getFilter: (filter) => {
          start_filter = filter;
        },
      }),
    },

    {
      dataField: 'expiration',
      text: t('columns-expiration'),
      sort: true,
      editable: true,
      headerStyle: { width: '10rem' },
      filter: dateFilter({
        dateClassName: 'form-control-sm',
        comparatorClassName: "form-control-sm",
        defaultValue: { date: null, comparator: "" },
        getFilter: (filter) => {
          expiration_filter = filter;
        },
      }),
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
  couponColumns,
  clickClearFilter
};