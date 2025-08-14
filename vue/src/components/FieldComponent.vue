<script setup>
import { computed, ref } from 'vue';
import store from '../store';

const props = defineProps({ field: String})
const emit = defineEmits(['change', 'error', 'file'])
const lead = computed(() => store.state.lead.data)

const previews = ref({})

const ALLOWED_MIME = new Set(['image/jpeg', 'image/png'])
const ALLOWED_EXT = new Set(['jpg', 'jpeg', 'png'])

const getExt = (name) => (name.toLowerCase().match(/\.([a-z0-9]+)$/)?.[1] ?? '')

async function isRealJpegOrPng(file) {
    // Check the first 8 bytes of the file to see if it's a JPEG or PNG
    const head = new Uint8Array(await file.slice(0, 8).arrayBuffer())
    const isJpeg = head[0] === 0xff && head[1] === 0xd8 && head[2] === 0xff
    const isPng = head[0] === 0x89 && head[1] === 0x50 && head[2] === 0x4e && head[3] === 0x47 && head[4] === 0x0d && head[5] === 0x0a && head[6] === 0x1a && head[7] === 0x0a
    return isJpeg || isPng
}

function fieldName(field) {
        return field.split('_')
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ')
}

async function onImageChoose(ev, field) {
    const file = ev.target.files?.[0];
    if(!file) return;

    const maxBytes = 10 * 1000 * 1000
    if(file.size > maxBytes) {
        emit('error', 'File too large, max 10MB')
        ev.target.value = ''
        previews.value[field] = null
        return
    }

    const extOK = ALLOWED_EXT.has(getExt(file.name))
    const mimeOK = file.type ? ALLOWED_MIME.has(file.type) : true
    if(!extOK || !mimeOK || !(await isRealJpegOrPng(file))) {
        emit('error', 'Invalid file type, must be JPG/JPEG or PNG')
        ev.target.value = ''
        previews.value[field] = null
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
                accept=".jpg,.jpeg,.png,image/jpeg,image/png"
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