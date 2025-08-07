<script setup>
import { computed, ref } from 'vue';
import store from '../store';

defineProps({
    field: String,
})
const emit = defineEmits(['change', 'error'])

const lead = computed(() => store.state.lead.data)

function fieldName(field) {
        return field.split('_')
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ')
}

function onImageChoose(ev, field) {
    const file = ev.target.files[0];
    const maxBytes = 2 * 1024 * 1024
    if(file.size > maxBytes) {
        emit('error', 'File too large, max 2MB')
        ev.target.value = ''
        return
    }
    const reader = new FileReader();
    reader.onload = () => {
        lead.value[field] = reader.result

    }
    reader.readAsDataURL(file);
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
        <div class="mt-2" v-if="!['date_of_birth', 'proof_of_id', 'proof_of_address'].includes(field)">
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
        <div class="mt-2" v-else-if="field === 'date_of_birth'">
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
        <div class="mt-2" v-else>
            <img :v-model="lead[field]" :src="lead[field]"/>
            <input 
                type="file" 
                id="image" 
                @change="$emit('change'); onImageChoose($event, field)"
                required=""
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