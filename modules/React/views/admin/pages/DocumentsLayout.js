import React from 'react';
import { useTranslation } from 'react-i18next';
import { Switch, Route } from "react-router";
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
//import loadable from '@loadable/component';
import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';
import { documentsLayoutColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/documentsLayoutOptions';
import TabLink from './components/form/TabLink';
import FormBackGridFixed from './components/FormBackGridFixed';
import DocumentsLayoutForm from './components/form/DocumentsLayoutForm';

const DocumentsLayout = () => {

  const { t } = useTranslation(['translation']);
  //const table = "layout_zones";
  const useColumns = documentsLayoutColumns;
  const columns = useColumns(t, 'name');
  const basePath = window.pageConfig.basePath;
  const paginateUrl = (basePath+'/' + SYS_LANG + '/api/admin/documents_layout').replace(/^\/{2,}/, '/');
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
            <DocumentsLayoutForm />
          </CTabs>
        </Route>
      }
      <Route path={locationPathname}>
        <CRow>
          <CCol>
            <CCard>
              <CCardHeader>{t('Documents layout data grid')}</CCardHeader>
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

export default DocumentsLayout;