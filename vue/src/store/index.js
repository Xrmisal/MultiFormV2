import { createStore } from "vuex";
import createPersistedState from "vuex-persistedstate";
import axiosClient from "../axios";

const store = createStore( {
    state: {
        user: {
            token: sessionStorage.getItem('TOKEN'),
            data: {}
        },
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
                proof_of_id: '',
                proof_of_address: '',
                status: '',
            },
            step: 1,
        },
        files: {
            proof_of_id: null,
            proof_of_address: null
        },
        fields: [
            'first_name',
            'last_name',
            'email',
            'phone'
        ],
        reload: false,
        loading: false
    },
    getters: {
        hasFieldValue: (state) => (field) => {
            if(['proof_of_id', 'proof_of_address'].includes(field)) {
                return !!(state.files[field] || state.lead.data[field])
            }
            const v = state.lead.data[field]
            return v !== null && v !== undefined && String(v).trim() !== ''
        }
    },
    actions: {
        register({commit}, user) {
            return axiosClient.post(`/register`, user)
            .then(({data}) => {
                commit(`setUser`, data.user)
                commit('setToken', data.token)
                commit('setStatus', data.leadStatus)
                return data
            })
        },
        login({commit}, user) {
            return axiosClient.post(`/login`, user)
            .then(({data}) => {
                commit(`setUser`, data.user)
                commit('setToken', data.token)
                commit('setStatus', data.leadStatus)
                return data
            })
        },
        logout({commit}) {
            return axiosClient.post('/logout')
            .then((response) => {
                commit(`setUser`, null)
                commit (`setToken`, null)
                return response
            })
        },
        loadLead({ commit }, id) {
            commit('setStateLoading', true)
            return axiosClient.get(`/leads/${id}`, id)
            .then((response) => {
                commit(`updateLead`, response.data.data);
                commit(`setReload`, true)
                commit ('setStateLoading', false)
            })
            .catch(() => {
                commit('setStateLoading', false)
                throw('error')
            })
        },
        createLead({ commit, state}) {
            const fd = makeFormData(state.lead.data, state.files);
            return axiosClient.post('/leads', fd)
            .then((response) => commit('updateLead', response.data.data))

        },
        updateLead({ commit, state}) {
            const fd = makeFormData(state.lead.data, state.files);
            fd.append('_method', 'PUT')
            return axiosClient.post(`/leads/${state.lead.data.id}`, fd, {
                headers: {
                    transformRequest: data => data,
                    'Content-Type': 'multipart/form-data',
                    'X-HTTP-Method-Override': 'PUT'
                }
            })
            .then(() => commit('updateLead', state.lead.data))
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
        setStatus(state, status) {
            state.lead.data.status = status
        },
        resetState(state) {
            localStorage.clear()
            store.replaceState(getDefaultState())
        },
        setUser(state, user) {
            state.user.data = user
        },
        setToken(state, token) {
            state.user.token = token
        },
        setFile(state, {field, file}) { 
            state.files[field] = file
        },
        setStateLoading(state, loading) {
        state.loading = loading  
        },
        updateLead(state, payload) {
            state.lead.data = payload
        },
        setReload(state, reload) {
            state.reload = reload
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
        createPersistedState({paths: ['lead.data', 'lead.step', 'fields', 'reload', 'loading', 'files', 'user']})
    ]
})
const getDefaultState = () => store.state

function makeFormData(lead, files) {
    const fd = new FormData();

    const appendScalar = (k, v) => {
        if(v === undefined || v === null || v === '') return;
        if (typeof v === 'boolean') fd.append(k, v ? '1' : '0')
        else fd.append(k, v)
    }

    Object.entries(lead).forEach(([k, v]) => {
        if(['proof_of_id', 'proof_of_address'].includes(k)) return;
        appendScalar(k, v)
    })

    if (files.proof_of_id) fd.append('proof_of_id', files.proof_of_id)
    if (files.proof_of_address) fd.append('proof_of_address', files.proof_of_address)
    
    return fd
}
export default store