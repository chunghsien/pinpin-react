
import React, { useState, useEffect, useRef, Fragment } from 'react';
import { useTranslation } from 'react-i18next';
import {
    CRow, CCol, CLabel,
    CCard,
    CInputCheckbox,
    CImg,
    CTabContent, CTabPane
} from '@coreui/react'
import { useForm, useFieldArray } from "react-hook-form";
import Form from '../Form';

const ProductsHasAttributesForm = (props) => {
    const { t } = useTranslation(['translation']);
    const methods = useForm({ mode: 'all' });
    const { register } = methods;
    let href = props.href;
    const products_id = location.pathname.match(/\/\d+$/)[0].replace(/^\//, '');

    const [formLists, setFormLists] = useState({
        products_has_attributes: [],
    });

    const count = 0;
    useEffect(() => {

    }, [count]);

    const formRef = useRef();

    return (
        <>
            <CTabContent>
                <CTabPane data-tab="products-has-attributes-form">
                    <CCard className="tab-card">
                        <Form notReset={1} innerRef={formRef} formLists={formLists} setFormLists={setFormLists} href={href} griduse {...methods} classRelation={{ self: 'products' }} >
                            <input type="hidden" name="products_id" defaultValue={products_id} ref={register()} />
                            {
                                formLists.products_has_attributes.map((item, key) => {
                                    let checkboxs = '';
                                    if (item.child.length > 0) {
                                        checkboxs = item.child.map((citem, ckey) => {
                                            return (
                                                <Fragment key={'checkbox_' + ckey}>
                                                    <div className="custom-control custom-checkbox custom-control-inline attributes-checkbox">
                                                        <CInputCheckbox
                                                            id={'attributes_id_' + citem.id}
                                                            custom
                                                            name={`attributes_id[${ckey}]`}
                                                            defaultValue={citem.id}
                                                            defaultChecked={citem.checked == 1}
                                                            innerRef={register()}
                                                        />
                                                        <CLabel htmlFor={'attributes_id_' + citem.id} className="custom-control-label">{citem.name}</CLabel>
                                                    </div>
                                                    <CLabel className="mr-2">
                                                        <CImg src={citem.photo} thumbnail width="40" />
                                                    </CLabel>
                                                </Fragment>
                                            );
                                        });
                                    }
                                    let control_inline = (
                                        <CCol>
                                            <h3>{item.name}</h3>
                                            {checkboxs}
                                        </CCol>
                                    );
                                    let crow = (
                                        <CRow key={'checkbox_row_' + key}>{control_inline}</CRow>
                                    );

                                    if (key > 0 && key < formLists.products_has_attributes.length) {
                                        return (
                                            <Fragment key={'fregment_' + key}>
                                                {crow}
                                                <hr key={'hr' + key} />
                                            </Fragment>
                                        );
                                    } else {
                                        return crow;
                                    }
                                })
                            }
                        </Form>
                    </CCard>
                </CTabPane>
            </CTabContent>
        </>
    );
}

export default ProductsHasAttributesForm;