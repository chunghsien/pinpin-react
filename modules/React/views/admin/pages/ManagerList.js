import React, { Suspense, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { Switch, Route } from "react-router";
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
import loadable from '@loadable/component';

import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';
//import simNavLinkClick from './components/simNavLinkClick';
import { managerListColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/managerListOptions';

const TabLink = loadable(() => import('./components/form/TabLink'));
const FormBackGridFixed = loadable(() => import('./components/FormBackGridFixed'));
const ManagerListForm = loadable(() => import('./components/form/ManagerListForm'));
const ClassRelationForm = loadable(() => import('./components/form/ClassRelationForm'));
import simNavLinkClick from './components/simNavLinkClick';

const ManagerList = (/*props*/) => {

  const { t } = useTranslation(['translation']);
  const useColumns = managerListColumns;
  const columns = useColumns(t, 'name');
  const basePath = window.pageConfig.basePath;
  const paginateUrl = (basePath + '/' + SYS_LANG + '/api/admin/manager_list').replace(/^\/{2,}/, '/');
  const pagination = paginationOptions(t);
  const locationPathname = location.pathname.replace(/\/add$/, '').replace(/\/\d+$/, '');

  const classRelationApi = (basePath + '/' + SYS_LANG + '/admin/manager_list').replace(/^\/{2,}/, '');
  const classRelation = {
    href: classRelationApi,
    self: 'users',
    parent: 'roles',
    bind: 'users_has_roles'
  };

  const count = 0;
  useEffect(() => {
    //修正階層下拉選單無法開啟的錯誤
    simNavLinkClick();
  }, [count]);

  return (
    <Switch>
      {
        typeof locationPathname != 'undefined' &&
        <Route path={locationPathname + '/:method_or_id'}>
          <FormBackGridFixed t={t} />
          <CTabs id="tabs-root" activeTab="default-form">
            <CNav variant="tabs">
              <TabLink tab="default-form" label="Default form" />
              {
                location.pathname.match(/\/\d+$/) &&
                <>
                  <TabLink tab="class-releation-form" label="manager group" />
                </>
              }

            </CNav>
            <ManagerListForm href="/admin/manager_list" />
            {
              location.pathname.match(/\/\d+$/) &&
              <Suspense fallback={<div>Loading...</div>}>
                <ClassRelationForm isMulti={false} href="/admin/users_has_roles" classRelation={classRelation} />
              </Suspense>
            }

          </CTabs>
        </Route>
      }
      <Route path={locationPathname}>
        <CRow>
          <CCol>
            <CCard>
              <CCardHeader>{t('manager list')}</CCardHeader>
              <CCardBody>
                <AdminBootstrapTable
                  paginateUrl={paginateUrl}
                  columns={columns}
                  isSelectRow
                  isFilterReset
                  isInsertAction
                  paginationOptions={pagination}
                  translation={t}
                  clearFilterTrigger={clickClearFilter}
                />
              </CCardBody>
            </CCard>
          </CCol>
        </CRow>
      </Route>

    </Switch>
  );
};

export default ManagerList;