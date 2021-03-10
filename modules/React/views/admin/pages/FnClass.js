import React from 'react';
import { fnClassColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/fnClassOptions';
import ClassPart from './components/ClassPart';

const FnClass = () => {
    //統一命名
    const classColumns = fnClassColumns;
    const basePath = window.pageConfig.basePath;
    const paginateUrl = (basePath+'/'+SYS_LANG+'/api/admin/fn_class').replace(/^\/{2,}/, '/');
    return (
        <ClassPart
          tableHeaderLabel="clsss_level1_datagrid"
          table="fn_class"
          href="/admin/fn_class"
          paginateUrl={paginateUrl}
          {...{classColumns, paginationOptions, clickClearFilter}}
        />
    );
};

export default FnClass;