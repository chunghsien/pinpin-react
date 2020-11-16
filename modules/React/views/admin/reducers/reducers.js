import toggleSlideBar from './toggleSideBar';
import apiGet from './apiGet';
import apiPost from './apiPost';
import apiPut from './apiPut';
import apiDelete from './apiDelete';

const reducers = (state, actions) => {
    switch(actions.type) {
        case 'toggleSideBar':
            return toggleSlideBar(state, actions);
        case 'apiGet':
            return apiGet(state, actions);
        case 'apiPost':
            return apiPost(state, actions);
        case 'apiPut':
            return apiPut(state, actions);
        case 'apiDelete':
            return apiDelete(state, actions);
        default:
            return state;
    }
}

export default reducers;