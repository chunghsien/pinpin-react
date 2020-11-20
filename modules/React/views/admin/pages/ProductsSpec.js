import React, {Suspense} from 'react';
import { productsSpecColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/productsSpecOptions';
import loadable from '@loadable/component';
import { useTranslation } from 'react-i18next';
import { Switch, Route } from "react-router";
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';

const FormBackGridFixed = loadable(() => import('./components/FormBackGridFixed'));
const TabLink = loadable(() => import('./components/form/TabLink'));

const ProductsSpecForm = loadable(() => import('./components/form/ProductsSpecForm'));
const ProductsSpecIdentifyForm = loadable(() => import('./components/form/ProductsSpecIdentifyForm'));
const ProductsSpecVolumeForm = loadable(() => import('./components/form/ProductsSpecVolumeForm'));



const ProductsSpec = () => {

    const { t } = useTranslation(['translation']);
    //Api位置
    const paginateUrl = '/'+SYS_LANG+'/api/admin/products_spec';
    const pagination = paginationOptions(t);
    const locationPathname = location.pathname.replace(/\/add$/, '').replace(/\/\d+$/, '');
    const columns = productsSpecColumns(t, 'name');
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
                                    <TabLink tab="products-spec-identify-form" label="products-spec-identify" />
                                    <TabLink tab="products-spec-volume-form" label="products-spec-volum" />
                                </>
                            }
                            
                        </CNav>
                        <ProductsSpecForm />
                        {
                            location.pathname.match(/\/\d+$/) &&
                            <Suspense fallback={<div>Loading...</div>}>
                                <ProductsSpecIdentifyForm
                                    href="/admin/products_spec_identify"
                                    table="products_spec_identify" 
                                />
                                <ProductsSpecVolumeForm
                                    href="/admin/products_spec_volume"
                                    table="products_spec_volume"
                                />
                            </Suspense>
                        }
                        
                    </CTabs>
                </Route>
            }
            <Route path={locationPathname}>
                <CRow>
                    <CCol>
                        <CCard>
                            <CCardHeader>{t('Products spec data grid')}</CCardHeader>
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

export default ProductsSpec;