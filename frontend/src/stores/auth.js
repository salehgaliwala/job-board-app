import { defineStore } from 'pinia';
import api from '../services/api';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        isAuthenticated: false,
        errors: {}
    }),

    actions: {
        async register(data) {
            try {
                await api.get('http://localhost:8000/sanctum/csrf-cookie', { baseURL: '' });
                await api.post('/register', data);
                await this.fetchUser();
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                }
                throw error;
            }
        },

        async login(credentials) {
            try {
                await api.get('http://localhost:8000/sanctum/csrf-cookie', { baseURL: '' });
                await api.post('/login', credentials);
                await this.fetchUser();
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                }
                throw error;
            }
        },

        async logout() {
            try {
                await api.post('/logout');
                this.user = null;
                this.isAuthenticated = false;
                // Clean up local storage logic if handled manually, usually sanctum handles cookies
            } catch (error) {
                console.error('Logout failed', error);
            }
        },

        async fetchUser() {
            try {
                const response = await api.get('/user');
                this.user = response.data;
                this.isAuthenticated = true;
            } catch (error) {
                console.error('Fetch user failed:', error);
                this.user = null;
                this.isAuthenticated = false;
            }
        }
    }
});
