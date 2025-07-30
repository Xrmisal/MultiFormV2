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
    <h1 class="text-3xl text-white">Complete</h1>
</template>