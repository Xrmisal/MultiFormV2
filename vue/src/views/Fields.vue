<script setup>
import { computed, ref } from 'vue';
import store from '../store';
import Alert from '../components/Alert.vue'
import FieldComponent from '../components/FieldComponent.vue';
import { useRouter } from 'vue-router';
import { onMounted } from 'vue';
import { parsePhoneNumberFromString } from 'libphonenumber-js'
import isValid from 'uk-postcode-validator'
import axios from 'axios'

const router = useRouter()

const lead = computed(() => store.state.lead)
const fields = computed(() => store.state.fields)
const finalStep = computed(() => (lead.value.step === 3))
const newLead = computed(() => {
        if (localStorage.getItem('vuex')) return false 
        else return true
})

const errorMsg = ref([])
const valueChange = ref(false)

const postcodeURL = "https://api.postcodes.io/postcodes/"

onMounted(() => {
        if(lead.value.data.complete) {
                router.push({name: 'Complete'})
        }
})
async function nextStep() {
        if(!await areCurrentFieldsValid()) return
        if(valueChange.value || newLead.value) {
                await updateOrCreateLead(newLead.value)
                valueChange.value = false
        } else {
                store.dispatch('nextStep')
        }
}
function lastStep() {
        store.dispatch('lastStep')
}
async function updateOrCreateLead(isNewLead) {
        if (isNewLead) {
                store.dispatch('createLead', lead.value.data)
                .then(() => {
                        store.dispatch('nextStep')
                })
                .catch((error) => {
                        errorMsg.value.push(error.message)
                })
        } else {
                store.dispatch('updateLead', lead.value.data)
                .then(() => {
                        store.dispatch('nextStep')
                })
                .catch((error) => {
                        errorMsg.value.push(error.message)
                })
        }
}
function completeLead() {
        lead.value.data.complete = true
        store.dispatch('completeLead', lead.value.data)
        .then(() => {
                router.push({name: 'Complete'})
        })
        .catch((error) => {
                errorMsg.value.push(error.message)
                console.log("lead complete failed")
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
async function hasValidValue(field) {
        switch (field) {
                case 'first_name':
                        if (lead.value.data[field].length < 3) {
                                errorMsg.value.push("Name must be at least 3 characters long")
                                return false
                        }
                        return true
                case 'last_name':
                        if (lead.value.data[field].length < 3) {
                                errorMsg.value.push("Name must be at least 3 characters long")
                                return false
                        }
                        return true
                case 'email':
                        if(!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(lead.value.data[field])){
                                errorMsg.value.push("Email must be valid")
                                return false
                        }
                        return true
                case 'phone':
                        return checkPhoneNumber(lead.value.data[field])
                case 'date_of_birth':
                        let stripTime = (d) => {
                                d.setHours(0, 0, 0, 0);
                                return d;
                        }
                        let today = new Date()
                        let eighteenYearsAgo = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate())
                        if (stripTime(new Date(lead.value.data[field])) > eighteenYearsAgo) {
                                errorMsg.value.push("You must be at least 18 years old")
                                return false
                        }
                        return true
                case 'house_number':
                        if(isNaN(lead.value.data[field])) {
                                errorMsg.value.push("House number must be a number")
                                return false
                        }
                        if (parseInt(lead.value.data[field]) < 1) {
                                errorMsg.value.push("House number must be greater than 0")
                                return false
                        }
                        return true
                case 'street_name':
                        if (lead.value.data.postcode.length < 3) {
                                errorMsg.value.push("Street name must be at least 3 characters long")
                                return false
                        }
                        return true
                case 'city':
                        await axios.get(postcodeURL + lead.value.data.postcode.trim().replace(" ", ""))
                        .then((response) => {
                                if (response.data.result.admin_district !== lead.value.data[field]) {
                                        errorMsg.value.push("City does not match postcode")
                                        return false
                                }
                        })
                        .catch((error) => {
                                errorMsg.value.push(error.message)
                                return false
                        })
                        if (lead.value.data[field].length < 3) {
                                errorMsg.value.push("City must be at least 3 characters long")
                                return false
                        }
                        return true;
                case 'postcode':
                        if (!isValid(lead.value.data[field])) {
                                errorMsg.value.push("Postcode must be valid")
                                return false
                        }
                        return true;
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




const progress = computed(() => {
        const pct = (lead.value.step / 3) * 100
        return Math.min(100, Math.max(0, pct));
})

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
        <div class="mt-10 mx-auto w-full max-w-md px-4">
                <header class="text-gray-300 flex justify-center font-bold text-3xl mb-6">
                        Submit Details
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
                                                <p v-if="field !== 'step' && field !== 'complete'">{{fieldName(field)}}: {{ value  }}</p>

                                        </div>


                                </div>
                        </div>

                        <div class="flex items-center justify-between">
                                <button type="button" @click="lastStep" :disabled="lead.step === 1" class="flex w-full justify-center rounded-md bg-gray-900 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 mr-3 whitespace-nowrap">
                                        << Last Step
                                </button>
                                <button type="button" @click="nextStep" :disabled="lead.step === 3" class="flex w-full justify-center rounded-md bg-gray-900 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 whitespace-nowrap">
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