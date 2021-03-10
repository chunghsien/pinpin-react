import { LANGUAGE_TYPE } from "../actions/languageActions";

const initState = "zh-TW";
const languageReducer = (state= initState, action) => {
  if (action.type === LANGUAGE_TYPE) {
    state = action.payload;
    return state;
  }
  return state;
};

export default languageReducer;
