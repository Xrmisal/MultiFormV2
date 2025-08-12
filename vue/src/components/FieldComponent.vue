<script setup>
import { computed, ref } from 'vue';
import store from '../store';

const props = defineProps({ field: String})
const emit = defineEmits(['change', 'error', 'file'])
const lead = computed(() => store.state.lead.data)

const previews = ref({})

function fieldName(field) {
        return field.split('_')
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ')
}

function onImageChoose(ev, field) {
    const file = ev.target.files?.[0];
    if(!file) return;

    const maxBytes = 2 * 1000 * 1000
    if(file.size > maxBytes) {
        emit('error', 'File too large, max 2MB')
        ev.target.value = ''
        return
    }

    emit('file', {field, file})
    emit('change')
    
    previews.value[field] = URL.createObjectURL(file)
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
            <img :v-if="previews[field]" :src="previews[field]" class="mt-2 max-h-48 rounded"/>
            <input 
                type="file" 
                accept="image/*"
                @change="onImageChoose($event, field)"
                required
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