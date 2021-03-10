
import axios from 'axios';

async function apiDelete(state = {}, actions) {
  let requestParams = {
    method: 'delete',
    url: actions.url,
    data: actions.params
  };
  if (actions.type == 'apiDelete') {
    return axios(requestParams).then(function(response) {
      if (response.data.code == 0) {
        let data = { ...state };
        data.apiDelete = response.data;
        return data;
      } else {
        return state;
      }
    }).catch(function(error) {
      return {
        state, ...{
          type: 'apiDelete',
          payload: error.response.data
        }
      }
    });
  }
  return state;
}

export default apiDelete;