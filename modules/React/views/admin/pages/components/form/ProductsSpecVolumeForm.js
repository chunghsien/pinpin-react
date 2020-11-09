
import React, { useState, useEffect, useRef } from 'react';
import { useTranslation } from 'react-i18next';
import {
    CRow, CCol, CLabel,
    CCard,
    CInput, CSelect,
    CTabContent, CTabPane
} from '@coreui/react'
import { useForm } from "react-hook-form";
import Form from '../Form';
import { useParams } from "react-router-dom";

const ProductsSpecVolumeForm = (props) => {
    const { t } = useTranslation(['translation']);
    const methods = useForm({ mode: 'all' });
    const { register } = methods;
    let href = props.href;

    const table = props.table;

    const count = 0;
    const { method_or_id } = useParams();

    const [units, setUnits] = useState({
        dimensions_unit: [],
        weight_unit: [],
        volume_unit: [],
    });

    useEffect(() => {
        formRef.current.elements.forEach((dom) => {
            const name = dom.name;
            if (name == 'products_spec_id') {
                dom.value = method_or_id;
            }
        });
        var request = new XMLHttpRequest();
        request.open('GET', '/api/admin/products_spec_volume/getUnit', false);
        request.send();
        const unitContainer = JSON.parse(request.responseText).data/*.options.stock_status*/;
        setUnits(unitContainer);

    }, [count]);

    const formRef = useRef();

    return (
        <>
            <CTabContent>
                <CTabPane data-tab="products-spec-volume-form">
                    <CCard className="tab-card">
                        <Form
                            innerRef={formRef}
                            href={href}
                            griduse
                            {...methods}
                            table={table}
                        >
                            <input type="hidden" name="products_spec_id" ref={register()} />
                            <input type="hidden" name="id" ref={register()} />
                            <CRow>
                                <CCol md="3" sm="12" className="mt-2">
                                    <CLabel>{t('columns-width')}</CLabel>
                                    <CInput
                                        type="number"
                                        name="width"
                                        innerRef={register()}
                                    />
                                </CCol>
                                <CCol md="3" sm="12" className="mt-2">
                                    <CLabel>{t('columns-height')}</CLabel>
                                    <CInput
                                        type="number"
                                        name="height"
                                        innerRef={register()}
                                    />
                                </CCol>
                                <CCol md="3" sm="12" className="mt-2">
                                    <CLabel>{t('columns-distance')}</CLabel>
                                    <CInput
                                        type="number"
                                        name="distance"
                                        innerRef={register()}
                                    />
                                </CCol>
                                <CCol md="3" sm="12" className="mt-2">
                                    <CLabel>{t('columns-dimensions_unit')}</CLabel>
                                    <CSelect innerRef={register()} name="dimensions_unit" custom>
                                        <option value="">{t("no options")}</option>
                                        {
                                            units.dimensions_unit.map((item, key) => {
                                                return (
                                                    <option key={'dimensions_unit_' + key} value={item}>
                                                        {t(item)}
                                                    </option>
                                                );
                                            })
                                        }
                                    </CSelect>
                                </CCol>
                            </CRow>
                            <CRow>
                                <CCol md="6" sm="12" className="mt-2">
                                    <CLabel>{t('columns-weight')}</CLabel>
                                    <CInput
                                        type="number"
                                        name="weight"
                                        innerRef={register()}
                                    />
                                </CCol>
                                <CCol md="6" sm="12" className="mt-2">
                                    <CLabel>{t('columns-weight_unit')}</CLabel>
                                    <CSelect innerRef={register()} name="weight_unit" custom>
                                        <option value="">{t("no options")}</option>
                                        {
                                            units.weight_unit.map((item, key) => {
                                                return (
                                                    <option key={'volume_unit' + key} value={item}>
                                                        {t(item)}
                                                    </option>
                                                );
                                            })
                                        }
                                    </CSelect>
                                </CCol>

                            </CRow>
                            <CRow>
                                <CCol md="6" sm="12" className="mt-2">
                                    <CLabel>{t('columns-volume')}</CLabel>
                                    <CInput
                                        type="number"
                                        name="volume"
                                        innerRef={register()}
                                    />
                                </CCol>
                                <CCol md="6" sm="12" className="mt-2">
                                    <CLabel>{t('columns-volume_unit')}</CLabel>
                                    <CSelect innerRef={register()} name="volume_unit" custom>
                                        <option value="">{t("no options")}</option>
                                        {
                                            units.volume_unit.map((item, key) => {
                                                return (
                                                    <option key={'volume_unit' + key} value={item}>
                                                        {t(item)}
                                                    </option>
                                                );
                                            })
                                        }
                                    </CSelect>
                                </CCol>

                            </CRow>
                        </Form>
                    </CCard>
                </CTabPane>
            </CTabContent>
        </>
    );
}

export default ProductsSpecVolumeForm;