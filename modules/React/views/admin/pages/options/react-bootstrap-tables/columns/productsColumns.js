import React from 'react';
import { textFilter, numberFilter, selectFilter, dateFilter } from 'react-bootstrap-table2-filter';
import { Type } from 'react-bootstrap-table2-editor';
import actions from './components/actions';
import sortFormatter from './components/sortFormatter';
import SortRenderer from './components/SortRenderer';
import selectYesOrNoOptions from './components/selectYesOrNoOptions';

/* begin: 自定義清除搜尋 */
let display_name_filter;
let model_filter;
let is_new_filter;
let is_hot_filter;
let is_show_filter;
let viewed_count_filter;
let sort_filter;
let created_at_filter;

const clickClearFilter = (e, state) => {
    e.preventDefault();
    if (Object.keys(state).length) {
        if (Object.keys(state.filters).length) {
            if (display_name_filter && typeof state.filters.display_name == 'object') {
                display_name_filter('');
            }
            if (model_filter && typeof state.filters.model == 'object') {
                model_filter('');
            }
            if (is_new_filter && typeof state.filters.is_new == 'object') {
                is_new_filter('');
            }
            if (is_hot_filter && typeof state.filters.is_hot == 'object') {
                is_hot_filter('');
            }
            if (is_show_filter && typeof state.filters.is_show == 'object') {
                is_show_filter('');
            }
            if (viewed_count_filter && typeof state.filters.view_count == 'object') {
                viewed_count_filter('');
            }
            if(sort_filter&& typeof state.filters.sort == 'object') {
                sort_filter({comparator: "", number: ""});
            }
            if(created_at_filter && typeof state.filters.created_at == 'object') {
                created_at_filter({comparator: "", date: ""});
            }
        }
    }
}
/* end */


const productsColumns = (t, smColumn) => {
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
            dataField: 'model',
            text: t('columns-model'),
            sort: true,
            editable: true,
            filter: textFilter({
                className: 'form-control-sm',
                placeholder: t('bootstrap-table-Enter model'),
                getFilter: (filter) => {
                    model_filter = filter;
                },
            }),
        },
        {
            dataField: 'is_new',
            text: t('columns-is_new'),
            sort: true,
            editor: {
                type: Type.CHECKBOX,
                value: '1:0',
            },
            classes: 'allow-cell-edit',
            headerClasses : 'sixPfiveRem',
            filter: selectFilter({
                className: 'form-control-sm',
                options: selectYesOrNoOptions(t),
                placeholder: t('isBoolOptionsDefault'),
                getFilter: (filter) => {
                    is_new_filter = filter;
                },
            }),
            formatter: (cell, row) => {
                return (
                    row.is_new == 1
                        ? t('is_type_1')
                        : t('is_type_0')
                );
            }
        },
        {
            dataField: 'is_hot',
            text: t('columns-is_hot'),
            sort: true,
            editor: {
                type: Type.CHECKBOX,
                value: '1:0',
            },
            classes: 'allow-cell-edit',
            headerClasses : 'sixPfiveRem',
            filter: selectFilter({
                className: 'form-control-sm',
                options: selectYesOrNoOptions(t),
                placeholder: t('isBoolOptionsDefault'),
                getFilter: (filter) => {
                    is_hot_filter = filter;
                },
            }),
            formatter: (cell, row) => {
                return (
                    row.is_hot == 1
                        ? t('is_type_1')
                        : t('is_type_0')
                );
            }
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
            headerClasses : 'sixPfiveRem',
            filter: selectFilter({
                className: 'form-control-sm',
                options: selectYesOrNoOptions(t),
                placeholder: t('isBoolOptionsDefault'),
                getFilter: (filter) => {
                    is_hot_filter = filter;
                },
            }),
            formatter: (cell, row) => {
                return (
                    row.is_show == 1
                        ? t('is_type_1')
                        : t('is_type_0')
                );
            }
        },
        {
            dataField: 'viewed_count',
            text: t('columns-viewed_count'),
            sort: true,
            editable: false,
            formatter: sortFormatter,
            filter: numberFilter({
                numberClassName: "form-control-sm",
                placeholder: t('bootstrap-table-Enter viewed count'),
                comparatorClassName: "form-control-sm",
                getFilter: (filter) => {
                    viewed_count_filter = filter;
                },
            }),
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
            filter: dateFilter({
                dateClassName: 'form-control-sm',
                comparatorClassName: "form-control-sm",
                defaultValue:{date: null, comparator: ""},
                getFilter: (filter) => {
                    created_at_filter = filter;
                },
            }),
        },
        actions(t)
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
    productsColumns,
    clickClearFilter
};