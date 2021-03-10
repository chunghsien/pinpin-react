import React from 'react';
import selectStockStatusOptions from './components/selectStockStatusOptions';
import { textFilter, numberFilter, dateFilter, selectFilter } from 'react-bootstrap-table2-filter';
import actions from './components/actions';
import sortFormatter from './components/sortFormatter';
import SortRenderer from './components/SortRenderer';
import StockStatusRenderer from './components/StockStatusRenderer';

/* begin: 自定義清除搜尋 */
let name_filter;
let stock_filter;
let price_filter;
let real_price_filter;
let stock_status_filter;
let sort_filter;
let created_at_filter;
let model_filter;
let group_name_filter;

const clickClearFilter = (e, state) => {
  e.preventDefault();
  if (Object.keys(state).length) {
    if (Object.keys(state.filters).length) {
      if (name_filter && typeof state.filters.display_name == 'object') {
        name_filter('');
      }
      if (model_filter && typeof state.filters.model == 'object') {
        model_filter('');
      }
      if (stock_status_filter && typeof state.filters.stock_status == 'object') {
        stock_status_filter(0);
      }
      if (group_name_filter && typeof state.filters.froup_name == 'object') {
        group_name_filter('');
      }
      if (stock_filter && typeof state.filters.stock == 'object') {
        stock_filter({ comparator: "", number: "" });
      }
      if (price_filter && typeof state.filters.price == 'object') {
        price_filter({ comparator: "", number: "" });
      }
      if (real_price_filter && typeof state.filters.real_price_filter == 'object') {
        real_price_filter({ comparator: "", number: "" });
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


const productsSpecColumns = (t, smColumn) => {

  let fullColumns = [
    {
      dataField: 'id',
      text: 'id',
      hidden: true,
    },
    {
      dataField: 'model',
      text: t('columns-model'),
      sort: true,
      editable: false,
      filter: textFilter({
        className: 'form-control-sm',
        //&#46 = . 保留字改用編碼替代
        placeholder: t('bootstrap-table-Enter model'),
        getFilter: (filter) => {
          model_filter = filter;
        },
      }),
    },
    {
      dataField: 'group_name',
      text: t('columns-group_name'),
      sort: true,
      editable: false,
    },

    {
      dataField: 'name',
      text: t('columns-name'),
      sort: true,
      editable: false,
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
      dataField: 'main_photo',
      text: t('columns-main_photo'),
      //classes: 'table-image',
      sort: false,
      editable: false,
      hidden: true,
      formatter: (cell, row, rowIndex) => {
        return <img className="table-image" src={row.main_photo} />
      }
    },
    {
      dataField: 'stock',
      headerClasses: 'eightPTwoFiveRem',
      classes: 'editable-cursor-alias',
      text: t('columns-stock'),
      sort: true,
      editable: true,
      filter: numberFilter({
        numberClassName: "form-control-sm",
        comparatorClassName: "form-control-sm",
        placeholder: t('bootstrap-table-Enter stock'),
        getFilter: (filter) => {
          stock_filter = filter;
        },
      }),
    },
    {
      dataField: 'stock_status',
      text: t('columns-stock_status'),
      headerClasses: 'eightPTwoFiveRem',
      classes: 'editable-cursor-alias',
      sort: true,
      editable: true,
      filter: selectFilter({
        className: 'form-control-sm',
        options: selectStockStatusOptions(t),
        placeholder: t('isOptionsDefault'),
        getFilter: (filter) => {
          stock_status_filter = filter;
        },
      }),
      formatter: (cell, row, rowIndex) => {
        const stockStatusOptions = pageConfig.stock_status;
        var selected = stockStatusOptions.filter((item) => {
          if (item.value == row.stock_status) {
            return true;
          }
          return false;
        });
        return t(selected[0].label);
      },
      editorRenderer: (editorProps, value, row, column, rowIndex, columnIndex) => {
        //const ref = React.createRef();
        return (<StockStatusRenderer t={t} rowIndex={rowIndex} columnIndex={columnIndex} {...editorProps} value={parseInt(value)} />);
      },


    },

    {
      dataField: 'price',
      text: t('columns-price'),
      headerClasses: 'eightPTwoFiveRem',
      classes: 'editable-cursor-alias',
      sort: true,
      editable: true,
      hidden: true,
      filter: numberFilter({
        numberClassName: "form-control-sm",
        placeholder: t('bootstrap-table-Enter price'),
        comparatorClassName: "form-control-sm",
        getFilter: (filter) => {
          price_filter = filter;
        },
      }),
    },
    {
      dataField: 'real_price',
      text: t('columns-real_price'),
      headerClasses: 'eightPTwoFiveRem',
      classes: 'editable-cursor-alias',
      sort: true,
      editable: true,
      hidden: true,
      filter: numberFilter({
        numberClassName: "form-control-sm",
        placeholder: t('bootstrap-table-Enter real_price'),
        comparatorClassName: "form-control-sm",
        getFilter: (filter) => {
          real_price_filter = filter;
        },
      }),
    },

    {
      dataField: 'sort',
      text: t('columns-sort'),
      headerClasses: 'eightPTwoFiveRem',
      classes: 'editable-cursor-alias',
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
    actions(t, true)
  ];
  return fullColumns;

};


export {
  productsSpecColumns,
  clickClearFilter
};