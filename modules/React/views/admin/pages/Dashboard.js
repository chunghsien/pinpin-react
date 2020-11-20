import React, { useEffect } from 'react';
import {
    CCard,
    CCardBody,
    CCardHeader,
    CCol,
    CRow,
} from '@coreui/react'
import axios from 'axios';
import { useTranslation } from 'react-i18next';

const Dashboard = () => {
    const count = 0;
    useEffect(() => {
        function getApi() {
            axios.get('/'+SYS_LANG+'/api/admin/dashboard').then(function(response) {
                const data = response.data.data;
                document.getElementById('php_os').innerText = data.PHP_OS;
                document.getElementById('server_software').innerText = data.SERVER_SOFTWARE;
                document.getElementById('db_ver').innerText = data.DB_VER;
                document.getElementById('php_ver').innerText = data.PHP_VERSION;
                document.getElementById('today_registed').innerText = data.today_registed;
                document.getElementById('total_registed').innerText = data.total_registed;
                document.getElementById('today_ordered').innerText = data.today_ordered;
                document.getElementById('total_ordered').innerText = data.total_ordered;
            }).catch(function(error) {
                console.log(error);
            });
        }
        getApi();
    }, [count]);
    const { t } = useTranslation(['translation', 'admin-dashboard']);
    return (
        <>
            <CRow>
                <CCol sm="6" lg="4">
                    <CCard className="text-white bg-gradient-primary">
                        <CCardBody className="pb-0 d-flex justify-content-between pb-4">
                            <div>
                                <div className="float-left mr-3">
                                    <i className="fas fa-users fa-4x" />
                                </div>
                                <div className="float-left">
                                    <div className="text-value-lg">
                                        <span id="today_registed"></span>
                                        <span>&nbsp;/&nbsp;</span>
                                        <span id="total_registed"></span>
                                    </div>
                                    <div>{t('admin-dashboard:Number of registered members (today / total)')}</div>
                                </div>
                            </div>
                        </CCardBody>
                    </CCard>
                </CCol>
                <CCol sm="6" lg="4">
                    <CCard className="text-white bg-gradient-warning">
                        <CCardBody className="pb-0 d-flex justify-content-between pb-4">
                            <div>
                                <div className="float-left mr-3">
                                    <i className="fas fa-user-clock fa-4x" />
                                </div>
                                <div className="float-left">
                                    <div className="text-value-lg" id="onlines">9999</div>
                                    <div>{t('admin-dashboard:Onlines (Data source%3a Google analytics)')}</div>
                                </div>
                            </div>
                        </CCardBody>
                    </CCard>
                </CCol>
                <CCol sm="6" lg="4">
                    <CCard className="text-white bg-gradient-info">
                        <CCardBody className="pb-0 d-flex justify-content-between pb-4">
                            <div>
                                <div className="float-left mr-3">
                                    <i className="fas fa-file-invoice-dollar fa-4x" />
                                </div>
                                <div className="float-left">
                                    <div className="text-value-lg">
                                        <span id="today_ordered"></span>
                                        <span>&nbsp;/&nbsp;</span>
                                        <span id="total_ordered"></span>
                                    </div>
                                    <div>{t('admin-dashboard:Quantity of order(today / total)')}</div>
                                </div>
                            </div>
                        </CCardBody>
                    </CCard>
                </CCol>
            </CRow>
            <CRow>
                <CCol>
                    <CCard>
                        <CCardBody>
                            <CRow>
                                <CCol md="6" xs="12">
                                    <h4 className="card-title">{t('admin-dashboard:Google Analytics')}</h4>
                                </CCol>
                                <CCol md="6" xs="12"></CCol>
                            </CRow>
                            <CRow>
                                <CCol>Chart 內容</CCol>
                            </CRow>
                        </CCardBody>
                    </CCard>
                </CCol>
            </CRow>
            <CRow>
                <CCol>
                    <CCard>
                        <CCardHeader>{t('admin-dashboard:Server information&#4')}</CCardHeader>
                        <CCardBody>
                            <table className="table table-hover table-outline mb-0 d-none d-sm-table">
                                <tbody>
                                    <tr>
                                        <th>{t('admin-dashboard:OS&#46')}</th>
                                        <td id="php_os"></td>
                                    </tr>
                                    <tr>
                                        <th>{t('admin-dashboard:Http server')}</th>
                                        <td id="server_software"></td>
                                    </tr>
                                    <tr>
                                        <th>{t('admin-dashboard:Database')}</th>
                                        <td id="db_ver"></td>
                                    </tr>
                                    <tr>
                                        <th>{t('admin-dashboard:Programming language')}</th>
                                        <td>PHP <span id="php_ver"></span></td>
                                    </tr>

                                </tbody>
                            </table>
                        </CCardBody>
                    </CCard>
                </CCol>
            </CRow>
        </>)
}

export default Dashboard
