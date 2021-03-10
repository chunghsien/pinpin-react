
import toggleSlideBar from './toggleSideBar';
import toForm from './toForm';
import apiGet from './apiGet';
import apiPut from './apiPut';
import apiDelete from './apiDelete';
//import { combineReducers } from "redux";
import formRowsReducer  from './formRowsReducer';
import formActiveTabReducer  from './formActiveTabReducer';
import { FORM_ROWS, FORM_ACTIVE_TAB } from "../actions/formRowsAction";

const reducers = (state, actions) => {
  switch (actions.type) {
    case 'toForm':
      return toForm(state, actions);
    case 'toggleSideBar':
      return toggleSlideBar(state, actions);
    case FORM_ROWS:
      return formRowsReducer(state, actions);
    case FORM_ACTIVE_TAB:
      return formActiveTabReducer(state, actions);
    case 'apiGet':
      return apiGet(state, actions);
    case 'apiPut':
      return apiPut(state, actions);
    case 'apiDelete':
      return apiDelete(state, actions);
    default:
      return state;
  }
}

export default reducers;