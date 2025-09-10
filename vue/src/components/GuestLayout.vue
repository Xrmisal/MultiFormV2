<script setup>
defineProps({
    title: String
})
</script>

<template>
    <div class="flex min-full flex-none flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img class="mx-auto w-auto size-20" src="../assets/xrmisalNoText.svg" alt="Your Company" />
            <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-white">{{ title }}</h2>
        </div>
            <slot></slot>
    </div>
</template>