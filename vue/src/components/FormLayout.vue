<script setup>

</script>

<template>
    <div class="min-h-screen flex-none ">
        <!-- Top strip / logo -->
        <header class="p-4 bg-gray-900 text-center justify-center items-center ">
            <img class="mx-auto w-auto size-25" src="../assets/xrmisalNoText.svg" alt="Your Company" />
            <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-white"></h2>
        </header>
        <main>
            <router-view />
        </main>
        
        <!-- Bottom nav -->
        <footer class="p-4 text-center text-sm text-white animate-fade-in-down">
        </footer>
    </div>
</template>

