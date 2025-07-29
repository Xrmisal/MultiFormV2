import { createStore } from "vuex";

const store = createStore( {
    state: {
        lead: {
            name: '',
            email: '',
            phone: '',
            date_of_birth: '',
            street: '',
            city: '',
            postcode: '',
            step: 1,
            complete: false
        },
    },
    getters: {},
    actions: {},
    mutations: {},
    modules: {}
})

export default store