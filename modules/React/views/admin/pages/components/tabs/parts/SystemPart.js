import React, { useState } from 'react';
import { CRow, CCol, CButton, CInputFile } from '@coreui/react'
import { notify } from '../../alertify';
import axios from 'axios';

const SystemPart = (props) => {
    const loadingBackgroundDom = document.getElementById('loading-background');
    const t = props.t;
    const [listsStore, setListsStore] = useState({});
    const onEleChange = (e) => {
        e.preventDefault();
        let store = {};
        const target = e.target;
        const id = target.dataset.id;
        const name = target.name;
        let value = target.value;

        store[id] = { id: id };
        if (target.type == 'file') {
            value = target.files[0];
            let reader = new FileReader();
            const file = target.files[0];
            reader.readAsDataURL(file);
            reader.onload = () => {
                target.previousElementSibling.src = reader.result;
            }
        }
        setListsStore((listsStore) => {
            if (typeof listsStore[id] === 'undefined') {
                listsStore[id] = {};
            }
            listsStore[id][name] = value;
            listsStore[id].sort = 65535;
            return listsStore;
        });
    }


    const onListItemSave = (e) => {
        e.preventDefault();
        const id = e.currentTarget.dataset.id;
        if (listsStore[id]) {
            const data = listsStore[id];
            var formData = new FormData();
            Object.keys(data).forEach((field) => {
                formData.set(field, data[field]);
            });
            formData.set('id', id);
            loadingBackgroundDom.classList.remove('d-none');
            axios({
                method: 'post',
                url: '/api/admin/system_setting',
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                data: formData,
            }).then((response) => {
                const data = response.data.data;
                props.setSystem(data.system);
                loadingBackgroundDom.classList.add('d-none')
                notify('success', t('update success'));
            });
        } else {
            loadingBackgroundDom.classList.add('d-none')
            notify('error', t('Please uploaded image.'), 5);
        }
    }

    return (
        <form method="post" >
            <table className="table table-bordered table-rwd">
                <tbody>
                    {
                        props.system.child && props.system.child.map((item, key) => {
                            return (
                                <tr key={'system_' + key} >
                                    <td className="align-middle" width="250">
                                        <div className="p-1">
                                            <b>{item.name}</b>
                                        </div>
                                    </td>
                                    <td className="align-middle" width="75%">
                                        <div className="p-1">
                                            <input type="hidden" name="id" value={item.id} />
                                            <img src={item.value} className="form-thumbnail-75px" />
                                            <CInputFile
                                                id={'file_' + item.id}
                                                data-id={item.id}
                                                name="value"
                                                accept="image/*"
                                                onChange={onEleChange}
                                            />
                                        </div>
                                    </td>
                                    <td className="align-middle">
                                        <CButton onClick={onListItemSave} color="primary" size="ms" type="button" data-id={item.id}>
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
        </form>

    );
}

export default SystemPart;