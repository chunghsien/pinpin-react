import React, { useState } from 'react';
import { useTranslation } from 'react-i18next';
import { orderColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/orderOptions';
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
import { Switch, Route } from "react-router";
import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';
import loadable from '@loadable/component';

const TabLink = loadable(() => import('./components/form/TabLink'));
const ContactForm = loadable(() => import('./components/form/ContactForm'));
const FormBackGridFixed = loadable(() => import('./components/FormBackGridFixed'));

const Order = () => {
    const logistics = pageConfig.third_party_service.logistics;
    const { t } = useTranslation(['translation', logistics.manufacturer]);
    const columns = orderColumns(t, 'name');
    const pagination = paginationOptions(t);
    const locationPathname = location.pathname.replace(/\/add$/, '').replace(/\/\d+$/, '');
    const paginateUrl = '/'+SYS_LANG+'/api/admin/order';

    const [formRow, setFormRow] = useState({
        order: {}
    });

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
                        <ContactForm href="/admin/order" formRow={formRow} setFormRow={setFormRow} />
                    </CTabs>
                </Route>
            }
            <Route path={locationPathname}>
                <CRow>
                    <CCol>
                        <CCard>
                            <CCardHeader>{t('Order data grid')}</CCardHeader>
                            <CCardBody>
                                <AdminBootstrapTable
                                    paginateUrl={paginateUrl}
                                    columns={columns}
                                    isSelectRow
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

export default Order;