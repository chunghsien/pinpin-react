
import axios from 'axios';

async function apiGet(state, actions) {
    var requestParams = {
        method: 'get',
        url: actions.url,
    };
    if (actions.params) {
        requestParams.params = {
            ...actions.params,
        };
    }
    return axios(requestParams).then(function(response) {
        return {
            type: 'apiGet',
            payload: response.data
        }
    }).catch(error => {
        return {state, ...{
            type: 'apiGet',
            payload: error.response.data
        }}
    });
}

export default apiGet;