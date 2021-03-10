import React, { Suspense, useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { Switch, Route } from "react-router";
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
import loadable from '@loadable/component';

import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';
import { documentsColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/documentsOptions';
import { connect } from "react-redux";


const TabLink = loadable(() => import('./components/form/TabLink'));
const SeoForm = loadable(() => import('./components/form/SeoForm'));
const FacebookTagsForm = loadable(() => import('./components/form/FacebookTagsForm'));
const FormBackGridFixed = loadable(() => import('./components/FormBackGridFixed'));
const BannerHasDocumentsForm = loadable(() => import('./components/form/BannerHasDocumentsForm'));
const DocumentsForm = loadable(() => import('./components/form/DocumentsForm'));
const DocumentsContentForm = loadable(() => import('./components/form/DocumentsContentForm'));

const Documents = (props) => {

  const { t } = useTranslation(['translation']);
  const table = "documents";
  const useColumns = documentsColumns;
  const columns = useColumns(t, 'name');
  const basePath = window.pageConfig.basePath;
  const paginateUrl = (basePath+'/' + SYS_LANG + '/api/admin/documents').replace(/\/{2,}/, '/');
  const pagination = paginationOptions(t);
  const locationPathname = location.pathname.replace(/\/add$/, '').replace(/\/\d+$/, '');

  const {formRows} = props;
  
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
                    !!(formRows && formRows.documents && formRows.documents.type == 2) &&
                    <TabLink tab="documents-content-form" label="documents content form" />
                  }
                  {
                    !!(formRows && formRows.documents && formRows.documents.type == 1) &&
                    <TabLink tab="banner-form" label="Documents carousel apply" />
                  }
                  {
                    !!(formRows && formRows.documents && formRows.documents.route == `/${SYS_LANG}`) &&
                    <TabLink tab="s-carousel-form" label="小輪播" />
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
                  !!(formRows && formRows.documents && formRows.documents.type == 2) &&
                  <DocumentsContentForm href="/admin/documents_content" tab="documents-content-form" table={table} />
                }

                {
                  !!(formRows && formRows.documents && formRows.documents.type == 1) &&
                  <BannerHasDocumentsForm
                    classRelation={{
                      parent: 'banner',
                      self: 'documents'
                    }}
                    href="/admin/banner_has_documents"
                    tab="banner-form"
                    bannerType="carousel"
                  />
                }
                {
                  !!(formRows && formRows.documents && formRows.documents.route == `/${SYS_LANG}`) &&
                  <BannerHasDocumentsForm
                    classRelation={{
                      parent: 'banner',
                      self: 'documents'
                    }}
                    href="/admin/banner_has_documents"
                    tab="s-carousel-form"
                    bannerType="s_carousel"
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

const mapStateToProps = (state) => {
  return {
    ...state
  };
};

//export default Documents;
export default connect(mapStateToProps)(Documents);