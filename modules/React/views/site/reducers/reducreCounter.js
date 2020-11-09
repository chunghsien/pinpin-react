
import {CounterType} from '../actions';
var reducerCounter = (state = 0, action) => {
    switch(action.type) {
        case CounterType.INCREMENT:
            return (++state) ;
        case CounterType.DECREMENT:
            return (--state);
        case CounterType.DOBULE:
            return (state +2);
        default:
            return state;
    }
};

export default reducerCounter;