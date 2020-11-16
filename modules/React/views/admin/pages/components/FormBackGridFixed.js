import React, { useEffect } from 'react';
import { Link } from "react-router-dom";
import { CRow } from '@coreui/react';

const FormBackGridFixed = (props) => {
    const locationPathname = location.pathname.replace(/\/add$/, '').replace(/\/\d+$/, '');
    const t = props.t;
    const backListUri = location.pathname.replace(/\/\w+$/, '').replace(/\/\d+$/, '');
    const regexp = new RegExp(backListUri, 'i');
    const navLinkElements = document.getElementsByClassName('c-sidebar-nav-link');
    var toLink = 0;

    const formNavLinkFixed = () => {
        navLinkElements.forEach((element) => {
            if (regexp.test(element.href)) {
                let parentElement = element.parentElement;
                let className = parentElement.className;
                if (!element.className.match(/c-active/)) {
                    var _class = element.className;
                    _class += ' c-active';
                    element.className = _class;
                }
                if (!className.match(/c-active/)) {
                    className += ' c-active';
                    parentElement.className = className;
                    var grandElement = parentElement.parentElement;
                    var parentUntilLastElement = grandElement.parentElement;
                    var parentUntilLastClassname = parentUntilLastElement.className;
                    parentUntilLastElement.className = parentUntilLastClassname += ' c-show';
                }
                return;
            }
        });

    }
    const backGridFixed = () => {
        navLinkElements.forEach((element) => {
            if (regexp.test(element.href)) {
                if (!toLink) {
                    var _class = element.className.replace(/\s?c-active/, '');
                    element.className = _class;
                } else {
                    var _class = element.className + ' c-active';
                    element.className = _class;
                }
                return;
            }
        });

    }

    useEffect(() => {
        formNavLinkFixed(regexp, navLinkElements);
        return () => {
            backGridFixed();
        }
    });


    //fix-bug: begin (修正下拉選單問題)
    const onBackListClcik = () => {
        ++toLink;
    }
    //fix-bug: end (修正下拉選單問題)
    return (
        <>
            <CRow className="mb-1">
                <div className="col">
                    <Link className="btn btn-info btn-sm float-right" to={locationPathname} onClick={onBackListClcik}>
                        <i className="fas fa-undo-alt mr-1" />
                        <span>{t('Return grid list')}</span>
                    </Link>
                </div>
            </CRow>

        </>
    );
}

export default FormBackGridFixed;