<script setup>
import { onMounted, ref, computed } from 'vue';
import { useJobStore } from '@/stores/job';
import JobCard from '@/components/job/JobCard.vue';
import heroBg from '@/assets/hero_background.png';

const jobStore = useJobStore();
const featuredJobs = computed(() => jobStore.jobs.slice(0, 8));

onMounted(async () => {
    await jobStore.fetchJobs();
});

const companies = [
    { name: 'TechCorp', logo: 'https://ui-avatars.com/api/?name=Tech+Corp&background=0D8ABC&color=fff' },
    { name: 'InnovateX', logo: 'https://ui-avatars.com/api/?name=Innovate+X&background=6366F1&color=fff' },
    { name: 'GreenEnergy', logo: 'https://ui-avatars.com/api/?name=Green+Energy&background=10B981&color=fff' },
    { name: 'FinanceFlow', logo: 'https://ui-avatars.com/api/?name=Finance+Flow&background=F59E0B&color=fff' },
    { name: 'HealthPlus', logo: 'https://ui-avatars.com/api/?name=Health+Plus&background=EC4899&color=fff' },
];
</script>

<template>
  <main>
    <!-- Hero Section -->
    <div class="relative bg-gray-900 isolate overflow-hidden pt-14 pb-16 sm:pb-20">
      <img :src="heroBg" alt="Office background" class="absolute inset-0 -z-10 h-full w-full object-cover opacity-20" />
      <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl py-32 sm:py-48 lg:py-56 text-center">
          <h1 class="text-4xl font-bold tracking-tight text-white sm:text-6xl">Find Your Dream Job Today</h1>
          <p class="mt-6 text-lg leading-8 text-gray-300">Discover thousands of job opportunities from top companies. Your next career move is just a click away.</p>
          <div class="mt-10 flex items-center justify-center gap-x-6">
            <router-link to="/jobs" class="rounded-md bg-primary-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">Browse Jobs</router-link>
            <router-link to="/register" class="text-sm font-semibold leading-6 text-white">Get Started <span aria-hidden="true">→</span></router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Featured Jobs Section -->
    <div class="bg-white py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Featured Jobs</h2>
                <p class="mt-2 text-lg leading-8 text-gray-600">Explore the latest opportunities hand-picked for you.</p>
            </div>
            <div class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-3 sm:grid-cols-2">
                <JobCard v-for="job in featuredJobs" :key="job.id" :job="job" />
            </div>
             <div class="mt-10 text-center">
                <router-link to="/jobs" class="text-sm font-semibold leading-6 text-primary-600 hover:text-primary-500">View all jobs <span aria-hidden="true">→</span></router-link>
            </div>
        </div>
    </div>

    <!-- Trusted Companies Section -->
    <div class="bg-slate-50 py-24 sm:py-32">
      <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <h2 class="text-center text-lg font-semibold leading-8 text-gray-900">Trusted by the world’s most innovative teams</h2>
        <div class="mx-auto mt-10 grid max-w-lg grid-cols-4 items-center gap-x-8 gap-y-10 sm:max-w-xl sm:grid-cols-6 sm:gap-x-10 lg:mx-0 lg:max-w-none lg:grid-cols-5">
            <div v-for="company in companies" :key="company.name" class="col-span-2 lg:col-span-1 flex justify-center grayscale hover:grayscale-0 transition-all duration-300">
                <img class="max-h-12 w-full object-contain" :src="company.logo" :alt="company.name" />
            </div>
        </div>
      </div>
    </div>
  </main>
</template>
