import { createStore } from "vuex";
import createPersistedState from "vuex-persistedstate";

const store = createStore( {
    state: {
        lead: {
            data: {
                name: '',
                email: '',
                phone: '',
                date_of_birth: '',
                street: '',
                city: '',
                postcode: '',
                step: 1
            },
            complete: false
        },
        fields: [
            'name',
            'email',
            'phone',
        ]
    },
    getters: {},
    actions: {
        /*
            TODO
            - Add action to initialise a lead
            - Add action to update a lead
            - Add action to 
            - Add action to complete a lead
            
        */
    },
    mutations: {
        /*
            TODO
            - Add mutation to update lead data
            - Add mutation to update current form fields
            - Add mutation to increment lead step
            - Add mutation to set complete lead
        */
    },
    modules: {},
    plugins: [
        createPersistedState()
    ]
})

export default store