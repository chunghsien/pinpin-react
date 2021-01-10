import React from 'react';
import { useForm } from "react-form";

const ReactForm = (porps) => {
    const {
        Form,
        meta: { isSubmitting, canSubmit }
    } = useForm({
        onSubmit: async (values, instance) => {
            //console.log(values);
            //console.log(instance);
            // onSubmit (and everything else in React Form)
            // has async support out-of-the-box
            //await sendToFakeServer(values);
            //console.log("Huzzah!");
        },
        debugForm: (typeof porps.debug != 'undefined' && porps.debug) ? true : false
    });

    return (
        <>
            <CRow className="mb-1">
                <div className="col">
                    <Link className="btn btn-info btn-sm float-right" to={props.backlist.uri} onClick={props.backlist.onBackListClcik}>
                        <i className="fas fa-undo-alt mr-1" />
                        <span>{t('Return grid list')}</span>
                    </Link>
                </div>
            </CRow>

        </>
    );
}