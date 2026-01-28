<script setup>
import { ref, watch, onMounted } from 'vue';
import api from '@/services/api';
import { debounce } from 'lodash';

const props = defineProps({
  initialFilters: {
    type: Object,
    default: () => ({ search: '', category_id: '', type: '' })
  }
});

const emit = defineEmits(['update:filters']);

const filters = ref({ ...props.initialFilters });
const categories = ref([]);
const jobTypes = ref([]);

const fetchOptions = async () => {
    try {
        const [catRes, typeRes] = await Promise.all([
            api.get('/categories'),
            api.get('/job-types')
        ]);
        // Assuming the API returns { data: [...] } for resources or just direct array?
        // Let's assume standard Laravel resource response structure or what we defined. Actually we haven't defined CategoryController index response yet properly but usually it's a list.
        // Let's assume the controller returns simple JSON or Resource collection.
        categories.value = catRes.data.data || catRes.data; 
        jobTypes.value = typeRes.data.data || typeRes.data;
    } catch (e) {
        console.error("Error fetching filter options", e);
    }
};

onMounted(() => {
    fetchOptions();
});

const emitUpdate = debounce(() => {
    emit('update:filters', filters.value);
}, 300);

watch(filters, () => {
    emitUpdate();
}, { deep: true });
</script>

<template>
  <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    
    <!-- Search -->
    <div>
        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
        <input 
            v-model="filters.search"
            id="search"
            type="text" 
            placeholder="Keywords, companies..." 
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm py-2 px-3 border"
        >
    </div>

    <!-- Category -->
    <div>
        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
        <select 
            v-model="filters.category_id"
            id="category"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm py-2 px-3 border"
        >
            <option value="">All Categories</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                {{ cat.name }}
            </option>
        </select>
    </div>

    <!-- Job Type -->
    <div>
        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Job Type</label>
        <select 
            v-model="filters.type"
            id="type"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm py-2 px-3 border"
        >
            <option value="">All Types</option>
            <option v-for="type in jobTypes" :key="type.id" :value="type.id">
                {{ type.name }}
            </option>
        </select>
    </div>

  </div>
</template>
