export default {
    customFilterStandard: {
        email: /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/,
        cellphone: /^(\+\d{2,3}){0,1}\d{9,}$/,
        zip: /^\d{3,}$/,
        county: /^.{3,}$/,
        distinct: /^.{3,}$/,
        address: /^.{5,}$/,
    }
};