import React from 'react';
import { productsSpecGroupColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/productsSpecGroupOptions';
import loadable from '@loadable/component';
import { useTranslation } from 'react-i18next';
import { Switch, Route } from "react-router";
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';

const TabLink = loadable(() => import('./components/form/TabLink'));
const FormBackGridFixed = loadable(() => import('./components/FormBackGridFixed'));
const ProductsSpecGroupForm = loadable(() => import('./components/form/ProductsSpecGroupForm'));


const ProductsSpecGroup = () => {

    const { t } = useTranslation(['translation']);
    //Api位置
    const paginateUrl = '/api/admin/products_spec_group';
    const pagination = paginationOptions(t);
    const locationPathname = location.pathname.replace(/\/add$/, '').replace(/\/\d+$/, '');
    const columns = productsSpecGroupColumns(t, 'name');
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
                        <ProductsSpecGroupForm />
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

export default ProductsSpecGroup;