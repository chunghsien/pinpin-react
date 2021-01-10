import { textFilter } from 'react-bootstrap-table2-filter';
import { Type } from 'react-bootstrap-table2-editor';

/* begin: 自定義清除搜尋 */
let general_seo_name_filter;
let general_seo_display_name_filter;
/* end */
const generalSeoClickClearFilter = (e, state) => {
  e.preventDefault();
  if (Object.keys(state).length) {
    if (Object.keys(state.filters).length) {
      if (general_seo_name_filter && typeof state.filters.name == 'object') {
        general_seo_name_filter('');
      }
      if (general_seo_display_name_filter && typeof state.filters.display_name == 'object') {
        general_seo_display_name_filter('');
      }
    }
  }
};

/* begin: 自定義清除搜尋 */
let site_info_name_filter;
let site_info_display_name_filter;
/* end */
const siteInfoClickClearFilter = (e, state) => {
  e.preventDefault();
  //console.log(state);
  if (Object.keys(state).length) {
    if (Object.keys(state.filters).length) {
      if (site_info_name_filter && typeof state.filters.name == 'object') {
        site_info_name_filter('');
      }
      if (site_info_display_name_filter && typeof state.filters.display_name == 'object') {
        site_info_display_name_filter('');
      }
    }
  }
};

const generalSeoColumns = (t, smColumn) => {
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
          general_seo_display_name_filter = filter;
        },
      }),
    },
    {
      dataField: 'name',
      text: t('columns-name'),
      sort: false,
      editable: false,
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter name'),
        getFilter: (filter) => {
          general_seo_name_filter = filter;
        },
      }),
    },
    {
      dataField: 'value',
      headerStyle: { width: '75%' },
      text: t('columns-value'),
      sort: false,
      editable: true,
      editor: {
        type: Type.TEXTAREA
      }
    },

  ];
  if (screen.width < 1201) {
    let returnColumns = fullColumns.filter((item, key) => {
      const lastKey = (fullColumns.length - 1);
      if (key === lastKey) {
        return item;
      } else if (smColumn && item.dataField == smColumn) {
        return item;
      }
      return false;
    });

    return returnColumns;
  } else {
    return fullColumns;
  }

};

const siteInfoColumns = (t, smColumn) => {
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
          site_info_display_name_filter = filter;
        },
      }),
    },
    {
      dataField: 'name',
      text: t('columns-name'),
      sort: false,
      editable: false,
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter name'),
        getFilter: (filter) => {
          site_info_name_filter = filter;
        },
      }),
    },
    {
      dataField: 'value',
      headerStyle: { width: '75%' },
      text: t('columns-value'),
      sort: false,
      editable: true,
    },

  ];
  return fullColumns;
};

export {
  generalSeoColumns,
  siteInfoColumns,
  generalSeoClickClearFilter,
  siteInfoClickClearFilter
};