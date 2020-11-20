import React from 'react';
import { useTranslation } from 'react-i18next';
import { CNav, CTabs } from '@coreui/react'
import loadable from '@loadable/component';

import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';
//import simNavLinkClick from './components/simNavLinkClick';
import { currenciesColumns, clickClearFilter } from './options/react-bootstrap-tables/currenciesOptions';
import CommonMainContent from './components/tabs/CommonMainContent';

const TabLink = loadable(() => import('./components/form/TabLink'));

const Currencies = (/*props*/) => {

    const { t } = useTranslation(['translation', 'admin-language', 'currencies']);
    const currencies_columns = currenciesColumns(t, 'name');
    return (
        <CTabs id="tabs-root" activeTab="default-form">
            <CNav variant="tabs">
                <TabLink tab="default-form" label="Default form" />
            </CNav>
            <CommonMainContent>
                <AdminBootstrapTable
                    isFilterReset
                    clearFilterTrigger={clickClearFilter}
                    paginateUrl={'/'+SYS_LANG+'/api/admin/currencies'}
                    columns={currencies_columns}
                    translation={t}
                    noOnDelBtn={true}
                />
            </CommonMainContent>
        </CTabs>
    );
};

export default Currencies;