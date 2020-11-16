import React, { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { CNav, CTabs } from '@coreui/react'
import loadable from '@loadable/component';
import axios from 'axios';
import SystemSettingDefaultContent from './components/tabs/SystemSettingDefaultContent';
import SystemPart from './components/tabs/parts/SystemPart';
import MailServicePart from './components/tabs/parts/MailServicePart';
import ThirdPartyServicePart from './components/tabs/parts/ThirdPartyServicePart';
import {
    generalSeoColumns,
    siteInfoColumns,
    generalSeoClickClearFilter,
    siteInfoClickClearFilter
} from './options/react-bootstrap-tables/systemSettingOptions';
import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';

const TabLink = loadable(() => import('./components/form/TabLink'));

const SystemSetting = (/*props*/) => {
    const { t } = useTranslation(['translation']);
    const [system, setSystem] = useState({});
    const [mailService, setMailService] = useState({});
    const [thirdPartyService, setThirdPartyService] = useState({});
    const [updateCount, setUpdateCount] = useState(0);
    const loadingBackgroundDom = document.getElementById('loading-background');
    const general_seo_columns = generalSeoColumns(t, 'name');
    const siteinfo_columns = siteInfoColumns(t, 'name');
    
    useEffect(() => {
        loadingBackgroundDom.classList.remove('d-none');
        axios.get('/api/admin/system_setting').then((response) => {
            const data = response.data.data;
            setSystem(data.system);
            setMailService(data['mail-service']);
            setThirdPartyService({
                google_service: data.google_service,
                facebook_dev: data.facebook_dev
            });
            loadingBackgroundDom.classList.add('d-none');
        });
    }, [updateCount]);

    //const { t } = useTranslation(['translation', 'admin-language', 'currencies']);
    return (
        <CTabs id="tabs-root" activeTab="system-form">
            <CNav variant="tabs">
                <TabLink tab="system-form" label={system.name} />
                <TabLink tab="site-info-form" label={t('Site info')} />
                <TabLink tab="general-seo-form" label={t('General SEO')} />
                <TabLink tab="mail-service-form" label={mailService.name} />
                <TabLink tab="third-party-service-form" label={t('Third party service')} />
            </CNav>
            <SystemSettingDefaultContent tab="system-form">
                <SystemPart system={system} setSystem={setSystem} setUpdateCount={setUpdateCount} t={t} />
            </SystemSettingDefaultContent>
            <SystemSettingDefaultContent tab="site-info-form">
                <AdminBootstrapTable
                    isFilterReset
                    clearFilterTrigger={siteInfoClickClearFilter}
                    paginateUrl="/api/admin/system_setting?pk=site_info"
                    columns={siteinfo_columns}
                    translation={t}
                    noOnDelBtn={true}
                />

            </SystemSettingDefaultContent>
            <SystemSettingDefaultContent tab="general-seo-form">
                {<AdminBootstrapTable
                    isFilterReset
                    clearFilterTrigger={generalSeoClickClearFilter}
                    paginateUrl="/api/admin/system_setting?pk=general_seo"
                    columns={general_seo_columns}
                    translation={t}
                    noOnDelBtn={true}
                />
                }
            
            </SystemSettingDefaultContent>
            <SystemSettingDefaultContent tab="mail-service-form">
                <MailServicePart mailService={mailService} setMailService={setMailService} setUpdateCount={setUpdateCount} t={t} />
            </SystemSettingDefaultContent>
            <SystemSettingDefaultContent tab="third-party-service-form">
                <ThirdPartyServicePart thirdPartyService={thirdPartyService} setThirdPartyService={setThirdPartyService} t={t} />
            </SystemSettingDefaultContent>

        </CTabs>
    );
};

export default SystemSetting;