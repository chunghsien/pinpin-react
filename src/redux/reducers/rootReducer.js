import productReducer from "./productReducer";
import productDataReducer from "./productDataReducer";
import cartReducer from "./cartReducer";
import wishlistReducer from "./wishlistReducer";
import compareReducer from "./compareReducer";
import languageReducer from "./languageReducer";
import themeReducer from "./themeReducer";
import maintainAuthReducer from "./maintainAuthReducer";
import { combineReducers } from "redux";

const rootReducer = combineReducers({
  //productData: productReducer,
  cartData: cartReducer,
  wishlistData: wishlistReducer,
  compareData: compareReducer,
  locale: languageReducer,
  theme:themeReducer,
  maintainAuth: maintainAuthReducer,
  productList: productReducer,
  productData: productDataReducer
});

export default rootReducer;
