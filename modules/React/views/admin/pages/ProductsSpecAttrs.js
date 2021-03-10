import React from 'react';
import { productsSpecAttrsColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/productsSpecAttrsOptions';
import loadable from '@loadable/component';
import { useTranslation } from 'react-i18next';
import { Switch, Route } from "react-router";
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';

const TabLink = loadable(() => import('./components/form/TabLink'));
const FormBackGridFixed = loadable(() => import('./components/FormBackGridFixed'));
const ProductsSpecAttrsForm = loadable(() => import('./components/form/ProductsSpecAttrsForm'));


const ProductsSpecAttrs = () => {

  const { t } = useTranslation(['translation']);
  //Api位置
  const basePath = window.pageConfig.basePath;
  const paginateUrl = (basePath + '/' + SYS_LANG + '/api/admin/products_spec_attrs').replace(/^\/{2,}/, '/');

  const pagination = paginationOptions(t);
  const locationPathname = location.pathname.replace(/\/add$/, '').replace(/\/\d+$/, '');
  const columns = productsSpecAttrsColumns(t, 'name');
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
            <ProductsSpecAttrsForm />
          </CTabs>
        </Route>
      }
      <Route path={locationPathname}>
        <CRow>
          <CCol>
            <CCard>
              <CCardHeader>{t('Products spec group data grid')}</CCardHeader>
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

export default ProductsSpecAttrs;