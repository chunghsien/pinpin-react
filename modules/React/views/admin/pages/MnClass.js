import React from 'react';
import { mnClassColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/mnClassOptions';
import ClassPart from './components/ClassPart';

const MnClass = () => {
    
    //統一命名
    const classColumns = mnClassColumns;
    const classRelation = {
        href: '/admin/fn_class_has_mn_class',
        self: 'mn_class',
        parent: 'fn_class',
        bind: 'fn_class_has_mn_class'
    };
    return (
        <ClassPart table="np_class" classRelation={classRelation} href="/admin/mn_class" paginateUrl="/api/admin/mn_class" {...{classColumns, paginationOptions, clickClearFilter}}  />
    );
};

export default MnClass;