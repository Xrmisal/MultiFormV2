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
const errorMsg = ref([])
const finalStep = computed(() => (lead.value.step === 3))
const isNewLead = ref(false)
const valueChange = ref(false)
const postcodeURL = "https://api.postcodes.io/postcodes/"

onMounted(() => {
        console.log("mounted")
        if(lead.value.data.complete) {
                console.log("complete lead")
                router.push({name: 'Complete'})
        }
        if (!lead.value.data.step) {
                console.log("new lead")
                isNewLead.value = true
        } else {
                console.log("existing lead")
        }
})
async function nextStep() {
        if(!await areCurrentFieldsValid()) return
        console.log("next step")
        if(isNewLead.value) createLead()
        else if(valueChange.value) {
                updateLead()
                valueChange.value = false
        }
        store.dispatch('nextStep')
}
function lastStep() {
        console.log("last step")
        store.dispatch('lastStep')
}
function createLead() {
        console.log("creating lead")
        console.log(lead.value.data)
        store.dispatch('createLead', lead.value.data)
        .then(() => {
                isNewLead.value = false
                console.log("lead created")
        })
        .catch((error) => {
                errorMsg.value.push(error.message)
                console.log("lead not created")
        })
}
function updateLead() {
        store.dispatch('updateLead', lead.value.data)
        .catch((error) => {
                errorMsg.value.push(error.message)
                console.log("lead not updated")
        })
}

function hasValue(field) {
        return lead.value.data[field]
}
async function hasValidValue(field) {
        switch (field) {
                case 'name':
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
                        let phone = parsePhoneNumberFromString(lead.value.data[field], 'GB')
                        if (phone ? !phone.isValid() : false) {
                                errorMsg.value.push("Phone number must be valid")
                                return false
                        }
                        return true
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
                case 'street':
                        if (lead.value.data.postcode.length < 3) {
                                errorMsg.value.push("Street must be at least 3 characters long")
                                return false
                        }
                        return true
                case 'city':
                        await axios.get(postcodeURL + lead.value.data.postcode.trim().replace(" ", ""))
                        .then((response) => {
                                console.log(response.data.result)
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
        console.log("validating current fields")
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
        console.log("fields are valid")
        return true
}

function complete() {
        console.log("completing lead")
        store.dispatch('completeLead')
        .then(() => {
                router.push({name: 'Complete'})
        })
        .catch((error) => {
                errorMsg.value.push(error.message)
                console.log("lead complete failed")
        })
}

</script>

<template>
        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-md ">
                <header class="text-gray-300 flex justify-center font-bold text-3xl mb-6">
                        Submit Details
                </header>
                <form class="space-y-6 rounded-lg p-8 bg-gray-800 animate-fade-in-down" @submit.prevent="complete">
                        <pre class="text-white">{{ lead }} {{ valueChange }}</pre>
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

                        <FieldComponent v-for="field in fields" :field="field" @change="valueChange = true" 
                        class="animate-fade-in-down"/>
                        <div v-if="finalStep">
                                Yeahhhhhhhhh buddddyyyyyyy
                        </div>
                        <div class="flex items-center justify-between">
                                <button type="button" @click="lastStep" :disabled="lead.step === 1" class="flex w-full justify-center rounded-md bg-gray-900 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 mr-3">
                                        << Last Step
                                </button>
                                <button type="button" @click="nextStep" :disabled="lead.step === 3" class="flex w-full justify-center rounded-md bg-gray-900 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
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