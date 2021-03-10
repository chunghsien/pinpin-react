
import axios from 'axios';

async function apiPut(state = {}, actions) {
  let requestParams = {
    method: 'put',
    url: actions.url,
    data: actions.params
  };
  if (actions.type == "apiPut") {
    return axios(requestParams).then(function(response) {
      if (response.data.code == 0) {
        let data = {
          ...state
        };
        data.apiPut = response.data;
        return data;
      }
    }).catch(function(err) {
      console.warn(err);
      return state;
    });
  }
  return state;
}

export default apiPut;
//apiPut