import React, { Suspense, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { Switch, Route } from "react-router";
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
import loadable from '@loadable/component';

import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';

import { managerRolesColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/managerRolesOptions';
import simNavLinkClick from './components/simNavLinkClick';

const TabLink = loadable(() => import('./components/form/TabLink'));
const ManagerRolesForm = loadable(() => import('./components/form/ManagerRolesForm'));
const FormBackGridFixed = loadable(() => import('./components/FormBackGridFixed'));
const PermissionForm = loadable(() => import('./components/form/PermissionForm'));

const ManagerRoles = () => {
  const { t } = useTranslation(['translation']);
  const useColumns = managerRolesColumns;
  const columns = useColumns(t, 'name');

  const pagination = paginationOptions(t);
  const locationPathname = location.pathname.replace(/\/add$/, '').replace(/\/\d+$/, '');
  const basePath = window.pageConfig.basePath;
  const paginateUrl = (basePath + '/' + SYS_LANG + '/api/admin/manager_roles').replace(/^\/{2,}/, '/');
  //const table = 'manufactures';

  const classRelationApi = (basePath + '/' + SYS_LANG + '/api/admin/roles_has_permission').replace(/^\/{2,}/, '/');
  const classRelation = {
    href: classRelationApi,
    self: 'roles',
    parent: 'permission',
    bind: 'roles_has_permission'
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
                  <TabLink tab="class-releation-form" label="permission detail list" />
                </>
              }

            </CNav>
            <ManagerRolesForm href="/admin/manager_roles" />
            {
              location.pathname.match(/\/\d+$/) &&
              <Suspense fallback={<div>Loading...</div>}>
                <PermissionForm href="/admin/roles_has_permission" classRelation={classRelation} />
              </Suspense>
            }

          </CTabs>
        </Route>
      }
      <Route path={locationPathname}>
        <CRow>
          <CCol>
            <CCard>
              <CCardHeader>{t('manager roles list')}</CCardHeader>
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

export default ManagerRoles;