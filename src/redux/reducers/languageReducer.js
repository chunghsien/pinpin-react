import { INIT_LANGUAGE } from "../actions/languageActions";

const initState = [INIT_LANGUAGE];
const languageReducer = (state= initState, action) => {
  if (action.type === INIT_LANGUAGE) {
    state = action.payload;
    return state;
  }
  return state;
};

export default languageReducer;
