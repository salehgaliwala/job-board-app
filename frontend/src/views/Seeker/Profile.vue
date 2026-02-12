<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const availableSkills = ref([]);
const selectedSkills = ref([]);
const message = ref('');
const error = ref('');

const fetchUserSkills = async () => {
    try {
        await authStore.fetchUser();
        if (authStore.user && authStore.user.skills) {
            selectedSkills.value = authStore.user.skills;
        }
    } catch (e) {
        console.error(e);
    }
};

const fetchSkills = async (query) => {
    if (!query) return;
    try {
        const response = await axios.get(`http://localhost:8000/api/skills?query=${query}`, {
             headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
        });
        availableSkills.value = response.data;
    } catch (e) {
        console.error(e);
    }
};

const addSkill = (skill) => {
    if (!selectedSkills.value.find(s => s.id === skill.id)) {
        selectedSkills.value.push(skill);
    }
    availableSkills.value = [];
};

const removeSkill = (skillId) => {
    selectedSkills.value = selectedSkills.value.filter(s => s.id !== skillId);
};

let timeout;
const onSkillInput = (e) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => fetchSkills(e.target.value), 300);
};

const saveProfile = async () => {
    message.value = '';
    error.value = '';
    try {
        await axios.post('http://localhost:8000/api/profile/skills', {
            skills: selectedSkills.value.map(s => s.id)
        }, {
             headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
        });
        message.value = 'Profile updated successfully.';
        await authStore.fetchUser(); 
    } catch (e) {
        console.error(e);
        error.value = 'Failed to update profile.';
    }
};

onMounted(() => {
    fetchUserSkills();
});
</script>

<template>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">My Profile</h1>

        <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Manage Skills</h2>
            
            <div v-if="message" class="mb-4 text-green-600">{{ message }}</div>
            <div v-if="error" class="mb-4 text-red-600">{{ error }}</div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Add Skills</label>
                <div class="mt-1 relative">
                    <input type="text" @input="onSkillInput"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 px-3"
                        placeholder="Type to search skills...">
                    
                    <div v-if="availableSkills.length" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                        <div v-for="skill in availableSkills" :key="skill.id" 
                            @click="addSkill(skill)"
                            class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-600 hover:text-white">
                            {{ skill.name }}
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap gap-2">
                    <span v-for="skill in selectedSkills" :key="skill.id" 
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        {{ skill.name }}
                        <button type="button" @click="removeSkill(skill.id)" class="ml-1 text-indigo-400 hover:text-indigo-600">×</button>
                    </span>
                    <span v-if="selectedSkills.length === 0" class="text-gray-500 text-sm italic">No skills added yet.</span>
                </div>
            </div>

            <div class="mt-6">
                <button @click="saveProfile" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</template>
