import axios from 'axios';

export const getCsrf = () => {
  return  document.getElementsByName("csrf-token")[0].content;
}
export const csrfAssign = (csrf) => {
  document.getElementsByName("csrf-token")[0].content = csrf;
};

export const initCsrf = () => {
  const ele = document.getElementsByName("csrf-token")[0];
  if(ele && !ele.content) {
    let uri = `/en/api/site/csrf`;
    if(__NEXT_DATA__.buildId == 'development') {
      uri = process.env.LOCAL_API_URI + uri;
    }
    axios.get(uri).then((response) => {
      ele.content = response.data.data.__csrf;
    });
  }
}