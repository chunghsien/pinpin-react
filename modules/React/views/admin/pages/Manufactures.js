import React from 'react';
import { useTranslation } from 'react-i18next';
import { Switch, Route } from "react-router";
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
import loadable from '@loadable/component';

import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';

import { manufacturesColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/manufacturesOptions';


const TabLink = loadable(() => import('./components/form/TabLink'));
const ManufacturesForm = loadable(() => import('./components/form/ManufacturesForm'));
const FormBackGridFixed = loadable(() => import('./components/FormBackGridFixed'));
//const SeoForm = loadable(() => import('./components/form/SeoForm'));
//const FacebookTagsForm = loadable(() => import('./components/form/FacebookTagsForm'));


const Manufactures = () => {
  const { t } = useTranslation(['translation']);
  const useColumns = manufacturesColumns;
  const columns = useColumns(t, 'name');

  const pagination = paginationOptions(t);
  const locationPathname = location.pathname.replace(/\/add$/, '').replace(/\/\d+$/, '');
  const basePath = window.pageConfig.basePath;
  const paginateUrl = (basePath + '/' + SYS_LANG + '/api/admin/manufactures').replace(/^\/{2,}/, '/');

  return (
    <Switch>
      {
        typeof locationPathname != 'undefined' &&
        <Route path={locationPathname + '/:method_or_id'}>
          <FormBackGridFixed t={t} />
          <CTabs id="tabs-root" activeTab="default-form">
            <CNav variant="tabs">
              <TabLink tab="default-form" label="Default form" />
              {/*
                location.pathname.match(/\/\d+$/) &&
                <>
                  <TabLink tab="seo-form" label="SEO form" />
                  <TabLink tab="facebook_tag-form" label="Facebook tags form" />
                </>
              */}

            </CNav>
            <ManufacturesForm href="/admin/manufactures" />
            {/*
                            location.pathname.match(/\/\d+$/) &&
                            <Suspense fallback={<div>Loading...</div>}>
                                <SeoForm href="/admin/seo" table={table} />
                                <FacebookTagsForm href="/admin/facebook_tags" table={table} />
                            </Suspense>
                        */}

          </CTabs>
        </Route>
      }
      <Route path={locationPathname}>
        <CRow>
          <CCol>
            <CCard>
              <CCardHeader>{t('Manufactures data grid')}</CCardHeader>
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

export default Manufactures;