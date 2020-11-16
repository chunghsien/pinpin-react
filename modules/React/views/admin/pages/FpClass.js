import React from 'react';
import { fpClassColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/fpClassOptions';
import ClassPart from './components/ClassPart';

const FpClass = () => {
    //統一命名
    const classColumns = fpClassColumns;
    
    return (
        <ClassPart table="fp_class" href="/admin/fp_class" paginateUrl="/api/admin/fp_class" {...{classColumns, paginationOptions, clickClearFilter}}  />
    );
};

export default FpClass;