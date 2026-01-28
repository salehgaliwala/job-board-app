<script setup>
import { onMounted, ref } from 'vue';
import { useJobStore } from '@/stores/job';
import JobCard from '@/components/job/JobCard.vue';
import JobFilters from '@/components/job/JobFilters.vue';

const jobStore = useJobStore();
const filters = ref({
    search: '',
    category_id: '',
    type: ''
});

onMounted(() => {
  jobStore.fetchJobs();
});

const handleFilterUpdate = (newFilters) => {
    filters.value = newFilters;
    // Reset to page 1 on filter change usually
    jobStore.fetchJobs({ ...newFilters, page: 1 });
};
</script>

<template>
  <div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Find Your Dream Job</h1>
        
        <JobFilters :initialFilters="filters" @update:filters="handleFilterUpdate" />
      </div>

      <!-- Job List -->
      <div v-if="jobStore.loading" class="text-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto"></div>
        <p class="mt-4 text-gray-500">Loading jobs...</p>
      </div>

      <div v-else-if="jobStore.error" class="text-center py-12 text-red-500">
        {{ jobStore.error }}
      </div>

      <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <JobCard 
            v-for="job in jobStore.jobs" 
            :key="job.id" 
            :job="job" 
        />
      </div>

      <!-- No Results -->
      <div v-if="!jobStore.loading && !jobStore.error && jobStore.jobs.length === 0" class="text-center py-12 bg-white rounded-lg shadow-sm">
        <p class="text-gray-500 text-lg">No jobs found matching your criteria.</p>
      </div>

      <!-- Pagination -->
      <div v-if="!jobStore.loading && jobStore.pagination.lastPage > 1" class="mt-8 flex justify-center gap-2">
         <button 
            :disabled="jobStore.pagination.currentPage === 1"
            @click="jobStore.fetchJobs({ ...filters, page: jobStore.pagination.currentPage - 1 })"
            class="px-4 py-2 border rounded-md disabled:opacity-50 hover:bg-gray-50"
         >
            Previous
         </button>
         <span class="px-4 py-2 text-gray-600 align-middle inline-flex items-center">
            Page {{ jobStore.pagination.currentPage }} of {{ jobStore.pagination.lastPage }}
         </span>
         <button 
            :disabled="jobStore.pagination.currentPage === jobStore.pagination.lastPage"
             @click="jobStore.fetchJobs({ ...filters, page: jobStore.pagination.currentPage + 1 })"
            class="px-4 py-2 border rounded-md disabled:opacity-50 hover:bg-gray-50"
         >
            Next
         </button>
      </div>

    </div>
  </div>
</template>
