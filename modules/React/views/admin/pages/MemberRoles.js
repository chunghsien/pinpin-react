import React, { useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { Switch, Route } from "react-router";
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
import loadable from '@loadable/component';

import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';

import { memberRolesColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/memberRolesOptions';
import simNavLinkClick from './components/simNavLinkClick';

const TabLink = loadable(() => import('./components/form/TabLink'));
const MemberRolesForm = loadable(() => import('./components/form/MemberRolesForm'));
const FormBackGridFixed = loadable(() => import('./components/FormBackGridFixed'));

const MemberRoles = () => {
    const { t } = useTranslation(['translation']);
    const useColumns = memberRolesColumns;
    const columns = useColumns(t, 'name');
    
    const pagination = paginationOptions(t);
    const locationPathname = location.pathname.replace(/\/add$/, '').replace(/\/\d+$/, '');
    const paginateUrl = '/api/admin/member_roles';
    //const table = 'manufactures';
    const count = 0;
    useEffect(() => {
        //修正階層下拉選單無法開啟的錯誤
        simNavLinkClick();
    }, [count]);

    
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
                        <MemberRolesForm href="/admin/member_roles" />
                    </CTabs>
                </Route>
            }
            <Route path={locationPathname}>
                <CRow>
                    <CCol>
                        <CCard>
                            <CCardHeader>{t('member roles list')}</CCardHeader>
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

export default MemberRoles;