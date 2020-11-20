import React, { useEffect } from 'react';
import { memberListColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/memberListOptions';
//import loadable from '@loadable/component';
import { useTranslation } from 'react-i18next';
import { Switch, Route } from "react-router";
import { CRow, CCol, CCard, CCardBody, CCardHeader, CTabs, CNav } from '@coreui/react'
import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';
import simNavLinkClick from './components/simNavLinkClick';
import loadable from '@loadable/component';

const TabLink = loadable(() => import('./components/form/TabLink'));
const MemberForm = loadable(() => import('./components/form/MemberForm'));
const FormBackGridFixed = loadable(() => import('./components/FormBackGridFixed'));

const MemberList = () => {

    const count = 0;
    useEffect(() => {
        //修正階層下拉選單無法開啟的錯誤
        simNavLinkClick();
    }, [count]);

    const { t } = useTranslation(['translation']);
    //Api位置
    const paginateUrl = '/'+SYS_LANG+'/api/admin/member_list';
    const pagination = paginationOptions(t);
    const locationPathname = location.pathname.replace(/\/add$/, '').replace(/\/\d+$/, '');
    const columns = memberListColumns(t, 'name');
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
                        <MemberForm href="/admin/member_list" />
                    </CTabs>
                </Route>
            }
        
            <Route path={locationPathname}>
                <CRow>
                    <CCol>
                        <CCard>
                            <CCardHeader>會員列表</CCardHeader>
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

export default MemberList;