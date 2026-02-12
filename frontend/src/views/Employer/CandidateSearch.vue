<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const candidates = ref([]);
const loading = ref(false);
const searchQuery = ref('');
const skillsQuery = ref([]); // Array of skill IDs
const availableSkills = ref([]);
const selectedSkills = ref([]);

const search = async () => {
    loading.value = true;
    try {
        const params = {};
        if (searchQuery.value) params.search = searchQuery.value;
        if (skillsQuery.value.length) params.skills = skillsQuery.value;

        const response = await axios.get('http://localhost:8000/api/candidates', {
            params,
            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
        });
        candidates.value = response.data.data;
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
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
        skillsQuery.value.push(skill.id);
    }
    availableSkills.value = [];
};

const removeSkill = (skillId) => {
    selectedSkills.value = selectedSkills.value.filter(s => s.id !== skillId);
    skillsQuery.value = skillsQuery.value.filter(id => id !== skillId);
};

// Debounce skill search
let timeout;
const onSkillInput = (e) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => fetchSkills(e.target.value), 300);
};

onMounted(() => {
    search();
});
</script>

<template>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Candidate Search</h1>

        <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">Keywords (Boolean Search)</label>
                    <div class="mt-1">
                        <input type="text" v-model="searchQuery" @keyup.enter="search" 
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                            placeholder="e.g. PHP AND Laravel">
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">Skills (Smart Search)</label>
                    <div class="mt-1 relative">
                        <input type="text" @input="onSkillInput"
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                            placeholder="Type to search skills...">
                        
                        <div v-if="availableSkills.length" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                            <div v-for="skill in availableSkills" :key="skill.id" 
                                @click="addSkill(skill)"
                                class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-600 hover:text-white">
                                {{ skill.name }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span v-for="skill in selectedSkills" :key="skill.id" 
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            {{ skill.name }}
                            <button type="button" @click="removeSkill(skill.id)" class="ml-1 text-indigo-400 hover:text-indigo-600">×</button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button @click="search" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Search Candidates
                </button>
            </div>
        </div>

        <div v-if="loading" class="text-center">Loading...</div>
        
        <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul role="list" class="divide-y divide-gray-200">
                <li v-for="candidate in candidates" :key="candidate.id">
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-indigo-600 truncate">{{ candidate.name }}</p>
                            <div class="ml-2 flex-shrink-0 flex">
                                <span v-if="candidate.skills_count !== undefined" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
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
                No candidates found.
            </div>
        </div>
    </div>
</template>
