import React from 'react';
import { CButton, CInput } from '@coreui/react'
import { notify } from '../../alertify';
import axios from 'axios';

const ThirdPartyServicePart = (props) => {
    const loadingBackgroundDom = document.getElementById('loading-background');
    const t = props.t;

    const onListItemSave = (e) => {
        e.preventDefault();
        const valueId = e.currentTarget.dataset.key;
        const id = e.currentTarget.dataset.id;
        const value = document.getElementById(valueId).value;
        const sort = document.getElementById(valueId + '_sort').value;
        loadingBackgroundDom.classList.remove('d-none');
        var formData = new FormData();
        formData.set('id', id);
        formData.set('value', value);
        formData.set('sort', sort);
        axios({
            method: 'post',
            url: '/'+SYS_LANG+'/api/admin/system_setting',
            headers: {
                'Content-Type': 'multipart/form-data'
            },
            data: formData,
        }).then((response) => {
            const data = response.data.data;
            props.setThirdPartyService({
                google_service: data.google_service,
                facebook_dev: data.facebook_dev,
            });
            loadingBackgroundDom.classList.add('d-none')
            notify('success', t('update success'));
        });
    }

    return (
        <form method="post" >
            {
                props.thirdPartyService.google_service &&
                <>
                    <h4>{props.thirdPartyService.google_service.name}</h4>
                    <table className="table table-bordered table-rwd">
                        <tbody>
                            {
                                props.thirdPartyService.google_service.child.map((item, key) => {
                                    return (
                                        <tr key={item.key+'_' + key} >
                                            <td className="align-middle" width="250">
                                                <div className="p-1">
                                                    <b>{item.name}</b>
                                                </div>
                                            </td>
                                            <td className="align-middle" width="75%">
                                                <div className="p-1">
                                                    <input id={item.key + '_sort'} type="hidden" name="sort" value={item.sort} />
                                                    <CInput id={item.key} name="value" defaultValue={item.value} />
                                                </div>
                                            </td>
                                            <td className="align-middle">
                                                <CButton
                                                    onClick={onListItemSave}
                                                    color="primary"
                                                    size="ms"
                                                    type="button"
                                                    data-id={item.id}
                                                    data-key={item.key}
                                                >
                                                    <i className="fas fa-save mr-1"></i>
                                                    <span>{t('form-save')}</span>
                                                </CButton>
                                            </td>
                                        </tr>

                                    )
                                })
                            }
                        </tbody>
                    </table>
                </>
            }

            {
                props.thirdPartyService.facebook_dev &&
                <>
                    <h4>{props.thirdPartyService.facebook_dev.name}</h4>
                    <table className="table table-bordered table-rwd">
                        <tbody>
                            {
                                props.thirdPartyService.facebook_dev.child.map((item, key) => {
                                    return (
                                        <tr key={item.key+'_' + key} >
                                            <td className="align-middle" width="250">
                                                <div className="p-1">
                                                    <b>{item.name}</b>
                                                </div>
                                            </td>
                                            <td className="align-middle" width="75%">
                                                <div className="p-1">
                                                    <input id={item.key + '_sort'} type="hidden" name="sort" value={item.sort} />
                                                    <CInput id={item.key} name="value" defaultValue={item.value} />
                                                </div>
                                            </td>
                                            <td className="align-middle">
                                                <CButton
                                                    onClick={onListItemSave}
                                                    color="primary"
                                                    size="ms"
                                                    type="button"
                                                    data-id={item.id}
                                                    data-key={item.key}
                                                >
                                                    <i className="fas fa-save mr-1"></i>
                                                    <span>{t('form-save')}</span>
                                                </CButton>
                                            </td>
                                        </tr>

                                    )
                                })
                            }
                        </tbody>

                    </table>
                </>
            }

        </form>

    );
}

export default ThirdPartyServicePart;