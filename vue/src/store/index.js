import { createStore } from "vuex";
import createPersistedState from "vuex-persistedstate";

const store = createStore( {
    state: {
        lead: {
            data: {
                first_name: '',
                last_name: '',
                email: '',
                phone: '',
                date_of_birth: '',
                house_number: '',
                street_name: '',
                city: '',
                postcode: '',
                step: '',
                complete: false
            },
            step: 1,

        },
        fields: [
            'first_name',
            'last_name',
            'email',
            'phone'
        ]
    },
    getters: {},
    actions: {

        createLead({ commit }, lead) {
            console.log('entered store')
            commit('createLead', lead)
            console.log("axios post here")
        },
        updateLead({ commit }, lead) {
            commit('updateLead', lead)
            console.log("axios put here")
        },
        completeLead({ commit }) {
            commit('completeLead')
            console.log("axios put here")
        },
        nextStep({commit}) {
            commit('nextStep')
            commit('setFields')
        },
        lastStep({commit}) {
            commit('lastStep')
            commit('setFields')

        }
    },
    mutations: {
        createLead(state, lead) {
            state.lead.data = lead
            state.lead.data.step = 2
        },
        updateLead(state, lead) {
            state.lead.data = lead
        },
        completeLead(state) {
            state.lead.data.complete = true
        },
        nextStep(state) {
            state.lead.step++
            if (state.lead.data.step < state.lead.step) {
                state.lead.data.step = state.lead.step
            }
        },
        lastStep(state) {
            state.lead.step--
        },
        setFields(state) {
            switch(state.lead.step) {
                case 1:
                    state.fields = ['first_name',
                    'last_name', 'email', 'phone']
                    break;
                case 2:
                    state.fields = ['date_of_birth', 'house_number','street_name', 'city', 'postcode']
                    break;
                case 3:
                    state.fields = []
                    break;
            }
        }
    },
    modules: {},
    plugins: [
        createPersistedState()
    ]
})

export default store