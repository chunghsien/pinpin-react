import React, { Suspense } from 'react';
import { useTranslation } from 'react-i18next';
import { Switch, Route } from "react-router";
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
import loadable from '@loadable/component';

import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';
import { newsColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/newsOptions';

const TabLink = loadable(() => import('./components/form/TabLink'));
const SeoForm = loadable(() => import('./components/form/SeoForm'));
const FacebookTagsForm = loadable(() => import('./components/form/FacebookTagsForm'));
const FormBackGridFixed = loadable(() => import('./components/FormBackGridFixed'));
const NewsForm = loadable(() => import('./components/form/NewsForm'));
const ClassRelationForm = loadable(() => import('./components/form/ClassRelationForm'));

const News = (/*props*/) => {
    const { t } = useTranslation(['translation']);
    const useColumns = newsColumns;
    const columns = useColumns(t, 'name');
    const table = 'news';
    const paginateUrl = '/api/admin/news';
    const pagination = paginationOptions(t);
    const locationPathname = location.pathname.replace(/\/add$/, '').replace(/\/\d+$/, '');

    const classRelation = {
        href: '/admin/nn_class_has_news',
        self: 'news',
        parent: 'nn_class',
        bind: 'nn_class_has_news'
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
                                    <TabLink tab="seo-form" label="SEO form" />
                                    <TabLink tab="facebook_tag-form" label="Facebook tags form" />
                                </>
                            }
                        </CNav>
                        <NewsForm href="/admin/news" />
                        {
                            location.pathname.match(/\/\d+$/) &&
                            <Suspense fallback={<div>Loading...</div>}>
                                <ClassRelationForm href="/admin/nn_class_has_news" classRelation={classRelation} />
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
                            <CCardHeader>{t('News data grid')}</CCardHeader>
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

export default News;