import React, { useState } from 'react';
import { useTranslation } from 'react-i18next';
import { contactColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/contactOptions';
import { CRow, CCol, CCard, CCardBody, CCardHeader, CNav, CTabs } from '@coreui/react'
import { Switch, Route } from "react-router";
import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';
import loadable from '@loadable/component';

const TabLink = loadable(() => import('./components/form/TabLink'));
const ContactForm = loadable(() => import('./components/form/ContactForm'));
const FormBackGridFixed = loadable(() => import('./components/FormBackGridFixed'));

const Contact = () => {

    const { t } = useTranslation(['translation']);
    const columns = contactColumns(t, 'name');
    const pagination = paginationOptions(t);
    const locationPathname = location.pathname.replace(/\/add$/, '').replace(/\/\d+$/, '');
    const paginateUrl = '/'+SYS_LANG+'/api/admin/contact';

    const [formRow, setFormRow] = useState({
        contact: {
            id: null,
            language_id: 0,
            locale_id: 0,
            full_name: '',
            email: '',
            subject: '',
            commet: '',
            reply: '',
            is_reply: 0,
        }
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
                        <ContactForm href="/admin/contact" formRow={formRow} setFormRow={setFormRow} />
                    </CTabs>
                </Route>
            }
            <Route path={locationPathname}>
                <CRow>
                    <CCol>
                        <CCard>
                            <CCardHeader>{t('Contact data grid')}</CCardHeader>
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

export default Contact;