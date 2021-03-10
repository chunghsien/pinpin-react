import React from 'react';
import { mnClassColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/mnClassOptions';
import ClassPart from './components/ClassPart';

const MnClass = () => {
    
    //統一命名
    const classColumns = mnClassColumns;
    const basePath = window.pageConfig.basePath;
    const classRelationApi = (basePath+'/'+SYS_LANG+'/admin/fn_class_has_mn_class').replace(/^\/{2,}/, '/');
    const classRelation = {
        href: classRelationApi,
        self: 'mn_class',
        parent: 'fn_class',
        bind: 'fn_class_has_mn_class'
    };
    const paginateUrl = (basePath+'/'+SYS_LANG+'/api/admin/mn_class').replace(/^\/{2,}/, '/');
    return (
        <ClassPart
          table="mn_class"
          tableHeaderLabel="clsss_level2_datagrid"
          classRelation={classRelation}
          href="/admin/mn_class"
          paginateUrl={paginateUrl}
          {...{classColumns, paginationOptions, clickClearFilter}}
        />
    );
};

export default MnClass;