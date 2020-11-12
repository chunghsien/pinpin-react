import React from 'react';
import { npClassColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/npClassOptions';
import ClassPart from './components/ClassPart';

const NpClass = () => {
    
    //統一命名
    const classColumns = npClassColumns;
    const classRelation = {
        href: '/admin/mp_class_has_np_class',
        self: 'np_class',
        parent: 'mp_class',
        bind: 'mp_class_has_np_class'
    };
    return (
        <ClassPart table="np_class" classRelation={classRelation} href="/admin/np_class" paginateUrl="/api/admin/np_class" {...{classColumns, paginationOptions, clickClearFilter}}  />
    );
};

export default NpClass;