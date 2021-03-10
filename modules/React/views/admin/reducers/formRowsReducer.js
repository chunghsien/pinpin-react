import { FORM_ROWS } from "../actions/formRowsAction";

const initState = {}
const formRowsReducer = (state = initState, action) => {
  //console.log(action);
  if (action.type == FORM_ROWS) {
    let data = {
      ...state
    };
    data[FORM_ROWS] = {
      ...state[FORM_ROWS],
      ...action.data
    }

    return data;
  }
  return state;
}

export default formRowsReducer;