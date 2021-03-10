import React from 'react';

import { useTranslation } from 'react-i18next';
import { Switch, Route } from "react-router";
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
import loadable from '@loadable/component';

import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';
import { couponColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/couponOptions';

const TabLink = loadable(() => import('./components/form/TabLink'));
const CouponForm = loadable(() => import('./components/form/CouponForm'));
const FormBackGridFixed = loadable(() => import('./components/FormBackGridFixed'));

const Coupon = () => {
    const { t } = useTranslation(['translation']);
    const columns = couponColumns(t, 'name');
    const basePath = window.pageConfig.basePath;
    const paginateUrl = (basePath + '/' + SYS_LANG + '/api/admin/coupon').replace(/^\/{2,}/, '/');
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
                        </CNav>
                        <CouponForm href="/admin/coupon" />
                    </CTabs>
                </Route>
            }
            <Route path={locationPathname}>
                <CRow>
                    <CCol>
                        <CCard>
                            <CCardHeader>{t('Coupon data grid')}</CCardHeader>
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

export default Coupon ;