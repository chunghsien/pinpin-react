import React, { Suspense, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { Switch, Route } from "react-router";
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
import loadable from '@loadable/component';

import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';
//import simNavLinkClick from './components/simNavLinkClick';
import { productsAttrsColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/productsAttrsOptions';

const FormBackGridFixed = loadable(() => import('./components/FormBackGridFixed'));
const TabLink = loadable(() => import('./components/form/TabLink'));
const ProductsAttrsForm = loadable(() => import('./components/form/ProductsAttrsForm'));
const ProductsAttrsChildForm = loadable(() => import('./components/form/ProductsAttrsChildForm'));

const ProductsAttrs = (/*props*/) => {

    const { t } = useTranslation(['translation']);
    const useColumns = productsAttrsColumns;
    const columns = useColumns(t, 'name');
    //const table = 'attributes';
    const paginateUrl = '/api/admin/products_attrs';
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
                            {
                                location.pathname.match(/\/\d+$/) &&
                                <>
                                    <TabLink tab="products-attrs-child-form" label="sub-attr" />
                                </>
                            }
                        </CNav>
                        <ProductsAttrsForm href="/admin/products_attrs" />
                        {
                            location.pathname.match(/\/\d+$/) &&
                            <Suspense fallback={<div>Loading...</div>}>
                                <ProductsAttrsChildForm href="/admin/products_attrs/products_attrs_parent" />
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

export default ProductsAttrs;