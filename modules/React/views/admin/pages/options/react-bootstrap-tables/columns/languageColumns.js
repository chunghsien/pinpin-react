import { textFilter, selectFilter } from 'react-bootstrap-table2-filter';
import { Type } from 'react-bootstrap-table2-editor';
import selectUseOptions from './components/selectUseOptions';


/* begin: 自定義清除搜尋 */
let code_filter;
let display_name_filter;
let is_use_filter;

const clickClearFilter = (e, state) => {
    if (Object.keys(state).length > 0 && Object.keys(state.filters).length > 0) {
        e.preventDefault();
        if (is_use_filter && typeof state.filters.is_use == 'object') {
            is_use_filter('');
        }
        if (display_name_filter && typeof state.filters.display_name == 'object') {
            display_name_filter('');
        }
        if (code_filter && typeof state.filters.code == 'object') {
            code_filter('');
        }
    }
}
/* end */


const languageColumns = (t, smColumn) => {
    let fullColumns = [
        {
            dataField: 'language_id',
            text: t('language_id'),
            hidden: true,
        },
        {
            dataField: 'locale_id',
            text: t('locale_id'),
            hidden: true,
        },
        {
            dataField: 'code',
            text: t('column-code'),
            sort: true,
            editable: false,
            filter: textFilter({
                className: 'form-control-sm',
                getFilter: (filter) => {
                    code_filter = filter;
                },
            }),
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
            dataField: 'is_use',
            text: t('admin-language:is use'),
            sort: true,
            editor: {
                type: Type.CHECKBOX,
                value: '1:0',
            },
            classes: 'allow-cell-edit',
            filter: selectFilter({
                className: 'form-control-sm',
                options: selectUseOptions(t),
                placeholder: t('isUseOptionsDefault'),
                getFilter: (filter) => {
                    is_use_filter = filter;
                },
            }),
            formatter: (cell, row) => {
                return (
                    row.is_use == 1
                        ? t('admin-language:is_use_1')
                        : t('admin-language:is_use_0')
                );
            }
        },
        //actions(t)
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
    languageColumns,
    clickClearFilter
};