
import React, {useState} from 'react';
import { CDropdown, CDropdownToggle, CDropdownMenu, CDropdownItem } from '@coreui/react'
import { customFilter } from 'react-bootstrap-table2-filter';
import { useLocation, useHistory} from "react-router-dom";

const action = (t) => {
    
    const history = useHistory();
    const location = useLocation();
    
    const editClick = (row) => {
        const to = location.pathname + '/' + row.id;
        history.push(to);
    }

    const copyClick = (row) => {
        const to = location.pathname + '/add';
        localStorage.setItem('copyId', row.id); 
        history.push(to);
    }

    
    return {
        dataField: 'actions',
        text: t('Actions'),
        headerClasses: 'table-md-show table-action-th',
        classes: 'table-md-show',
        editable: false,
        filter:customFilter(),
        filterRenderer: (onFilter, column) => {
            return (<span className="table-md-parent-show" />)
        },
        formatter: (cell, row, rowIndex) => {
            return (
                <CDropdown row={row}>
                    <CDropdownToggle caret color="info">{t('action methods')}</CDropdownToggle>
                    <CDropdownMenu>
                        <CDropdownItem  onClick={() => editClick(row)}><i className="fas fa-edit mr-1"/>{t('action-edit')}</CDropdownItem>
                        <CDropdownItem  onClick={() => copyClick(row)}><i className="fas fa-copy mr-1" />{t('action-copy')}</CDropdownItem>
                    </CDropdownMenu>
                </CDropdown>
            );
        },

    };
}

export default action;
