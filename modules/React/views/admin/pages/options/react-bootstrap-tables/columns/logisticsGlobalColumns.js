import { textFilter } from 'react-bootstrap-table2-filter';

/* begin: 自定義清除搜尋 */
let name_filter;
let value_filter;
let key_filter;
let display_name_filter;

const clickClearFilter = (e, state) => {
  e.preventDefault();
  if (Object.keys(state).length) {
    if (Object.keys(state.filters).length) {
      if (name_filter && typeof state.filters.name == 'object') {
        name_filter('');
      }
      if (key_filter && typeof state.filters.key == 'object') {
        key_filter('');
      }
      if (value_filter && typeof state.filters.value == 'object') {
        value_filter('');
      }
      if (display_name_filter && typeof state.filters.display_name == 'object') {
        display_name_filter('');
      }
    }
  }
}
/* end */


const logisticsGlobalColumns = (t, smColumn) => {
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
      dataField: 'key',
      text: t('columns-key'),
      sort: true,
      editable: false,
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter name'),
        getFilter: (filter) => {
          key_filter = filter;
        },
      }),
    },
    {
      dataField: 'value',
      text: t('columns-value'),
      sort: true,
      editable: pageConfig.admin.account === 'admin',
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter value'),
        getFilter: (filter) => {
          value_filter = filter;
        },
      }),
    },

  ];
  return fullColumns;
};


export {
  logisticsGlobalColumns,
  clickClearFilter
};