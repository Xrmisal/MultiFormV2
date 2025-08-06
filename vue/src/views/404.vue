<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

function pushToForm() {
    router.push({name: 'Form'})
}
</script>

<template>
    <div class="text-center text-white">
        <h1 class="text-3xl mb-4">404: Not Found</h1>
        <p class="mb-5">
            Page not found, if you want to go back to the original form click the button below
        </p>
        <button
            class="
            bg-indigo-800
            rounded-xl
            border: border-transparent
            hover:bg-indigo-600
            focus:bg-indigo-500
            py-2
            px-4
            shadow-sm
            inline-flex
            justify-center
            "
            @click="pushToForm()"
        >
        Return
        </button>
    </div>
</template>