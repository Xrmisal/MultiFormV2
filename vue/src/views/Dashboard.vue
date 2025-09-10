<script setup>
import { computed } from 'vue';
import store from '../store';
import { useRouter } from 'vue-router';

const router = useRouter()

const leadStatus = computed(() => store.state.lead.data.status)

</script>

<template>
    <div class="text-white animate-fade-in-down">
        <div v-if="leadStatus === 'empty'">
            <div class="mb-4">
                No details found
            </div>
            <router-link to="/form" class="py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Start
            </router-link>
        </div>
        <div v-else-if="leadStatus === 'draft'">
            <div class="mb-4">
                Details found, submission incomplete
            </div>
            <router-link to="/form"
            class="py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Continue
            </router-link>
        </div>
        <div v-else-if="leadStatus === 'complete'">
            <div class="mb-4">
                Submission complete, please wait for it to be processed
            </div>
        </div>
        <div v-else-if="leadStatus === 'failed'">
            <div class="mb-4">
                There was an error processing your submission, please resubmit details
            </div>
            <router-link to="/form" class="py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Resubmit
            </router-link>
        </div>
        <div v-else-if="leadStatus === 'processed'">
            <div class="mb-2">
                Submission processed successfully. 
            </div>
        </div>
    </div>
</template>