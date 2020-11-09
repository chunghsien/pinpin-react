
import React, { useState, useRef } from 'react';
import { useTranslation } from 'react-i18next';
import {
    CRow, CCol, CLabel,
    CCard, CSwitch,
    CTabContent, CTabPane
} from '@coreui/react'

import { useForm } from "react-hook-form";
import Form from '../Form';


const PermissionForm = (props) => {

    const { t } = useTranslation(['translation', 'admin-navigation']);
    const methods = useForm({ mode: 'all' });
    const { register } = methods;
    const matcher = location.pathname.match(/\/\d+$/);
    let href = props.href;
    if (location.pathname.match(/\/\d+$/)) {
        href = href.replace(/\/$/, '') + matcher[0];
    } else {
        href += 'add';
    }
    let self_id_value = '';
    if (matcher && matcher[0]) {
        self_id_value = matcher[0].replace(/^\//, '');
    }

    const [reactSelectOptions, setReactSelectOptions] = useState({
        switchValues: {
            permission: {}
        },
        switchDefaultValues: {
            permission: {}
        }
    });

    const handleSwitchChange = (e) => {
        let permission = reactSelectOptions.switchValues.permission;
        let target = e.target;
        let permission_id = target.value;
        permission[permission_id] = target.checked === true ? 1 : 0;
        //console.log(permission[permission_id]);
        setReactSelectOptions({
            ...reactSelectOptions,
            switchValues: {
                permission: permission
            }
        });
    }

    const formRef = useRef();
    //const 'permission_id' = parent + '_id';
    const permissions = pageConfig.admin_permissions;
    const renderPermissionLevel2 = (pages, key) => {
        //console.log(reactSelectOptions);
        return pages.map((item, key2) => {
            let uri = item.uri;
            const switcher = reactSelectOptions.switchValues.permission;
            let permission_id = permissions[uri];
            return (
                <CCol md="4" sm="6" xs="12" className="mt-2" key={key + '-' + key2}>
                    <CSwitch
                        checked={switcher[permission_id] === 1}
                        onChange={handleSwitchChange}
                        innerRef={register()}
                        color="info"
                        name="permission_id[]"
                        value={permission_id}
                    />
                    <CLabel className="align-top ml-2">{t('admin-navigation:' + item.name)}</CLabel>
                </CCol>
            )
        });
    }
    const renderPermission = (item, key) => {
        //const permissions = pageConfig.admin_permissions;
        const switcher = reactSelectOptions.switchValues.permission;
        if (item.uri != '#') {
            let uri = item.uri;
            let permission_id = permissions[uri];
            return (
                <CCol md="4" sm="6" xs="12" className="mt-2" key={'permission_' + key}>
                    <CSwitch
                        checked={switcher[permission_id] === 1}
                        onChange={handleSwitchChange}
                        innerRef={register()}
                        color="info"
                        name="permission_id[]"
                        value={permission_id}
                    />
                    <CLabel className="align-top ml-2">{t('admin-navigation:' + item.name)}</CLabel>
                </CCol>
            );
        } else {
            if (item.tag == 'CSidebarNavTitle') {
                return (
                    <CCol xl="12" className="mt-2" key={'permission_' + key}>
                        <h5 className="font-weight-bold">{t('admin-navigation:' + item.name)}</h5>
                        <hr />
                    </CCol>
                );
            } else {
                if (item.pages) {
                    return (
                        <CCol xl="12" className="mt-2" key={'permission_' + key}>
                            <h6 className="font-weight-bold">{t('admin-navigation:' + item.name)}</h6>
                            <CRow>
                                {renderPermissionLevel2(item.pages, key)}
                            </CRow>
                        </CCol>
                    );
                }
            }
            return '';
        }
    }

    const processPermission = () => {
        return pageConfig.admin_permission_status.map((item, key) => renderPermission(item, key));
    }
    return (
        <CTabContent>
            <CTabPane data-tab="class-releation-form">
                <CCard className="tab-card">
                    <Form
                        innerRef={formRef}
                        griduse
                        {...methods}
                        {...props}
                        setReactSelectOptions={setReactSelectOptions}
                    >
                        <input type="hidden" name="roles_id" ref={register()} value={self_id_value} />
                        <CRow>
                            <CCol xl="12" className="mt-2">
                                <h4 className="font-weight-bold">{t('permission select')}</h4>
                                <hr />
                            </CCol>
                            {processPermission()}
                        </CRow>
                    </Form>
                </CCard>
            </CTabPane>
        </CTabContent>
    );
}

export default PermissionForm;