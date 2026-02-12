<script setup>
import { ref, onMounted } from 'vue';
import { useJobStore } from '@/stores/job';
import { useRouter } from 'vue-router';
import api from '@/services/api';

import { useAuthStore } from '@/stores/auth';

const jobStore = useJobStore();
const authStore = useAuthStore();
const router = useRouter();

const form = ref({
    title: '',
    company_name: '',
    category_id: '',
    job_type_id: '',
    location: '',
    salary_range: '',
    description: '',
    skills: []
});

const categories = ref([]);
const jobTypes = ref([]);
const errors = ref({});

const availableSkills = ref([]);
const selectedSkills = ref([]);

const fetchSkills = async (query) => {
    if (!query) return;
    try {
        const response = await api.get(`/skills?query=${query}`);
        availableSkills.value = response.data;
    } catch (e) {
        console.error(e);
    }
};

const addSkill = (skill) => {
    if (!selectedSkills.value.find(s => s.id === skill.id)) {
        selectedSkills.value.push(skill);
        form.value.skills.push(skill.id);
    }
    availableSkills.value = [];
};

const removeSkill = (skillId) => {
    selectedSkills.value = selectedSkills.value.filter(s => s.id !== skillId);
    form.value.skills = form.value.skills.filter(id => id !== skillId);
};

let timeout;
const onSkillInput = (e) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => fetchSkills(e.target.value), 300);
};

const fetchOptions = async () => {
    try {
        const [catRes, typeRes] = await Promise.all([
            api.get('/categories'),
            api.get('/job-types')
        ]);
        categories.value = catRes.data.data || catRes.data;
        jobTypes.value = typeRes.data.data || typeRes.data;
    } catch (e) {
        console.error("Error fetching options", e);
    }
};

onMounted(async () => {
    await authStore.fetchUser();
    if (authStore.user?.company) {
        form.value.company_name = authStore.user.company.name;
    }
    fetchOptions();
});

const handleSubmit = async () => {
    errors.value = {};
    try {
        await jobStore.createJob(form.value);
        router.push('/jobs');
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            console.error("Submission error", error);
        }
    }
};
</script>

<template>
  <div class="max-w-2xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-8">
      <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Post a New Job</h2>
      </div>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-6 bg-white p-6 shadow-sm rounded-lg border border-gray-100">
        
      <!-- Title -->
      <div>
        <label for="title" class="block text-sm font-medium leading-6 text-gray-900">Job Title</label>
        <div class="mt-2">
          <input v-model="form.title" type="text" name="title" id="title" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 px-3" placeholder="e.g. Senior Software Engineer" />
          <p v-if="errors.title" class="mt-2 text-sm text-red-600">{{ errors.title[0] }}</p>
        </div>
      </div>

      <!-- Company Name -->
      <div>
        <label for="company_name" class="block text-sm font-medium leading-6 text-gray-900">Company Name</label>
        <div class="mt-2">
          <input v-model="form.company_name" type="text" name="company_name" id="company_name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 px-3" placeholder="e.g. Acme Corp" />
          <p v-if="errors.company_name" class="mt-2 text-sm text-red-600">{{ errors.company_name[0] }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
        <!-- Category -->
        <div>
            <label for="category" class="block text-sm font-medium leading-6 text-gray-900">Category</label>
            <div class="mt-2">
                <select v-model="form.category_id" id="category" name="category" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6 px-3">
                    <option value="" disabled>Select a category</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
                <p v-if="errors.category_id" class="mt-2 text-sm text-red-600">{{ errors.category_id[0] }}</p>
            </div>
        </div>

        <!-- Job Type -->
        <div>
            <label for="type" class="block text-sm font-medium leading-6 text-gray-900">Job Type</label>
            <div class="mt-2">
                <select v-model="form.job_type_id" id="type" name="type" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:max-w-xs sm:text-sm sm:leading-6 px-3">
                    <option value="" disabled>Select a job type</option>
                    <option v-for="type in jobTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
                </select>
                <p v-if="errors.job_type_id" class="mt-2 text-sm text-red-600">{{ errors.job_type_id[0] }}</p>
            </div>
        </div>
      </div>

      <!-- Location -->
      <div>
        <label for="location" class="block text-sm font-medium leading-6 text-gray-900">Location</label>
        <div class="mt-2">
          <input v-model="form.location" type="text" name="location" id="location" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 px-3" placeholder="e.g. Remote, New York, NY" />
           <p v-if="errors.location" class="mt-2 text-sm text-red-600">{{ errors.location[0] }}</p>
        </div>
      </div>

      <!-- Salary Range -->
      <div>
        <label for="salary_range" class="block text-sm font-medium leading-6 text-gray-900">Salary Range</label>
        <div class="mt-2">
          <input v-model="form.salary_range" type="text" name="salary_range" id="salary_range" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 px-3" placeholder="e.g. $100k - $120k" />
           <p v-if="errors.salary_range" class="mt-2 text-sm text-red-600">{{ errors.salary_range[0] }}</p>
        </div>
      </div>

      <!-- Description -->
      <div>
        <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Job Description</label>
        <div class="mt-2">
          <textarea v-model="form.description" id="description" name="description" rows="5" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 px-3"></textarea>
           <p v-if="errors.description" class="mt-2 text-sm text-red-600">{{ errors.description[0] }}</p>
        </div>
      </div>

      <!-- Skills -->
      <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Required Skills</label>
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
            <div class="mt-2 flex flex-wrap gap-2">
                <span v-for="skill in selectedSkills" :key="skill.id" 
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                    {{ skill.name }}
                    <button type="button" @click="removeSkill(skill.id)" class="ml-1 text-indigo-400 hover:text-indigo-600">×</button>
                </span>
            </div>
      </div>

      <div class="flex justify-end gap-x-3">
        <router-link to="/jobs" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Cancel</router-link>
        <button type="submit" :disabled="jobStore.loading" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 disabled:opacity-50">Post Job</button>
      </div>

    </form>
  </div>
</template>
