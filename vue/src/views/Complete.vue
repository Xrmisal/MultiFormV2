<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import store from '../store';

const router = useRouter()

onMounted(() => {
    if (!store.state.lead.data.complete) {
        router.push({name: 'Form'})
    }
    localStorage.removeItem('vuex')
})

</script>

<template>
    <div class="text-center text-white">
        <h1 class="text-3xl">Completed Submission</h1>
        <p>
            Your lead has been submitted. You may now close this window.
        </p>
    </div>
</template>