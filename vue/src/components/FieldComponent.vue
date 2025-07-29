<script setup>
import { computed } from 'vue';
import store from '../store';
defineProps({
    Field: String
})

const lead = computed(() => store.state.lead.data)

</script>

<template>
    <div v-if="Field !== 'date_of_birth'">
        <label
            :for="Field"
            class="block text-sm/6 font-medium text-white"
        >
            {{Field.charAt(0).toUpperCase() + Field.slice(1) }}
        </label>
        <div class="mt-2">
            <input 
                :type="Field"
                :name="Field"
                :id="Field"
                :autocomplete="Field"
                required=""
                v-model="lead[Field]"
                class="
                    block w-full
                    rounded-md
                    bg-white
                    px-3
                    py-1.5
                    text-base
                    text-gray-900
                    outline-1
                    -outline-offset-1
                    outline-gray-300
                    placeholder:text-gray-400
                    focus:outline-2
                    focus:-outline-offset-2
                    focus:outline-indigo-600
                    sm:text-sm/6
                    "
            />
        </div>
    </div>
    <div v-else>
        <label
            :for="Field"
            class="block text-sm/6 font-medium text-white"
        >
            Date Of Birth
        </label>
        <div class="mt-2">
            <input 
                type="date" 
                :name="Field" 
                :id="Field" 
                :autocomplete="Field" 
                required="" 
                v-model="lead[Field]" 
                class="
                    block w-full
                    rounded-md
                    bg-white
                    px-3
                    py-1.5
                    text-base
                    text-gray-900
                    outline-1
                    -outline-offset-1
                    outline-gray-300
                    placeholder:text-gray-400
                    focus:outline-2
                    focus:-outline-offset-2
                    focus:outline-indigo-600
                    sm:text-sm/6
                ">
        </div>
    </div>
</template>