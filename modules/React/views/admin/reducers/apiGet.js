
import axios from 'axios';

async function apiGet(state={}, actions) {

  var requestParams = {
    method: 'get',
    url: actions.url,
  };
  if (actions.params) {
    requestParams.params = {
      ...actions.params,
    };
  }
  if (actions.type == 'apiGet') {
    return axios(requestParams).then(function(response) {
      if (response.data.code == 0) {
        let data = {
          ...state
        };
        data.apiGet = response.data;
        return data;
      }
      return state;
    }).catch(error => {
      console.warn(error);
      return state;
    });
  } else {
    return state;
  }
}

export default apiGet;