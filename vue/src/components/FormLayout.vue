<script setup>

</script>

<template>
    <div class="min-h-screen flex-none bg-gray-900 ">
        <!-- Top strip / logo -->
        <header class="p-4 bg-grey-900 text-center justify-center items-center animate-fade-in-down">
            <img class="mx-auto w-auto size-25" src="../assets/xrmisalNoText.svg" alt="Your Company" />
            <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-white"></h2>
        </header>
        
        <!-- slot for login/register forms -->
        <main class="flex-1 flex justify-center px-2-full max-w-s">
            <router-view />
        </main>
        
        <!-- Bottom nav -->
        <footer class="p-4 text-center text-sm text-white animate-fade-in-down">
        </footer>
    </div>
</template>

