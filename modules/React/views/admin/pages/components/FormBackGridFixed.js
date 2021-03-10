import React, { useEffect, useState } from 'react';
import { connect } from "react-redux";
import { Link } from "react-router-dom";
import { CRow } from '@coreui/react';
import { FORM_ACTIVE_TAB } from "../../actions/formRowsAction";

const FormBackGridFixed = (props) => {
  const [locationPathname, setLocationPathname] = useState(location.pathname.replace(/\/add$/, '').replace(/\/\d+$/, ''));
  const [linkColor, setLinkColor] = useState("btn-info");
  const { formActiveTab, dispatch } = props;
  const t = props.t;
  
  const NC = 0;
  useEffect(() => {
    if (formActiveTab && formActiveTab.goal) {
      if(location.pathname == formActiveTab.goal)
      {
        const clone = Object.assign({}, formActiveTab);
        setLocationPathname(clone.uri);
        dispatch({type: FORM_ACTIVE_TAB, data:{
          uri: undefined,
          goal:undefined,
          tab: clone.tab
        }});
        setLinkColor("btn-secondary");
      }
    }
  }, [formActiveTab, NC]);

  return (
    <>
      <CRow className="mb-1">
        <div className="col">
          <Link className={`btn ${linkColor} btn-sm float-right`} to={locationPathname}>
            <i className="fas fa-undo-alt mr-1" />
            <span>{t('Return prev')}</span>
          </Link>
        </div>
      </CRow>

    </>
  );
}
const mapStateToProps = (state) => {
  return {
    dispatch: state.dispatch,
    formActiveTab: state.formActiveTab,
  };
};

//export default Documents;
export default connect(mapStateToProps)(FormBackGridFixed);