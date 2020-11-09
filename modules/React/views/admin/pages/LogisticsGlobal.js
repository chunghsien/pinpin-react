import React from 'react';
import { useTranslation } from 'react-i18next';
import { CNav, CTabs } from '@coreui/react'
import loadable from '@loadable/component';

import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';
//import simNavLinkClick from './components/simNavLinkClick';
import { logisticsGlobalColumns, logisticsGlobalClickClearFilter } from './options/react-bootstrap-tables/logisticsGlobalOptions';
import { shippingColumns, shippingClickClearFilter } from './options/react-bootstrap-tables/shippingOptions';
import { paymentColumns, paymentClickClearFilter } from './options/react-bootstrap-tables/paymentOptions';
import { otherLogisticsColumns, otherLogisticsClickClearFilter } from './options/react-bootstrap-tables/otherLogisticsOptions';
import {freeShippingColumns}  from './options/react-bootstrap-tables/freeShippingOptions';

import CommonMainContent from './components/tabs/CommonMainContent';
import ShippingContent from './components/tabs/ShippingContent';
import PaymentContent from './components/tabs/PaymentContent';
import OtherLogisticsContent from './components/tabs/OtherLogisticsContent';
import FreeShippingContent from './components/tabs/FreeShippingContent';


const TabLink = loadable(() => import('./components/form/TabLink'));

const LogisticsGlobal = (/*props*/) => {

    const { t } = useTranslation(['translation', 'admin-language']);
    //const useColumns = logisticsGlobalColumns;
    const logistics_global_columns = logisticsGlobalColumns(t, 'name');
    const shipping_columns = shippingColumns(t, 'name');
    const payment_columns = paymentColumns(t, 'name');
    const other_logistics_columns = otherLogisticsColumns(t, 'name');
    const free_shipping_columns = freeShippingColumns(t, 'name');
    return (
        <CTabs id="tabs-root" activeTab="default-form">
            <CNav variant="tabs">
                <TabLink tab="default-form" label="Default form" />
                <TabLink tab="shipping-form" label="Shipping form" />
                <TabLink tab="payment-form" label="Payment form" />
                <TabLink tab="other-logistics-form" label="Other logistics form" />
                <TabLink tab="free-shipping-form" label="Free shipping form" />
            </CNav>
            <CommonMainContent>
                <AdminBootstrapTable
                    isFilterReset
                    clearFilterTrigger={logisticsGlobalClickClearFilter}
                    paginateUrl="/api/admin/system_settings/logistics"
                    columns={logistics_global_columns}
                    isFilterReset
                    translation={t}
                    noOnDelBtn={true}
                />
            </CommonMainContent>
            <ShippingContent>
                <AdminBootstrapTable
                    isFilterReset
                    clearFilterTrigger={shippingClickClearFilter}
                    paginateUrl="/api/admin/shipping"
                    columns={shipping_columns}
                    translation={t}
                    noOnDelBtn={true}
                />
            </ShippingContent>
            <PaymentContent>
                <AdminBootstrapTable
                    isFilterReset
                    clearFilterTrigger={paymentClickClearFilter}
                    paginateUrl="/api/admin/payment"
                    columns={payment_columns}
                    translation={t}
                    noOnDelBtn={true}
                />
            </PaymentContent>
            <OtherLogisticsContent>
                <AdminBootstrapTable
                    isFilterReset
                    clearFilterTrigger={otherLogisticsClickClearFilter}
                    paginateUrl="/api/admin/other_logistics"
                    columns={other_logistics_columns}
                    translation={t}
                    noOnDelBtn={true}
                />
            </OtherLogisticsContent>
            <FreeShippingContent>
                <AdminBootstrapTable
                    paginateUrl="/api/admin/free_shipping"
                    columns={free_shipping_columns}
                    translation={t}
                    noOnDelBtn={true}
                />
            </FreeShippingContent>

        </CTabs>
    );
};

export default LogisticsGlobal;