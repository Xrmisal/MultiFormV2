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
            commit('updateLead', lead)
        },
        updateLead({ commit }, lead) {
            commit('updateLead', lead)
        },
        completeLead({ commit }) {
            commit('completeLead')
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
        updateLead(state, lead) {
            state.lead.data = lead
        },
        completeLead(state) {
            state.lead.data.complete = true
        },
        nextStep(state) {
            state.lead.step++
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