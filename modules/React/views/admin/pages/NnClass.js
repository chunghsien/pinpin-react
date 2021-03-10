import React from 'react';
import { nnClassColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/nnClassOptions';
import ClassPart from './components/ClassPart';

const NnClass = () => {

  //統一命名
  const classColumns = nnClassColumns;
  const basePath = window.pageConfig.basePath;
  const classRelationApi = (basePath + '/' + SYS_LANG + '/admin/mn_class_has_nn_class').replace(/^\/{2,}/, '/');
  const classRelation = {
    href: classRelationApi,
    self: 'nn_class',
    parent: 'mn_class',
    bind: 'mn_class_has_nn_class'
  };
  const paginateUrl = (basePath+'/'+SYS_LANG+'/api/admin/nn_class').replace(/^\/{2,}/, '/');
  return (
    <ClassPart
      table="nn_class"
      tableHeaderLabel="clsss_level3_datagrid"
      classRelation={classRelation}
      href="/admin/nn_class"
      paginateUrl={paginateUrl}
      {...{ classColumns, paginationOptions, clickClearFilter }}
    />
  );
};

export default NnClass;