import { textFilter, dateFilter } from 'react-bootstrap-table2-filter';
import actions from './components/actions';

/* begin: 自定義清除搜尋 */
let display_name_filter;
let name_filter;
let route_filter;
let created_at_filter;

const clickClearFilter = (e, state) => {
    e.preventDefault();
    if (Object.keys(state).length) {
        if (Object.keys(state.filters).length) {
            if (display_name_filter && typeof state.filters.display_name == 'object') {
                display_name_filter('');
            }
            if (name_filter && typeof state.filters.name == 'object') {
                name_filter('');
            }
            if (route_filter && typeof state.filters.route == 'object') {
                route_filter('');
            }

            if(created_at_filter && typeof state.filters.created_at == 'object') {
                created_at_filter({comparator: "", date: ""});
            }
        }
    }
}
/* end */


const documentsColumns = (t, smColumn) => {
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
            dataField: 'route',
            text: t('columns-route'),
            sort: true,
            editable: true,
            filter: textFilter({
                className: 'form-control-sm',
                placeholder: t('bootstrap-table-Enter name'),
                getFilter: (filter) => {
                    route_filter = filter;
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
    documentsColumns,
    clickClearFilter
};