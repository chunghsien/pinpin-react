import React from 'react';
import { fnClassColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/fnClassOptions';
import ClassPart from './components/ClassPart';

const FnClass = () => {
    //統一命名
    const classColumns = fnClassColumns;
    
    return (
        <ClassPart table="fn_class" href="/admin/fn_class" paginateUrl="/api/admin/fn_class" {...{classColumns, paginationOptions, clickClearFilter}}  />
    );
};

export default FnClass;