<script setup>
import { computed, ref } from 'vue';
import store from '../store';
import Alert from '../components/Alert.vue'
import FieldComponent from '../components/FieldComponent.vue';
import { useRoute } from 'vue-router';
import { watch } from 'vue';


const route = useRoute()
const lead = computed(() => store.state.lead.data)
const fields = computed(() => store.state.fields)
const errorMsg = ref([])

function getErrors() {
        console.log("getting errors")
}

function nextStep() {
        console.log("next step")
}

function lastStep() {
        console.log("last step")
}

function complete() {
        console.log("complete")
}

</script>

<template>
        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-md ">
                <header class="text-gray-300 flex justify-center font-bold text-3xl mb-6">
                        Submit Details
                </header>
                <form class="space-y-6 rounded-lg p-8 bg-gray-800 animate-fade-in-down" @submit.prevent="complete">
                        <Alert v-if="errorMsg.length">
                                <ul class="list-disc list-inside space-y-1 text-sm">
                                        <li v-for="error in errorMsg">
                                                {{error}}   
                                        </li>
                                </ul>
                                <span @click="errorMsg = ''" class="w-8 h-8 flex items-center justify-center rounded-full transition-colors cursor-pointer hover:bg-[rgba(0,0,0,0.2)]">
                                        <svg xmlns="../assets/xrmisalNoText.svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                        
                                </span>
                        </Alert>
                        <div>
                                <label for="name" class="block text-sm/6 font-medium text-white">Full Name</label>
                                <div class="mt-2">
                                        <input type="name" name="name" id="name" autocomplete="name" required="" 
                                        v-model="lead.name" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                </div>
                        </div>
                        
                        <div>
                                <label for="email" class="block text-sm/6 font-medium text-white">Email Address</label>
                                <div class="mt-2">
                                        <input type="email" name="email" id="email" autocomplete="email" required="" v-model="lead.email" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                </div>
                                
                        </div>
                        <div>
                                <label for="phone" class="block text-sm/6 font-medium text-white">Phone Number</label>
                                
                                <div class="mt-2">
                                        <input type="phone" name="phone" id="phone" autocomplete="phone" required="" v-model="lead.phone" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                                <button type="button" @click="lastStep" :disabled="lead.step === 1" class="flex w-full justify-center rounded-md bg-gray-900 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 mr-3">
                                        << Last Step
                                </button>
                                <button type="button" @click="nextStep" :disabled="lead.step === 3" class="flex w-full justify-center rounded-md bg-gray-900 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                        Next Step >>
                                </button>
                        </div>
                        <div v-if="lead.step === 3">
                                <button type="submit" class="flex w-full justify-center rounded-md bg-gray-900 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                        Submit
                                </button>
                        </div>

                </form>
        </div>
</template>