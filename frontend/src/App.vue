<script setup>
import { RouterView } from 'vue-router'
</script>

<template>
  <div class="min-h-screen bg-slate-50">
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between">
        <h1 class="text-3xl font-bold text-gray-900">
          Job Board
        </h1>
        <nav>
           <!-- Navigation Links will go here -->
        </nav>
      </div>
    </header>
    <main>
      <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <RouterView />
      </div>
    </main>
  </div>
</template>
