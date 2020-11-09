import React, { Suspense, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { Switch, Route } from "react-router";
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
import loadable from '@loadable/component';

import AdminBootstrapTable from './react-bootstrap-tables/AdminBootstrapTable';
import simNavLinkClick from './simNavLinkClick';

const TabLink = loadable(() => import('./form/TabLink'));
const MainClassForm = loadable(() => import('./form/MainClassForm'));
const BannerForm = loadable(() => import('./form/BannerForm'));
const SeoForm = loadable(() => import('./form/SeoForm'));
const FacebookTagsForm = loadable(() => import('./form/FacebookTagsForm'));
const FormBackGridFixed = loadable(() => import('./FormBackGridFixed'));
const ClassRelationForm = loadable(() => import('./form/ClassRelationForm'));

const ClassPart = (props) => {

    const count = 0;
    useEffect(() => {
        //修正階層下拉選單無法開啟的錯誤
        simNavLinkClick();
    }, [count]);

    const { t } = useTranslation(['translation']);
    
    const {classColumns, paginateUrl, paginationOptions, table, clickClearFilter, href, classRelation} = props;
    
    const columns = classColumns(t, 'name');
    //Api位置
    //const paginateUrl = '/api/admin/fp_class';
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
                                (classRelation && location.pathname.match(/\/\d+$/)) && 
                                <>
                                <TabLink tab="class-releation-form" label="class releation form" />
                                </>
                            }
                            {
                                location.pathname.match(/\/\d+$/) &&
                                <>
                                    <TabLink tab="banner-form" label="Banner form" />
                                    <TabLink tab="seo-form" label="SEO form" />
                                    <TabLink tab="facebook_tag-form" label="Facebook tags form" />
                                </>
                            }

                        </CNav>
                        <MainClassForm href={href} />
                        {
                            (classRelation && location.pathname.match(/\/\d+$/)) && 
                                <>
                                <ClassRelationForm classRelation={classRelation} table={table} href={classRelation.href} />
                                </>

                        }
                        {
                            location.pathname.match(/\/\d+$/) &&
                            <Suspense fallback={<div>Loading...</div>}>
                                <BannerForm href="/admin/banner" table={table} />
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
                            <CCardHeader>{t('FpClass data grid')}</CCardHeader>
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

export default ClassPart;