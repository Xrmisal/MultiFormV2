import { createStore } from "vuex";
import createPersistedState from "vuex-persistedstate";
import axiosClient from "../axios";

const store = createStore( {
    state: {
        lead: {
            data: {
                id: '',
                first_name: '',
                last_name: '',
                email: '',
                phone: '',
                date_of_birth: '',
                house_number: '',
                street_name: '',
                city: '',
                postcode: '',
                proof_of_id: '',
                proof_of_address: '',
                complete: false,
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
            console.log(lead)
            return axiosClient.post('/leads', lead)
            .then((response) => {
                commit('updateLead', response.data.data)
            })

        },
        updateLead({ commit }, lead) {
            return axiosClient.put(`/leads/${lead.id}`, lead
            )
            .then(() => {
                commit('updateLead', lead)
            })
        },
        completeLead({ commit }, lead) {
            return axiosClient.put(`/leads/${lead.id}`, lead)
            .then(() => {
                commit('completeLead')
            })
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
                    state.fields = ['proof_of_id', 'proof_of_address']
                    break;
                case 4:
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