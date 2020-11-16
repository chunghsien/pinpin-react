import React from 'react';
import { textFilter, numberFilter } from 'react-bootstrap-table2-filter';
import DecimalRenderer from './components/DecimalRenderer';

/* begin: 自定義清除搜尋 */
let main_code_filter;
let name_filter;
let code_filter;
let rate_filter;

const clickClearFilter = (e, state) => {
    e.preventDefault();
    if (Object.keys(state).length) {
        if (Object.keys(state.filters).length) {
            if (code_filter && typeof state.filters.code == 'object') {
                code_filter('');
            }
            if (name_filter && typeof state.filters.name == 'object') {
                name_filter('');
            }
            if (main_code_filter && typeof state.filters.main_code == 'object') {
                main_code_filter('');
            }
            if (rate_filter && typeof state.filters.rate == 'object') {
                rate_filter('');
            }

        }
    }
}
/* end */

const currenciesColumns = (t, smColumn) => {
    let fullColumns = [
        {
            dataField: 'id',
            text: 'id',
            hidden: true,
        },
        {
            dataField: 'main_code',
            text: t('columns-main_code'),
            sort: false,
            editable: false,
        },

        {
            dataField: 'main_name',
            text: t('columns-main_name'),
            sort: false,
            editable: false,
            formatter: (cell, row, rowIndex) => {
                return t('currencies:'+row.main_name);
            }
        },

        {
            dataField: 'code',
            text: t('columns-exchange_rate_code'),
            sort: true,
            editable: false,
            filter: textFilter({
                className: 'form-control-sm',
                placeholder: t('bootstrap-table-Enter name'),
                getFilter: (filter) => {
                    code_filter = filter;
                },
            }),
        },

        {
            dataField: 'name',
            text: t('columns-exchange_rate_name'),
            sort: false,
            editable: false,
            formatter: (cell, row, rowIndex) => {
                return t('currencies:'+row.name);
            }
        },
        {
            dataField: 'rate',
            text: t('columns-exange_rate'),
            sort: true,
            editable: true,
            editorRenderer: (editorProps, value, row, column, rowIndex, columnIndex) => {
                return (<DecimalRenderer {...editorProps} value={parseFloat(value)} />);
            },

            filter: numberFilter({
                numberClassName: "form-control-sm",
                placeholder: t('bootstrap-table-Enter exchange_rate'),
                comparatorClassName: "form-control-sm",
                getFilter: (filter) => {
                    rate_filter = filter;
                },
            }),
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


export {
    currenciesColumns,
    clickClearFilter
};