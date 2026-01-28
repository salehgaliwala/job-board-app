import { defineStore } from 'pinia';
import api from '../services/api';

export const useJobStore = defineStore('job', {
    state: () => ({
        jobs: [],
        job: null,
        loading: false,
        error: null,
        pagination: {
            currentPage: 1,
            lastPage: 1,
            total: 0,
        }
    }),

    actions: {
        async fetchJobs(filters = {}) {
            this.loading = true;
            this.error = null;
            try {
                const params = {
                    page: this.pagination.currentPage,
                    ...filters
                };
                const response = await api.get('/jobs', { params });
                this.jobs = response.data.data;
                this.pagination.currentPage = response.data.meta.current_page;
                this.pagination.lastPage = response.data.meta.last_page;
                this.pagination.total = response.data.meta.total;
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to fetch jobs';
                console.error('Error fetching jobs:', err);
            } finally {
                this.loading = false;
            }
        },

        async fetchJob(slug) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.get(`/jobs/${slug}`);
                this.job = response.data.data;
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to fetch job details';
            } finally {
                this.loading = false;
            }
        },

        async createJob(jobData) {
            this.loading = true;
            this.error = null;
            try {
                await api.post('/jobs', jobData);
                // We could refresh the list or just let the navigation handle it
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to create job';
                throw err; // Re-throw to handle in component (e.g. valid errors)
            } finally {
                this.loading = false;
            }
        }
    }
});
