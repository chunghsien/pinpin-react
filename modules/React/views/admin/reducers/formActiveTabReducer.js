import { FORM_ACTIVE_TAB } from "../actions/formRowsAction";

const initState = {};
const formActiveTabReducer = (state = initState, action) => {
  if(action.type == FORM_ACTIVE_TAB) {
    let data = {
      ...state
    };
    data[FORM_ACTIVE_TAB] = action.data;
    return data;
  }
  return state
}

export default formActiveTabReducer;