import React, { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { Switch, Route } from "react-router";
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
import loadable from '@loadable/component';

import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';
import { bannerColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/bannerOptions';
//import axios from 'axios';

const TabLink = loadable(() => import('./components/form/TabLink'));
const CarouselForm = loadable(() => import('./components/form/CarouselForm'));
const BannerHasDocumentsForm = loadable(() => import('./components/form/BannerHasDocumentsForm'));

const Banner = (/*props*/) => {

  const { t } = useTranslation(['translation']);
  //const table = "banner";
  const useColumns = bannerColumns;
  const columns = useColumns(t, 'name');
  const paginateUrl = '/' + SYS_LANG + '/api/admin/banner';
  const pagination = paginationOptions(t);
  const locationPathname = location.pathname.replace(/\/add$/, '').replace(/\/\d+$/, '');
  const FormBackGridFixed = loadable(() => import('./components/FormBackGridFixed'));

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
                <TabLink tab="banner-form" label="Documents carousel apply" />
              }
            </CNav>
            <CarouselForm href="/admin/banner" tab="default-form" />
            {
              location.pathname.match(/\/\d+$/) &&
              <BannerHasDocumentsForm
                classRelation={{
                  parent: 'documents',
                  self: 'banner'
                }}
                href="/admin/banner_has_documents"
                tab="banner-form"
              />
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

export default Banner;