<script setup>
import {HomeIcon, UsersIcon, ViewListIcon, ChartBarIcon} from '@heroicons/vue/outline'

</script>

<template>
    <div class="w-[200px]  sticky top-0 h-screen transition-all bg-slate-900/80
        backdrop-blur-sm ring-1 ring-gray-400 text-gray-100 shadow-2xl border-r border-white/5 py-4 px-2">
        <router-link :to="{name: 'app.dashboard'}"
        class="flex items-center p-2 rounded transition-colors hover:bg-black/30">
        <span class="mr-2 text-gray-300">
            <HomeIcon class="w-5"/>
        </span>
        <span class="text-xs">
            Dashboard
        </span>
    </router-link>
</div>
</template>


