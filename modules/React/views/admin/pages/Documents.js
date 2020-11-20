import React, { Suspense } from 'react';
import { useTranslation } from 'react-i18next';
import { Switch, Route } from "react-router";
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
import loadable from '@loadable/component';

import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';
import { documentsColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/documentsOptions';

const TabLink = loadable(() => import('./components/form/TabLink'));
const SeoForm = loadable(() => import('./components/form/SeoForm'));
const FacebookTagsForm = loadable(() => import('./components/form/FacebookTagsForm'));
const FormBackGridFixed = loadable(() => import('./components/FormBackGridFixed'));
const BannerForm = loadable(() => import('./components/form/BannerForm'));
//const DocumentsForm = loadable(() => import('./components/form/DocumentsForm'));
//const DocumentsContentForm = loadable(() => import('./components/form/DocumentsContentForm'));

const Documents = (/*props*/) => {

    const { t } = useTranslation(['translation']);
    const table = "documents";
    const useColumns = documentsColumns;
    const columns = useColumns(t, 'name');
    const paginateUrl = '/'+SYS_LANG+'/api/admin/documents';
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
                            <TabLink tab="default-form" label="Banner form" />
                            {
                                location.pathname.match(/\/\d+$/) &&
                                <>
                                    {/*<TabLink tab="documents-content-form" label="documents content form" />*/}
                                    <TabLink tab="seo-form" label="SEO form" />
                                    <TabLink tab="facebook_tag-form" label="Facebook tags form" />
                                </>
                            }
                        </CNav>
                        {/*<DocumentsForm href="/admin/documents" />*/}
                        <BannerForm href="/admin/banner" table={table} tab="default-form" />
                        {
                            location.pathname.match(/\/\d+$/) &&
                            <Suspense fallback={<div>Loading...</div>}>
                                {/*<DocumentsContentForm href="/admin/documents_content" table={table} />*/}
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

export default Documents;