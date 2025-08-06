<script setup>
import { computed, ref } from 'vue';
import store from '../store';
import Alert from '../components/Alert.vue'
import FieldComponent from '../components/FieldComponent.vue';
import { useRouter, useRoute} from 'vue-router';
import { onMounted } from 'vue';
import { parsePhoneNumberFromString } from 'libphonenumber-js'
import isValid from 'uk-postcode-validator'
import axios from 'axios'

const router = useRouter()
const route = useRoute()

const lead = computed(() => store.state.lead)
const fields = computed(() => store.state.fields)
const reload = computed(() => store.state.reload);
const finalStep = computed(() => (lead.value.step === 4))
const newLead = computed(() => localStorage.getItem('vuex') ? false : true)
const loading = computed(() => store.state.loading)

const progress = computed(() => {
        const pct = (lead.value.step / 4) * 100
        return Math.min(100, Math.max(0, pct));
})

const errorMsg = ref([])
const valueChange = ref(false)

const postcodeURL = "https://api.postcodes.io/postcodes/"

onMounted(() => {
        if(route.params.id && !reload.value) {
                store.dispatch('loadLead', route.params.id)
                .catch(() => {
                        localStorage.removeItem('vuex')
                        router.push({name: '404'})

                })
        }
        if(lead.value.data.complete) {
                router.push({name: 'Complete'})
        }
})
async function nextStep() {
        console.log(newLead.value)
        if(!await areCurrentFieldsValid()) return
        if(valueChange.value) {
                await updateOrCreateLead(newLead.value)
        }
        if (!errorMsg.value.length) {
                valueChange.value = false
                store.dispatch('nextStep')
        }
}
function lastStep() {
        store.dispatch('lastStep')
}
async function updateOrCreateLead(isNewLead) {
        if (isNewLead) {
                await store.dispatch('createLead', lead.value.data)
                .catch((error) => {
                        errorMsg.value.push(error.response.data)
                })
        } else {
                await store.dispatch('updateLead', lead.value.data)
                .catch((error) => {
                        errorMsg.value.push(error.response.data)
                })
        }
}
function completeLead() {
        lead.value.data.complete = true
        lead.value.data.failed = false
        store.dispatch('completeLead', lead.value.data)
        .then(() => {
                router.push({name: 'Complete'})
        })
        .catch((error) => {
                errorMsg.value.push(error.response.data)
        })
}
function fieldName(field) {
        return field.split('_')
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ')
}
function hasValue(field) {
        return lead.value.data[field]
}
async function hasValidValue(fieldName) {
        const fieldValue = lead.value.data[fieldName]
        switch (fieldName) {
                case 'first_name':
                case 'last_name':
                        if (fieldValue.length < 2) {
                                errorMsg.value.push(`Name values must be at least 2 characters long`)
                                return false
                        } else if (fieldValue.length > 30) {
                                errorMsg.value.push(`Name values must be less than 30 characters long`)
                                return false
                        }
                        return true
                case 'email':
                        const emailRegex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/
                        if (!emailRegex.test(fieldValue)) {
                                errorMsg.value.push('Email must be valid')
                                return false
                        } else if (fieldValue.length > 40) {
                                errorMsg.value.push('Email must be less than 40 characters long')
                                return false
                        }
                        return true
                case 'phone':
                        return checkPhoneNumber(fieldValue)
                case 'date_of_birth':
                        const tomorrow = new Date()
                        tomorrow.setDate(tomorrow.getDate() + 1)
                        const eighteenYearsAgo = new Date(tomorrow.getFullYear() - 18, tomorrow.getMonth(), tomorrow.getDate())
                        if (new Date(fieldValue).getTime() > eighteenYearsAgo.getTime()) {
                                errorMsg.value.push('You must be at least 18 years old')
                                return false
                        }
                        return true
                case 'house_number':
                        if (isNaN(fieldValue) || fieldValue < 1) {
                                errorMsg.value.push('House number must be a positive number')
                                return false
                        }
                        return true
                case 'street_name':
                        if (fieldValue.length < 3) {
                                errorMsg.value.push('Street name must be at least 3 characters long')
                                return false
                        } else if (fieldValue.length > 37) {
                                errorMsg.value.push('Street name can\'t be greater than 37 characters long')
                                return false
                        }
                        return true
                case 'city':
                        try {
                                const response = await axios.get(`${postcodeURL}${lead.value.data.postcode.replace(" ", "")}`)
                                if (response.data.result.admin_district !== fieldValue) {
                                        errorMsg.value.push('City does not match postcode')
                                        return false
                                }
                        } catch (error) {
                                errorMsg.value.push(error.message)
                                return false
                        }
                        if (fieldValue.length < 3) {
                                errorMsg.value.push('City must be at least 3 characters long')
                                return false
                        } else if (fieldValue.length > 58) {
                                errorMsg.value.push('City name can\'t be greater than 58 characters long')
                                return false
                        }
                        return true;
                case 'postcode':
                        if (!isValid(fieldValue)) {
                                errorMsg.value.push('Postcode must be valid')
                                return false
                        }
                        return true;
                case'proof_of_id':
                case'proof_of_address':
                        const allowedInputs = /^data:image\/(?:png|jpe?g);base64,/i
                        if (!allowedInputs.test(fieldValue)) {
                                errorMsg.value.push('File must be a jpg, jpeg or png')
                                return false
                        }
                        return true
        }
}
async function areCurrentFieldsValid() {
        errorMsg.value = []
        if (!fields.value.every(hasValue)) {
                errorMsg.value.push("All fields must be completed")
                return false
        }
        for(let field of fields.value) {
        let valid = await hasValidValue(field)
        if (!valid) {
                return false
        }
        }
        return true
}

