
import React, { useState, useEffect, useRef } from 'react';
import { useTranslation } from 'react-i18next';
import {
    CRow, CCol, CFormGroup, CLabel,
    CCard,
    CInput,
    CInputGroup, CInputGroupAppend, CInputGroupText,
    CTabContent, CTabPane
} from '@coreui/react'
import { useForm } from "react-hook-form";
import Form from '../Form';
import { useParams } from "react-router-dom";

const ProductsSpecIdentifyForm = (props) => {
    const { t } = useTranslation(['translation']);
    const methods = useForm({ mode: 'all' });
    const { register } = methods;
    let href = props.href;
    
    const [remaining, setRemaining] = useState({});
    const [maxLength, setMaxLength] = useState({});
    
    const table = props.table;

    //欄位剩餘字數
    const remainderChange = (e) => {
        let dom = null;
        if (e && typeof e.preventDefault == 'function') {
            e.preventDefault();
            dom = e.target;
        } else {
            dom = e;
        }
        let name = dom.name;
        let tObj = {};
        tObj[name] = dom.value.length;
        //setRemaining({ ...remaining, ...tObj });
        setRemaining((remaining) => ({ ...remaining, ...tObj }));
        return remaining[name];
    }
    const count = 0;
    const {method_or_id}= useParams();
    useEffect(() => {
        formRef.current.elements.forEach((dom) => {
            const name = dom.name;
            const _maxLength = dom.maxLength;
            if (_maxLength && _maxLength > 0) {
                let obj = {};
                obj[name] = _maxLength;
                setMaxLength((maxLength) => ({ ...maxLength, ...obj }));
            }
            if(name == 'products_spec_id') {
                dom.value = method_or_id;
            }
        });
        
        
    }, [count]);

    const formRef = useRef();

    return (
        <>
            <CTabContent>
                <CTabPane data-tab="products-spec-identify-form">
                    <CCard className="tab-card">
                        <Form
                            innerRef={formRef}
                            href={href}
                            griduse
                            {...methods}
                            remainderChange={remainderChange}
                            table={table}
                        >
                            <input type="hidden" name="products_spec_id" ref={register()} />
                            <input type="hidden" name="id" ref={register()} />
                            <CRow>
                                <CCol md="12" sm="12" className="mt-2">
                                    <CFormGroup>
                                        <CLabel>{t('columns-sku')}</CLabel>
                                        <CInputGroup>
                                            <CInput
                                                name="sku"
                                                maxLength="48"
                                                onChange={remainderChange}
                                                innerRef={register()}
                                            />
                                            <CInputGroupAppend>
                                                <CInputGroupText className="bg-light text-muted">{remaining.sku ? remaining.sku : 0}/{maxLength.sku}</CInputGroupText>
                                            </CInputGroupAppend>
                                        </CInputGroup>
                                    </CFormGroup>
                                </CCol>
                            </CRow>

                            <CRow>
                                <CCol md="3" sm="6" className="mt-2">
                                    <CFormGroup>
                                        <CLabel>{t('columns-upc')}</CLabel>
                                        <CInputGroup>
                                            <CInput
                                                name="upc"
                                                maxLength="10"
                                                onChange={remainderChange}
                                                innerRef={register()}
                                            />
                                            <CInputGroupAppend>
                                                <CInputGroupText className="bg-light text-muted">{remaining.upc ? remaining.upc : 0}/{maxLength.upc}</CInputGroupText>
                                            </CInputGroupAppend>
                                        </CInputGroup>
                                    </CFormGroup>
                                </CCol>
                                <CCol md="3" sm="6" className="mt-2">
                                    <CFormGroup>
                                        <CLabel>{t('columns-ean')}</CLabel>
                                        <CInputGroup>
                                            <CInput
                                                name="ean"
                                                maxLength="24"
                                                onChange={remainderChange}
                                                innerRef={register()}
                                            />
                                            <CInputGroupAppend>
                                                <CInputGroupText className="bg-light text-muted">{remaining.ean ? remaining.ean : 0}/{maxLength.ean}</CInputGroupText>
                                            </CInputGroupAppend>
                                        </CInputGroup>
                                    </CFormGroup>
                                </CCol>
                                <CCol md="3" sm="6" className="mt-2">
                                    <CFormGroup>
                                        <CLabel>{t('columns-jan')}</CLabel>
                                        <CInputGroup>
                                            <CInput
                                                name="jan"
                                                maxLength="24"
                                                onChange={remainderChange}
                                                innerRef={register()}
                                            />
                                            <CInputGroupAppend>
                                                <CInputGroupText className="bg-light text-muted">{remaining.jan ? remaining.jan : 0}/{maxLength.jan}</CInputGroupText>
                                            </CInputGroupAppend>
                                        </CInputGroup>
                                    </CFormGroup>
                                </CCol>
                                <CCol md="3" sm="6" className="mt-2">
                                    <CFormGroup>
                                        <CLabel>{t('columns-isbn')}</CLabel>
                                        <CInputGroup>
                                            <CInput
                                                name="isbn"
                                                maxLength="24"
                                                onChange={remainderChange}
                                                innerRef={register()}
                                            />
                                            <CInputGroupAppend>
                                                <CInputGroupText className="bg-light text-muted">{remaining.isbn ? remaining.isbn : 0}/{maxLength.isbn}</CInputGroupText>
                                            </CInputGroupAppend>
                                        </CInputGroup>
                                    </CFormGroup>
                                </CCol>
                            </CRow>

                            <CRow>
                                <CCol md="12" sm="12" className="mt-2">
                                    <CFormGroup>
                                        <CLabel>{t('columns-mpn')}</CLabel>
                                        <CInputGroup>
                                            <CInput
                                                name="mpn"
                                                maxLength="96"
                                                onChange={remainderChange}
                                                innerRef={register()}
                                            />
                                            <CInputGroupAppend>
                                                <CInputGroupText className="bg-light text-muted">{remaining.mpn ? remaining.mpn : 0}/{maxLength.mpn}</CInputGroupText>
                                            </CInputGroupAppend>
                                        </CInputGroup>
                                    </CFormGroup>
                                </CCol>
                            </CRow>
                            
                        </Form>
                    </CCard>
                </CTabPane>
            </CTabContent>
        </>
    );
}

export default ProductsSpecIdentifyForm;