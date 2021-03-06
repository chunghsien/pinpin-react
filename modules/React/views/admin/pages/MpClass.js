import React from 'react';
import { mpClassColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/mpClassOptions';
import ClassPart from './components/ClassPart';

const MpClass = () => {
    
    //統一命名
    const classColumns = mpClassColumns;
    const basePath = window.pageConfig.basePath;
    const classRelationApi = (basePath+'/'+SYS_LANG+'/admin/fp_class_has_mp_class').replace(/^\/{2,}/, '/');
    const classRelation = {
        href: classRelationApi,
        self: 'mp_class',
        parent: 'fp_class',
        bind: 'fp_class_has_mp_class'
    };
    const paginateUrl = (basePath+'/'+SYS_LANG+'/api/admin/mp_class').replace(/^\/{2,}/, '/');
    return (
        <ClassPart
          table="mp_class"
          tableHeaderLabel="clsss_level2_datagrid"
          classRelation={classRelation}
          href="/admin/mp_class"
          paginateUrl={paginateUrl}
          {...{classColumns, paginationOptions, clickClearFilter}}
        />
    );
};

export default MpClass;