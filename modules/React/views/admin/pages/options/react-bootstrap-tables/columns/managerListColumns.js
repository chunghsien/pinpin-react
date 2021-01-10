import { textFilter, dateFilter } from 'react-bootstrap-table2-filter';
import actions from './components/actions';

/* begin: 自定義清除搜尋 */
let display_name_filter;
let role_name_filter;
let account_filter;
let created_at_filter;

const clickClearFilter = (e, state) => {
  e.preventDefault();
  if (Object.keys(state).length) {
    if (Object.keys(state.filters).length) {
      if (display_name_filter && typeof state.filters.display_name == 'object') {
        display_name_filter('');
      }
      if (role_name_filter && typeof state.filters.role_name == 'object') {
        role_name_filter('');
      }
      if (account_filter && typeof state.filters.account == 'object') {
        account_filter('');
      }
      if (created_at_filter && typeof state.filters.created_at == 'object') {
        created_at_filter({ comparator: "", date: "" });
      }
    }
  }
}
/* end */


const managerListColumns = (t, smColumn) => {
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
      hidden: true,
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
      dataField: 'role_name',
      text: '群組名稱',
      sort: false,
      editable: false,
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: '請輸入群組名稱',
        getFilter: (filter) => {
          role_name_filter = filter;
        },
      }),
    },

    {
      dataField: 'account',
      text: '帳號',
      sort: true,
      editable: true,
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: '請輸入帳號',
        getFilter: (filter) => {
          account_filter = filter;
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
  managerListColumns,
  clickClearFilter
};