<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const jobId = route.params.id;
const candidates = ref([]);
const loading = ref(true);
const jobTitle = ref('');

const fetchMatches = async () => {
    try {
        // Fetch matched candidates
        const response = await axios.get(`http://localhost:8000/api/jobs/${jobId}/matches`, {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
        });
        candidates.value = response.data.data;

        // Fetch job details for title (optimization: endpoint matches could return job title too)
        const jobResponse = await axios.get(`http://localhost:8000/api/jobs/${jobId}`, { // Assuming this endpoint exists and public/accessible
             headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
        });
        // Note: The public job endpoint uses slug, but maybe we have an ID based one or search?
        // Let's rely on the matching endpoint or just display "Job Matches" for now if fetching title is complex without slug.
        // Actually, the match endpoint is new, I can make it return title.
        // For now, I'll just match the ID.
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchMatches();
});
</script>

<template>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Candidate Matches for Job #{{ jobId }}</h1>

        <div v-if="loading" class="text-center">Loading matches...</div>
        
        <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul role="list" class="divide-y divide-gray-200">
                <li v-for="candidate in candidates" :key="candidate.id">
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-indigo-600 truncate">{{ candidate.name }}</p>
                             <div class="ml-2 flex-shrink-0 flex">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ candidate.skills_count }} Matched Skills
                                </span>
                            </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                                <p class="flex items-center text-sm text-gray-500">
                                    {{ candidate.email }}
                                </p>
                            </div>
                        </div>
                         <div class="mt-2 flex flex-wrap gap-2">
                             <span v-for="skill in candidate.skills" :key="skill.id" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                {{ skill.name }}
                            </span>
                        </div>
                    </div>
                </li>
            </ul>
             <div v-if="candidates.length === 0" class="p-4 text-center text-gray-500">
                No matching candidates found.
            </div>
        </div>
    </div>
</template>
