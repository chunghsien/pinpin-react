import languageReducer from "./languageReducer";
import { combineReducers } from "redux";

const rootReducer = combineReducers({
  locale: languageReducer
});

export default rootReducer;
