<script setup>
import { computed } from 'vue';
import store from '../store';

const lead = computed(() => store.state.lead.data)
</script>

<template>


</template>