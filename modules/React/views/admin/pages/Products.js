import React, { Suspense, useEffect } from 'react';
import { connect } from "react-redux";
import { useTranslation } from 'react-i18next';
import { Switch, Route } from "react-router";
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
import loadable from '@loadable/component';

import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';
//import simNavLinkClick from './components/simNavLinkClick';
import { productsColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/productsOptions';
import { FORM_ACTIVE_TAB } from "../actions/formRowsAction";

const TabLink = loadable(() => import('./components/form/TabLink'));
const SeoForm = loadable(() => import('./components/form/SeoForm'));
const FacebookTagsForm = loadable(() => import('./components/form/FacebookTagsForm'));
const FormBackGridFixed = loadable(() => import('./components/FormBackGridFixed'));
const ProductsForm = loadable(() => import('./components/form/ProductsForm'));
const ProductsSpecGroupForm = loadable(() => import('./components/form/ProductsSpecGroupForm'));
const ClassRelationForm = loadable(() => import('./components/form/ClassRelationForm'));
const AssetsForm = loadable(() => import('./components/form/AssetsForm'));
//const ProductsHasAttributesForm = loadable(() => import('./components/form/ProductsHasAttributesForm'));


const Products = (props) => {

  const { t } = useTranslation(['translation']);
  const useColumns = productsColumns;
  const columns = useColumns(t, 'name');
  const table = 'products';
  const basePath = window.pageConfig.basePath;
  const paginateUrl = (basePath + '/' + SYS_LANG + '/api/admin/products').replace(/^\/{2,}/, '/');
  const pagination = paginationOptions(t);
  const locationPathname = location.pathname.replace(/\/add$/, '').replace(/\/\d+$/, '');

  const classRelationApi = (basePath + '/' + SYS_LANG + '/admin/np_class_has_products').replace(/^\/{2,}/, '/');
  const classRelation = {
    href: classRelationApi,
    self: 'products',
    parent: 'np_class',
    bind: 'np_class_has_products'
  };
  const {formActiveTab, dispatch} = props;
  const tabClick = (e) => {
    e.preventDefault();
    dispatch({ type: FORM_ACTIVE_TAB, data: null });
  }
  useEffect(()=>{}, [formActiveTab]);
  return (
    <Switch>
      {
        typeof locationPathname != 'undefined' &&
        <Route path={locationPathname + '/:method_or_id'}>
          <FormBackGridFixed t={t} />
          <CTabs id="tabs-root" activeTab={formActiveTab ? formActiveTab.tab : 'default-form'}>
            <CNav variant="tabs">
              <TabLink tab="default-form" label="Default form" />
              {
                location.pathname.match(/\/\d+$/) &&
                <>
                  <TabLink tab="class-releation-form" label="class releation form" onClick={tabClick} />
                  {/*<TabLink tab="products-has-attributes-form" label="Products has attributes" />*/}
                  <TabLink tab="assets-form" label="Products assets" onClick={tabClick} />
                  <TabLink tab="products-spec-group-form" label="Products spec group" onClick={tabClick} />
                  <TabLink tab="seo-form" label="SEO form" onClick={tabClick} />
                  <TabLink tab="facebook_tag-form" label="Facebook tags form" onClick={tabClick} />
                </>
              }
            </CNav>
            <ProductsForm href="/admin/products" tab="default-form" />
            {
              location.pathname.match(/\/\d+$/) &&
              <Suspense fallback={<div>Loading...</div>}>
                <ClassRelationForm href="/admin/np_class_has_products" classRelation={classRelation} />
                {/*<ProductsHasAttributesForm table={table} href="/admin/products_has_attributes" />*/}
                <AssetsForm href="/admin/assets" table={table} />
                <ProductsSpecGroupForm
                  href="/admin/products-spec-group"
                  classRelation={{
                    parent: "products_spec_group_attrs",
                    self: "products_spec_group"
                  }}
                />
                <SeoForm href="/admin/seo" table={table} />
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
              <CCardHeader>{t('Products data grid')}</CCardHeader>
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

const mapStateToProps = (state) => {
  return {
    dispatch: state.dispatch,
    formActiveTab: state.formActiveTab
  };
};

//export default Documents;
export default connect(mapStateToProps)(Products);
