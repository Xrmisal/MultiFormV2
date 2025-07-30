<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import store from '../store';

const router = useRouter()

onMounted(() => {
    if (!store.state.lead.data.complete) {
        console.log("lead not complete")
        router.push({name: 'Form'})
    }
})

</script>

<template>
    <div class="text-center">
        <h1 class="text-3xl text-white">Completed Submission</h1>
        <p class="text-white">Your lead has been submitted. You may now close this window.</p>
    </div>
</template>