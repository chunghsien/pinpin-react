import React, { Suspense } from 'react';
import { useTranslation } from 'react-i18next';
import { Switch, Route } from "react-router";
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
import loadable from '@loadable/component';

import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';
//import simNavLinkClick from './components/simNavLinkClick';
import { productsColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/productsOptions';

const TabLink = loadable(() => import('./components/form/TabLink'));
const SeoForm = loadable(() => import('./components/form/SeoForm'));
const FacebookTagsForm = loadable(() => import('./components/form/FacebookTagsForm'));
const FormBackGridFixed = loadable(() => import('./components/FormBackGridFixed'));
const ProductsForm = loadable(() => import('./components/form/ProductsForm'));
const ClassRelationForm = loadable(() => import('./components/form/ClassRelationForm'));
const AssetsForm = loadable(() => import('./components/form/AssetsForm'));
const ProductsHasAttributesForm = loadable(() => import('./components/form/ProductsHasAttributesForm'));


const Products = (/*props*/) => {

    const { t } = useTranslation(['translation']);
    const useColumns = productsColumns;
    const columns = useColumns(t, 'name');
    const table = 'products';
    const paginateUrl = '/'+SYS_LANG+'/api/admin/products';
    const pagination = paginationOptions(t);
    const locationPathname = location.pathname.replace(/\/add$/, '').replace(/\/\d+$/, '');

    const classRelation = {
        href: '/'+SYS_LANG+'/admin/np_class_has_products',
        self: 'products',
        parent: 'np_class',
        bind: 'np_class_has_products'
    };
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
                                    <TabLink tab="class-releation-form" label="class releation form" />
                                    <TabLink tab="products-has-attributes-form" label="Products has attributes" />
                                    <TabLink tab="assets-form" label="Products assets" />
                                    <TabLink tab="seo-form" label="SEO form" />
                                    <TabLink tab="facebook_tag-form" label="Facebook tags form" />
                                </>
                            }
                        </CNav>
                        <ProductsForm href="/admin/products" />
                        {
                            location.pathname.match(/\/\d+$/) &&
                            <Suspense fallback={<div>Loading...</div>}>
                                <ClassRelationForm href="/admin/np_class_has_products" classRelation={classRelation} />
                                <ProductsHasAttributesForm table={table} href="/admin/products_has_attributes" />
                                <AssetsForm href="/admin/assets" table={table} />
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

export default Products;