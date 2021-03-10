import { Type } from 'react-bootstrap-table2-editor';
import { textFilter, dateFilter, selectFilter, numberFilter } from 'react-bootstrap-table2-filter';
import actions from './components/actions';
import selectYesOrNoOptions from './components/selectYesOrNoOptions';
import selectUrlTargetOptions from './components/selectUrlTargetOptions';
import sortFormatter from './components/sortFormatter';
import SortRenderer from './components/SortRenderer';


/* begin: 自定義清除搜尋 */
let display_name_filter;
let title_filter;
let subtitle_filter;
let url_filter;
let target_filter;
let is_show_filter;
let sort_filter;
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
      if (subtitle_filter && typeof state.filters.subtitle == 'object') {
        subtitle_filter('');
      }
      if (url_filter && typeof state.filters.url == 'object') {
        url_filter('');
      }
      if (target_filter && typeof state.filters.target == 'object') {
        target_filter('');
      }
      if (is_show_filter && typeof state.filters.is_show == 'object') {
        is_show_filter('');
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


const sBannerColumns = (t, smColumn) => {
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
      headerStyle: { width: '10rem' },
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
      editable: false,
      headerStyle: { width: '10rem' },
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter name'),
        getFilter: (filter) => {
          title_filter = filter;
        },
      }),
    },
    {
      dataField: 'subtitle',
      text: t('columns-subtitle'),
      sort: true,
      editable: false,
      hidden: true,
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter name'),
        getFilter: (filter) => {
          subtitle_filter = filter;
        },
      }),
    },
    {
      dataField: 'url',
      text: t('columns-url'),
      sort: true,
      editable: false,
      filter: textFilter({
        className: 'form-control-sm',
        placeholder: t('bootstrap-table-Enter name'),
        getFilter: (filter) => {
          url_filter = filter;
        },
      }),
    },
    {
      dataField: 'target',
      text: t('columns-target'),
      sort: true,
      edit:false,
      classes: 'allow-cell-edit',
      headerClasses: 'sixPfiveRem',
      filter: selectFilter({
        className: 'form-control-sm',
        options: selectUrlTargetOptions(t),
        placeholder: t('isBoolOptionsDefault'),
        getFilter: (filter) => {
          target_filter = filter;
        },
      }),
    },

    {
      dataField: 'is_show',
      text: t('columns-is_show'),
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
          is_show_filter = filter;
        },
      }),
      headerStyle: { width: '10rem' },
      formatter: (cell, row) => {
        return (
          row.is_show == 1
            ? t('is_type_1')
            : t('is_type_0')
        );
      }
    },
    {
      dataField: 'sort',
      text: t('columns-sort'),
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
  sBannerColumns,
  clickClearFilter
};