function checkPhoneNumber(phone) {
        let phoneNum = parsePhoneNumberFromString(phone, 'GB')
        if (phoneNum ? !phoneNum.isValid() : false) {
                errorMsg.value.push("Phone number must be a UK phone number")
                return false
        }
        lead.value.data.phone = phoneNum.formatInternational();
        return true
}
</script>
<template>
        <div class="fixed top-0 left-0 w-full h-1 bg-gray-200 z-50">
                <div
                class="h-full bg-indigo-600 transition-all duration-300 ease-in-out"
                :style="{ width: progress + '%' }"
                />
        </div>
        <div v-if="loading">Loading...</div>
        <div class="mt-10 mx-auto w-full max-w-md px-4" v-else>
                <header class="text-gray-300 flex justify-center font-bold text-3xl mb-6">
                        {{ reload ? 'Resubmit Details' : 'Submit Details' }}
                </header>
                <form class="space-y-6 rounded-lg p-8 bg-gray-800 animate-fade-in-down" @submit.prevent="completeLead">
                        <Alert v-if="errorMsg.length">
                                <ul class="list-disc list-inside space-y-1 text-sm">
                                        <li v-for="error in errorMsg">
                                                {{error}}   
                                        </li>
                                </ul>
                                <span @click="errorMsg = []" class="w-8 h-8 flex items-center justify-center rounded-full transition-colors cursor-pointer hover:bg-[rgba(0,0,0,0.2)]">
                                        <svg xmlns="../assets/xrmisalNoText.svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                        
                                </span>
                        </Alert>
                        <transition
                        appear
                        mode="out-in"
                        enter-active-class="animate-fade-in-down"
                        v-if="!finalStep"
                        >
                                <div
                                :key="fields.join('-')" class="space-y-4"
                                >
                                        <FieldComponent v-for="field in fields" 
                                        :key="field"
                                        :field="field"
                                        @change="valueChange = true" 
                                        />
                                </div>
                        </transition>

                        <div v-if="finalStep">
                                <div class=" text-white text-center animate-fade-in-down">
                                        <p>Your Details</p>
                                        <hr>
                                        <div v-for="(value, field) in lead.data">
                                                <p v-if="!['id', 'complete', 'proof_of_id', 'proof_of_address', 'failed'].includes(field)">{{fieldName(field)}}: {{ value  }}</p>
                                        </div>
                                        <div>
                                                <p>Proof Sent</p>
                                        </div>


                                </div>
                        </div>

                        <div class="flex items-center justify-between">
                                <button type="button" @click="lastStep" :disabled="lead.step === 1" class="flex w-full justify-center rounded-md bg-gray-900 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 mr-3 whitespace-nowrap">
                                        << Last Step
                                </button>
                                <button type="button" @click="nextStep" :disabled="lead.step === 4" class="flex w-full justify-center rounded-md bg-gray-900 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 whitespace-nowrap">
                                        Next Step >>
                                </button>
                        </div>
                        <div v-if="finalStep">
                                <button type="submit" class="flex w-full justify-center rounded-md bg-gray-900 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                        Submit
                                </button>
                        </div>

                </form>
        </div>
</template>