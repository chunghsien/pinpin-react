import { textFilter, dateFilter } from 'react-bootstrap-table2-filter';
import actions from './components/actions';

/* begin: 自定義清除搜尋 */
let full_name_filter;
let cellphone_filter;
let email_filter;
let zip_filter;
let county_filter;
let district_filter;
let address_filter;
let created_at_filter;
let display_name_filter;

const clickClearFilter = (e, state) => {
  //console.log(state);
  e.preventDefault();
  if (Object.keys(state).length) {
    if (Object.keys(state.filters).length) {
      if (full_name_filter && typeof state.filters.full_name != 'undefined') {
        full_name_filter('');
      }
      if (cellphone_filter && typeof state.filters.cellphone != 'undefined') {
        cellphone_filter('');
      }
      if (email_filter && typeof state.filters.email != 'undefined') {
        email_filter('');
      }
      if (zip_filter && typeof state.filters.zip != 'undefined') {
        zip_filter('');
      }
      if (county_filter && typeof state.filters.county != 'undefined') {
        county_filter('');
      }
      if (district_filter && typeof state.filters.district != 'undefined') {
        district_filter('');
      }
      if (address_filter && typeof state.filters.address != 'undefined') {
        address_filter('');
      }
      if (created_at_filter && typeof state.filters.created_at != 'undefined') {
        created_at_filter({ comparator: "", date: "" });
      }
      if (display_name_filter && typeof state.filters.display_name != 'undefined') {
        display_name_filter('');
      }
    }
  }
}
/* end */


const memberListColumns = (t, smColumn) => {

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
      headerStyle: { width: '9rem' },
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
      dataField: 'email',
      text: t('Email (account)'),
      sort: true,
      editable: false,
      headerStyle: { width: '20rem' },
      filter: textFilter({
        style: { width: '18rem' },
        className: 'form-control-sm',
        //&#46 = . 保留字改用編碼替代
        placeholder: t('bootstrap-table-Enter email'),
        getFilter: (filter) => {
          email_filter = filter;
        },
      }),
    },
    {
      dataField: 'full_name',
      text: t('columns-full_name'),
      sort: true,
      editable: false,
      headerStyle: { width: '9rem' },
      filter: textFilter({
        className: 'form-control-sm',
        //&#46 = . 保留字改用編碼替代
        placeholder: t('bootstrap-table-Enter full name'),
        getFilter: (filter) => {
          full_name_filter = filter;
        },
      }),
    },
    {
      dataField: 'cellphone',
      text: t('columns-cellphone'),
      sort: true,
      editable: false,
      headerStyle: { width: '10rem' },
      filter: textFilter({
        className: 'form-control-sm',
        //&#46 = . 保留字改用編碼替代
        placeholder: t('bootstrap-table-Enter cellphone'),
        getFilter: (filter) => {
          cellphone_filter = filter;
        },
      }),
    },
    {
      dataField: 'zip',
      text: t('columns-zip'),
      sort: true,
      editable: false,
      headerStyle: { width: '9rem' },
      filter: textFilter({
        className: 'form-control-sm',
        //&#46 = . 保留字改用編碼替代
        placeholder: t('bootstrap-table-Enter zip'),
        getFilter: (filter) => {
          zip_filter = filter;
        },
      }),
    },
    {
      dataField: 'county',
      text: t('columns-county'),
      sort: true,
      editable: false,
      headerStyle: { width: '7.5rem' },
      filter: textFilter({
        className: 'form-control-sm',
        //&#46 = . 保留字改用編碼替代
        placeholder: t('bootstrap-table-Enter county'),
        getFilter: (filter) => {
          county_filter = filter;
        },
      }),
    },
    {
      dataField: 'district',
      text: t('columns-district'),
      sort: true,
      editable: false,
      headerStyle: { width: '9rem' },
      filter: textFilter({
        className: 'form-control-sm',
        //&#46 = . 保留字改用編碼替代
        placeholder: t('bootstrap-table-Enter district'),
        getFilter: (filter) => {
          district_filter = filter;
        },
      }),
    },
    {
      dataField: 'address',
      text: t('columns-address'),
      sort: true,
      editable: false,
      headerStyle: { width: '25rem' },
      filter: textFilter({
        style: { width: '23rem' },
        className: 'form-control-sm',
        //&#46 = . 保留字改用編碼替代
        placeholder: t('bootstrap-table-Enter address'),
        getFilter: (filter) => {
          address_filter = filter;
        },
      }),
    },

    {
      dataField: 'created_at',
      text: t('columns-created_at'),
      sort: true,
      editable: false,
      headerStyle: { width: '9rem' },
      filter: dateFilter({
        dateClassName: 'form-control-sm',
        comparatorClassName: "form-control-sm",
        defaultValue: { date: null, comparator: "" },
        getFilter: (filter) => {
          created_at_filter = filter;
        },
      }),
    },
    actions(t, false)
  ];
  return fullColumns;
};


export {
  memberListColumns,
  clickClearFilter
};