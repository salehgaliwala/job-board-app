<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';

const jobs = ref([]);
const loading = ref(true);
const error = ref(null);

onMounted(async () => {
    try {
        const response = await axios.get('http://localhost:8000/api/employer/applications', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        jobs.value = response.data;
    } catch (e) {
        error.value = 'Failed to load applications.';
        console.error(e);
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Employer Dashboard</h1>
            <div class="flex space-x-4">
                <router-link :to="{ name: 'candidate-search' }" class="text-indigo-600 hover:text-indigo-900">Candidate Search</router-link>
                <router-link :to="{ name: 'resume-alerts' }" class="text-indigo-600 hover:text-indigo-900">Resume Alerts</router-link>
            </div>
        </div>

        <div v-if="loading" class="text-center py-4">Loading applications...</div>
        <div v-else-if="error" class="text-red-600 py-4">{{ error }}</div>

        <div v-else class="space-y-6">
            <div v-if="jobs.length === 0" class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
                <p>You haven't posted any jobs yet.</p>
            </div>

            <div v-for="job in jobs" :key="job.id" class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ job.title }}</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        {{ job.applications.length }} Application{{ job.applications.length !== 1 ? 's' : '' }}
                    </p>
                </div>
                <div class="border-t border-gray-200">
                    <ul v-if="job.applications.length > 0" role="list" class="divide-y divide-gray-200">
                        <li v-for="application in job.applications" :key="application.id" class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-medium text-indigo-600 truncate">
                                    {{ application.user.name }}
                                </div>
                                <div class="ml-2 flex-shrink-0 flex">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="{
                                            'bg-green-100 text-green-800': application.status === 'hired',
                                            'bg-yellow-100 text-yellow-800': application.status === 'pending',
                                            'bg-red-100 text-red-800': application.status === 'rejected'
                                        }">
                                        {{ application.status }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <p class="flex items-center text-sm text-gray-500">
                                        {{ application.user.email }}
                                    </p>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                    <router-link :to="{ name: 'candidate-match', params: { id: job.id }}" class="text-indigo-600 hover:text-indigo-900 mr-4">
                                        Find Candidates
                                    </router-link>
                                    <a :href="'http://localhost:8000/storage/' + application.resume_path" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                        View Resume
                                    </a>
                                </div>
                            </div>
                            <div v-if="application.cover_letter" class="mt-2 text-sm text-gray-500 bg-gray-50 p-3 rounded">
                                <p class="font-medium">Cover Letter:</p>
                                <p>{{ application.cover_letter }}</p>
                            </div>
                        </li>
                    </ul>
                    <div v-else class="px-4 py-5 sm:px-6 text-sm text-gray-500">
                        No applications received yet.
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
