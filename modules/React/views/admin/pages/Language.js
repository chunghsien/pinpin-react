import React from 'react';
import { CRow, CCol, CCard, CCardBody, CCardHeader } from '@coreui/react'
import { useTranslation } from 'react-i18next';
import { languageColumns, paginationOptions, clickClearFilter } from './options/react-bootstrap-tables/languageOptions';

import AdminBootstrapTable from './components/react-bootstrap-tables/AdminBootstrapTable';

const Language = () => {

  const { t } = useTranslation(['translation', 'admin-language']);
  const columns = languageColumns(t, 'display_name');
  //Api位置
  const basePath = window.pageConfig.basePath;
  const paginateUrl = (basePath + '/' + SYS_LANG + '/api/admin/language').replace(/^\/{2,}/, '/');
  const pagination = paginationOptions(t);
  return (
    <>
      <CRow>
        <CCol>
          <CCard>
            <CCardHeader>{t('Language data grid')}</CCardHeader>
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
    </>
  );
};

export default Language;