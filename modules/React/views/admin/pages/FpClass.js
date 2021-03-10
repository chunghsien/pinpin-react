import React from 'react';
import { fpClassColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/fpClassOptions';
import ClassPart from './components/ClassPart';

const FpClass = () => {
    //統一命名
    const classColumns = fpClassColumns;
    const basePath = window.pageConfig.basePath;
    const paginateUrl = (basePath+'/'+SYS_LANG+'/api/admin/fp_class').replace(/^\/{2,}/, '/');
    return (
        <ClassPart
          table="fp_class"
          tableHeaderLabel="clsss_level1_datagrid"
          href="/admin/fp_class"
          paginateUrl={paginateUrl}
          {...{classColumns, paginationOptions, clickClearFilter}}
        />
    );
};

export default FpClass;