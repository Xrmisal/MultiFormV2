<script setup>
import {onMounted, onUnmounted, ref, computed} from 'vue'
import Sidebar from "./Sidebar.vue";
import TopHeader from "./TopHeader.vue";
import store from '../store';
import Spinner from './core/Spinner.vue';

const currentUser = computed(() => store.state.user.data)
const leadStatus = computed(() => store.state.lead.data.status)
const sidebarOpened = ref(true);

defineProps({
    title: String
})

onMounted(() => {
    updateSidebarState()
    window.addEventListener('resize', updateSidebarState)
    store.dispatch('getStatus')
})

onUnmounted(() => {
    window.removeEventListener('resize', updateSidebarState)

})


function toggleSidebar() {
    sidebarOpened.value = !sidebarOpened.value
}
function updateSidebarState() {
    sidebarOpened.value = window.innerWidth > 768
}

</script>

<template>
    <div v-if="currentUser.name" class="min-h-full flex">
        <!--    Sidebar-->
        <Sidebar :class="{'-ml-[200px]': !sidebarOpened}"/>
        <!--/    Sidebar-->
        
        <div class="flex-1">
            <TopHeader @toggle-sidebar="toggleSidebar"></TopHeader>
            <!--      Content-->
            <main class="p-6">
                <router-view></router-view>
            </main>
            <!--      Content-->
        </div>
    </div>
    <div v-else><Spinner /></div>
</template>