import React, { Suspense, useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { Switch, Route } from "react-router";
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
import loadable from '@loadable/component';

import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';
import { documentsColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/documentsOptions';
import axios from 'axios';

const TabLink = loadable(() => import('./components/form/TabLink'));
const SeoForm = loadable(() => import('./components/form/SeoForm'));
const FacebookTagsForm = loadable(() => import('./components/form/FacebookTagsForm'));
const FormBackGridFixed = loadable(() => import('./components/FormBackGridFixed'));
const BannerHasDocumentsForm = loadable(() => import('./components/form/BannerHasDocumentsForm'));
const DocumentsForm = loadable(() => import('./components/form/DocumentsForm'));
const DocumentsContentForm = loadable(() => import('./components/form/DocumentsContentForm'));

const Documents = (/*props*/) => {

  const { t } = useTranslation(['translation']);
  const table = "documents";
  const useColumns = documentsColumns;
  const columns = useColumns(t, 'name');
  const paginateUrl = '/' + SYS_LANG + '/api/admin/documents';
  const pagination = paginationOptions(t);
  const locationPathname = location.pathname.replace(/\/add$/, '').replace(/\/\d+$/, '');

  const [type, setType] = useState(2);

  const match = location.pathname.match(/\d+$/);
  useEffect(() => {
    if (match) {
      var uri = location.pathname.replace(/admin/, 'api/admin');
      uri += '?getType=1';
      axios.get(uri).then((response) => {
        setType(response.data.data.type);
      });
    }
  }, [match]);

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
                  {
                    type == 2 &&
                    <TabLink tab="documents-content-form" label="documents content form" />
                  }
                  {
                    type == 1 &&
                    <TabLink tab="banner-form" label="Documents carousel apply" />
                  }

                  {/*<TabLink tab="banner-form" label="Banner form" />*/}
                  <TabLink tab="seo-form" label="SEO form" />
                  <TabLink tab="facebook_tag-form" label="Facebook tags form" />
                </>
              }
            </CNav>
            <DocumentsForm href="/admin/documents" tab="default-form" />

            {
              location.pathname.match(/\/\d+$/) &&
              <Suspense fallback={<div>Loading...</div>}>
                {
                  type == 2 &&
                  <DocumentsContentForm href="/admin/documents_content" tab="documents-content-form" table={table} />
                }

                {
                  type == 1 &&
                  <BannerHasDocumentsForm
                    classRelation={{
                      parent: 'banner',
                      self: 'documents'
                    }}
                    href="/admin/banner_has_documents"
                    tab="banner-form"
                  />
                }
                <SeoForm href="/admin/seo" tab="seo-form" table={table} />
                <FacebookTagsForm href="/admin/facebook_tags" table={table} />
              </Suspense>
            }
          </CTabs>
        </Route>
      }
      <Route path={locationPathname}>
        <CRow>
          <CCol>
            <CCard>
              <CCardHeader>{t('Base page data grid')}</CCardHeader>
              <CCardBody>
                <AdminBootstrapTable
                  paginateUrl={paginateUrl}
                  isSelectRow
                  isInsertAction
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

export default Documents;