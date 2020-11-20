import React from 'react';
import { nnClassColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/nnClassOptions';
import ClassPart from './components/ClassPart';

const NnClass = () => {
    
    //統一命名
    const classColumns = nnClassColumns;
    const classRelation = {
        href: '/'+SYS_LANG+'/admin/mn_class_has_nn_class',
        self: 'nn_class',
        parent: 'mn_class',
        bind: 'mn_class_has_nn_class'
    };
    return (
        <ClassPart table="nn_class" classRelation={classRelation} href="/admin/nn_class" paginateUrl={'/'+SYS_LANG+'/api/admin/nn_class'} {...{classColumns, paginationOptions, clickClearFilter}}  />
    );
};

export default NnClass;