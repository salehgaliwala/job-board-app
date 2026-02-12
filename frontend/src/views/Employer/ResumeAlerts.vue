<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const alerts = ref([]);
const loading = ref(false);
const newAlert = ref({ name: '', keywords: '' });
const error = ref(null);

const fetchAlerts = async () => {
    loading.value = true;
    try {
        const response = await axios.get('http://localhost:8000/api/alerts', {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
        });
        alerts.value = response.data;
    } catch (e) {
        console.error(e);
        error.value = 'Failed to load alerts.';
    } finally {
        loading.value = false;
    }
};

const createAlert = async () => {
    try {
        await axios.post('http://localhost:8000/api/alerts', newAlert.value, {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
        });
        newAlert.value = { name: '', keywords: '' };
        fetchAlerts();
    } catch (e) {
        console.error(e);
        alert('Failed to create alert.');
    }
};

const deleteAlert = async (id) => {
    if (!confirm('Are you sure?')) return;
    try {
        await axios.delete(`http://localhost:8000/api/alerts/${id}`, {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
        });
        fetchAlerts();
    } catch (e) {
        console.error(e);
        alert('Failed to delete alert.');
    }
};

onMounted(() => {
    fetchAlerts();
});
</script>

<template>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Resume Alerts</h1>

        <!-- Create Alert Form -->
        <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Create New Alert</h2>
            <form @submit.prevent="createAlert" class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Alert Name</label>
                    <input type="text" v-model="newAlert.name" required
                        class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                        placeholder="e.g. Senior PHP Devs">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Keywords</label>
                    <input type="text" v-model="newAlert.keywords"
                        class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                        placeholder="e.g. Laravel, Vue, MySQL">
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Create Alert
                    </button>
                </div>
            </form>
        </div>

        <!-- Alerts List -->
        <div v-if="loading" class="text-center">Loading...</div>
        <div v-else-if="alerts.length === 0" class="text-center text-gray-500">No alerts configured.</div>
        <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul role="list" class="divide-y divide-gray-200">
                <li v-for="alert in alerts" :key="alert.id" class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">{{ alert.name }}</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Keywords: {{ alert.keywords }}</p>
                        </div>
                        <button @click="deleteAlert(alert.id)" class="text-red-600 hover:text-red-900">Delete</button>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>
