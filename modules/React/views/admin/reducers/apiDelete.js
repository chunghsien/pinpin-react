
import axios from 'axios';

async function apiDelete(state, actions) {
    let requestParams = {
        method: 'delete',
        url: actions.url,
        data: actions.params
    };
    return axios(requestParams).then(function(response) {
        return {
            type: 'apiDelete',
            payload: response.data
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

export default apiDelete;