<script setup>
import { computed } from 'vue';
import store from '../store';
defineProps({
    field: String,
})
defineEmits(['change'])

const lead = computed(() => store.state.lead.data)

function fieldName(field) {
        return field.split('_')
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ')
}

</script>

<template>
    <div >
        <label
            :for="field"
            class="block text-sm/6 font-medium text-white"
        >
            {{ fieldName(field) }}
        </label>
        <div class="mt-2" v-if="field !== 'date_of_birth'">
            <input 
                :type="field"
                :name="field"
                :id="field"
                :autocomplete="field"
                @change="$emit('change')"
                required=""
                v-model="lead[field]"
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
        <div class="mt-2" v-else>
            <input 
                type="date" 
                :name="field" 
                :id="field" 
                :autocomplete="field" 
                @change="$emit('change')"
                required="" 
                v-model="lead[field]" 
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