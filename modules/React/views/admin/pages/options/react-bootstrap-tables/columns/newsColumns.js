import { textFilter, dateFilter } from 'react-bootstrap-table2-filter';
import actions from './components/actions';

/* begin: 自定義清除搜尋 */
let display_name_filter;
let title_filter;
let publish_filter;
let created_at_filter;

const clickClearFilter = (e, state) => {
  e.preventDefault();
  if (Object.keys(state).length) {
    if (Object.keys(state.filters).length) {
      if (display_name_filter && typeof state.filters.display_name == 'object') {
        display_name_filter('');
      }
      if (title_filter && typeof state.filters.title == 'object') {
        title_filter('');
      }
      if (publish_filter && typeof state.filters.publish == 'object') {
        publish_filter('');
      }
      if (sort_filter && typeof state.filters.sort == 'object') {
        sort_filter({ comparator: "", number: "" });
      }
      if (created_at_filter && typeof state.filters.created_at == 'object') {
        created_at_filter({ comparator: "", date: "" });
      }
    }
  }
}
/* end */


const newsColumns = (t, smColumn) => {
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
      dataField: 'title',
      text: t('columns-title'),
      sort: true,
      editable: true,
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter model'),
        getFilter: (filter) => {
          title_filter = filter;
        },
      }),
    },
    {
      dataField: 'publish',
      text: t('columns-publish'),
      sort: true,
      editable: false,
      headerStyle: { width: '10rem' },
      filter: dateFilter({
        dateClassName: 'form-control-sm',
        comparatorClassName: "form-control-sm",
        defaultValue: { date: null, comparator: "" },
        getFilter: (filter) => {
          publish_filter = filter;
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
  newsColumns,
  clickClearFilter
};