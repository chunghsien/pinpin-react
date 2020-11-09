import React from 'react';
import { CButton, CInput, CSelect } from '@coreui/react'
import { notify } from '../../alertify';
import axios from 'axios';

const MailServicePart = (props) => {
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
            url: '/api/admin/system_setting',
            headers: {
                'Content-Type': 'multipart/form-data'
            },
            data: formData,
        }).then((response) => {
            const data = response.data.data;
            props.setMailService(data['mail-service']);
            loadingBackgroundDom.classList.add('d-none')
            notify('success', t('update success'));
        });

    }
    
    const sendMethodChange = (e) => {
        e.preventDefault();
        const value = e.currentTarget.value;
        props.setMailService((mailService) => {
            var cloneObject = Object.assign({}, mailService);
            cloneObject.child[0].value = value;
            return cloneObject;
        });
    }
    
    return (
        <form method="post" >
            <table className="table table-bordered table-rwd">
                <tbody>
                    {
                        props.mailService.child &&
                        <tr>
                            <td className="align-middle">
                                <div className="p-1">
                                    <input type="hidden" name="id" value={props.mailService.child[0].id} />
                                    <input id={props.mailService.child[0].key + '_sort'} type="hidden" name="sort" value={props.mailService.child[0].sort} />
                                    <b>{props.mailService.child[0].name}</b>
                                </div>
                            </td>
                            <td className="align-middle">
                                <CSelect
                                    id={props.mailService.child[0].key}
                                    name="value"
                                    onChange={sendMethodChange}
                                    custom
                                    value={props.mailService.child[0].value}
                                >
                                    <option value="sendmail">{t('sendmail')}</option>
                                    <option value="smtp">{t('smtp')}</option>
                                </CSelect>
                            </td>
                            <td className="align-middle">
                                <CButton
                                    onClick={onListItemSave}
                                    color="primary"
                                    size="ms"
                                    type="button"
                                    data-id={props.mailService.child[0].id}
                                    data-key={props.mailService.child[0].key}
                                >
                                    <i className="fas fa-save mr-1"></i>
                                    <span>{t('form-save')}</span>
                                </CButton>
                            </td>
                        </tr>
                    }
                    {
                        props.mailService.child && props.mailService.child.map((item, key) => {
                            if (key > 0 && props.mailService.child[0].value != 'php_sendmail') {
                                return (
                                    <tr key={item.key} >
                                        <td className="align-middle">
                                            <div className="p-1">
                                                <input type="hidden" name="id" value={item.id} />
                                                <input id={item.key + '_sort'} type="hidden" name="sort" value={item.sort} />
                                                <b>{item.name}</b>
                                            </div>
                                        </td>
                                        <td className="align-middle">
                                            {
                                                item.key == 'ssl' &&
                                                <CSelect
                                                    id={item.key}
                                                    name="value"
                                                    custom
                                                    defaultValue={item.value}
                                                >
                                                    <option value="0">無</option>
                                                    <option value="ssl">ssl</option>
                                                    <option value="tls">tls</option>
                                                </CSelect>

                                            }
                                            {
                                                (item.key == 'from' || item.key == 'host' || item.key == 'username' || item.key == 'password' || item.key == 'port') &&
                                                <CInput id={item.key} name="value" defaultValue={item.value} />
                                            }
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
                            }

                        })
                    }
                </tbody>
            </table>
        </form>

    );
}

export default MailServicePart;