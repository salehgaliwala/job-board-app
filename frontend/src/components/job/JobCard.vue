<script setup>
defineProps({
  job: {
    type: Object,
    required: true
  }
});
</script>

<template>
  <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 p-6 border border-slate-100">
    <div class="flex justify-between items-start">
      <div>
        <h3 class="text-xl font-bold text-gray-900 group-hover:text-primary-600 transition-colors">
            <router-link :to="{ name: 'job-details', params: { slug: job.slug } }">
                {{ job.title }}
            </router-link>
        </h3>
        <p class="text-sm text-gray-500 mt-1">{{ job.company.name }}</p>
      </div>
      <div v-if="job.company.logo" class="flex-shrink-0 ml-4">
        <img :src="job.company.logo" :alt="job.company.name" class="h-12 w-12 rounded-full object-cover bg-gray-50" />
      </div>
      <div v-else class="flex-shrink-0 ml-4 h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold text-lg">
        {{ job.company.name.charAt(0) }}
      </div>
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
      <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-800">
        {{ job.location }}
      </span>
      <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700">
        {{ job.type }}
      </span>
      <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700">
        {{ job.salary_range || 'Salary not specified' }}
      </span>
    </div>

    <div class="mt-4 text-sm text-gray-600 line-clamp-2">
        <!-- We might want to strip HTML tags if description is rich text, for now assuming it might be -->
         {{ job.description.replace(/<[^>]*>?/gm, '').substring(0, 150) }}...
    </div>

    <div class="mt-6 flex items-center justify-between">
      <span class="text-xs text-gray-400">{{ job.created_at }}</span>
      <router-link :to="{ name: 'job-details', params: { slug: job.slug } }" class="text-sm font-medium text-primary-600 hover:text-primary-700">
        View Details &rarr;
      </router-link>
    </div>
  </div>
</template>
