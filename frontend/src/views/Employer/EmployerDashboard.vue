<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const jobs = ref([]);
const loading = ref(true);
const error = ref(null);

const showChatModal = ref(false);
const selectedApplication = ref(null);
const selectedJob = ref(null);
const chatMessage = ref('');

const openChatModal = (application, job) => {
    selectedApplication.value = application;
    selectedJob.value = job;
    showChatModal.value = true;
};

const sendFirstMessage = async () => {
    if (!chatMessage.value.trim()) return;

    try {
        const response = await axios.post('http://localhost:8000/api/conversations', {
            seeker_id: selectedApplication.value.user_id,
            job_listing_id: selectedJob.value.id,
            message: chatMessage.value
        }, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });

        showChatModal.value = false;
        chatMessage.value = '';
        router.push({ name: 'chat', query: { id: response.data.conversation.id } });
    } catch (e) {
        console.error('Failed to start chat', e);
        alert('Failed to start chat. Please try again.');
    }
};

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
                                    <button @click="openChatModal(application, job)" class="text-indigo-600 hover:text-indigo-900 mr-4">
                                        Chat
                                    </button>
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

    <!-- Chat Initiation Modal -->
    <div v-if="showChatModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-middle bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Start Conversation with {{ selectedApplication?.user.name }}</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">Respond to their application for "{{ selectedJob?.title }}".</p>
                        <textarea v-model="chatMessage" rows="4" class="mt-4 block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border border-gray-300 rounded-md" placeholder="Enter your message..."></textarea>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button @click="sendFirstMessage" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                        Send Message
                    </button>
                    <button @click="showChatModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
