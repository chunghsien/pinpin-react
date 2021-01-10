import React, { Suspense, useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { Switch, Route } from "react-router";
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
//import loadable from '@loadable/component';
import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';
import { documentsFooterColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/documentsFooterOptions';
import TabLink from './components/form/TabLink';
import FormBackGridFixed from './components/FormBackGridFixed';
import DocumentsFooterForm from './components/form/DocumentsFooterForm';

const DocumentsFooter = () => {

  const { t } = useTranslation(['translation']);
  //const table = "layout_zones";
  const useColumns = documentsFooterColumns;
  const columns = useColumns(t, 'name');
  const paginateUrl = '/' + SYS_LANG + '/api/admin/documents_footer';
  const pagination = paginationOptions(t);
  const locationPathname = location.pathname.replace(/\/add$/, '').replace(/\/\d+$/, '');

  return (
    <Switch>
      {
        typeof locationPathname != 'undefined' &&
        <Route path={locationPathname + '/:method_or_id'}>
          <FormBackGridFixed t={t} />
          <CTabs id="tabs-root" activeTab="default-form">
            <CNav variant="tabs">
              <TabLink tab="default-form" label="Default form" />
            </CNav>
            <DocumentsFooterForm />
          </CTabs>
        </Route>
      }
      <Route path={locationPathname}>
        <CRow>
          <CCol>
            <CCard>
              <CCardHeader>{t('Documents footer data grid')}</CCardHeader>
              <CCardBody>
                <AdminBootstrapTable
                  paginateUrl={paginateUrl}
                  isSelectRow
                  columns={columns}
                  isFilterReset
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

export default DocumentsFooter;