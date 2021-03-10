import React from 'react';
import { npClassColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/npClassOptions';
import ClassPart from './components/ClassPart';

const NpClass = () => {
    
    //統一命名
    const classColumns = npClassColumns;
    const basePath = window.pageConfig.basePath;
    const classRelationApi = (basePath+'/'+SYS_LANG+'/admin/mp_class_has_np_class').replace(/^\/{2,}/, '/');
    const classRelation = {
        href: classRelationApi,
        self: 'np_class',
        parent: 'mp_class',
        bind: 'mp_class_has_np_class'
    };
    const paginateUrl = (basePath+'/'+SYS_LANG+'/api/admin/np_class').replace(/^\/{2,}/, '/');
    return (
        <ClassPart
          table="np_class"
          tableHeaderLabel="clsss_level3_datagrid"
          classRelation={classRelation}
          href="/admin/np_class"
          paginateUrl={paginateUrl}
          {...{classColumns, paginationOptions, clickClearFilter}}
        />
    );
};

export default NpClass;