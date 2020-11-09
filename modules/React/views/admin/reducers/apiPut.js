
import axios from 'axios';

async function apiPut(state, actions) {
    let requestParams = {
        method: 'put',
        url: actions.url,
        data: actions.params
    };
    return axios(requestParams).then(function(response) {
        return {
            type: 'apiPut',
            payload: response.data
        }

    }).catch(function(error) {
        return {
            state, ...{
                type: 'apiPut',
                payload: error.response.data
            }
        }
    });
}

export default apiPut;