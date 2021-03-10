import { textFilter, dateFilter } from 'react-bootstrap-table2-filter';
import actions from './components/actions';

/* begin: 自定義清除搜尋 */
let name_filter;
let extra_name_filter;
let triple_name_filter;
let display_name_filter;
let created_at_filter;

const clickClearFilter = (e, state) => {
  e.preventDefault();
  if (Object.keys(state).length) {
    if (Object.keys(state.filters).length) {
      if (name_filter && typeof state.filters.name == 'object') {
        name_filter('');
      }
      if (extra_name_filter && typeof state.filters.extra_name == 'object') {
        extra_name_filter('');
      }
      if (triple_name_filter && typeof state.filters.triple_name == 'object') {
        extra_name_filter('');
      }

      if (display_name_filter && typeof state.filters.display_name == 'object') {
        display_name_filter('');
      }

      if (created_at_filter && typeof state.filters.created_at == 'object') {
        created_at_filter({ comparator: "", date: "" });
      }
    }
  }
}
/* end */


const productsSpecAttrsColumns = (t, smColumn) => {
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
      dataField: 'name',
      text: t('columns-name'),
      sort: true,
      editable: true,
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter name'),
        getFilter: (filter) => {
          name_filter = filter;
        },
      }),
    },
    {
      dataField: 'extra_name',
      text: t('columns-extra-name'),
      sort: true,
      editable: true,
      hidden: true,
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter name'),
        getFilter: (filter) => {
          extra_name_filter = filter;
        },
      }),
    },
    {
      dataField: 'triple_name',
      text: t('columns-triple-name'),
      sort: true,
      editable: true,
      hidden: true,
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter name'),
        getFilter: (filter) => {
          triple_name_filter = filter;
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
  productsSpecAttrsColumns,
  clickClearFilter
};