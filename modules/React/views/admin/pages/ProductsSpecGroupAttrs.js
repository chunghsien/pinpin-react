import React from 'react';
import { productsSpecGroupAttrsColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/productsSpecGroupAttrsOptions';
import loadable from '@loadable/component';
import { useTranslation } from 'react-i18next';
import { Switch, Route } from "react-router";
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';

const TabLink = loadable(() => import('./components/form/TabLink'));
const FormBackGridFixed = loadable(() => import('./components/FormBackGridFixed'));
const ProductsSpecGroupAttrsForm = loadable(() => import('./components/form/ProductsSpecGroupAttrsForm'));


const ProductsSpecGroupAttrs = () => {

  const { t } = useTranslation(['translation']);
  //Api位置
  const basePath = window.pageConfig.basePath;
  const paginateUrl = (basePath + '/' + SYS_LANG + '/api/admin/products_spec_group_attrs').replace(/^\/{2,}/, '/');

  const pagination = paginationOptions(t);
  const locationPathname = location.pathname.replace(/\/add$/, '').replace(/\/\d+$/, '');
  const columns = productsSpecGroupAttrsColumns(t, 'name');
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
            <ProductsSpecGroupAttrsForm />
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

export default ProductsSpecGroupAttrs;