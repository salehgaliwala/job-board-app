import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'home',
            component: HomeView
        },
        {
            path: '/login',
            name: 'login',
            component: () => import('../views/Auth/LoginView.vue')
        },
        {
            path: '/register',
            name: 'register',
            component: () => import('../views/Auth/RegisterView.vue')
        },
        {
            path: '/jobs',
            name: 'jobs',
            component: () => import('../views/Jobs/JobIndex.vue')
        },
        {
            path: '/jobs/create',
            name: 'job-create',
            component: () => import('../views/Jobs/JobCreate.vue'),
            meta: { requiresAuth: true }
        },
        {
            path: '/jobs/:slug',
            name: 'job-details',
            component: () => import('../views/Jobs/JobShow.vue')
        },
        {
            path: '/about',
            name: 'about',
            component: () => import('../views/AboutView.vue')
        },
        {
            path: '/employer/dashboard',
            name: 'employer-dashboard',
            component: () => import('../views/Employer/EmployerDashboard.vue'),
            meta: { requiresAuth: true }
        },
        {
            path: '/contact',
            name: 'contact',
            component: () => import('../views/ContactView.vue')
        }
    ]
})

import { useAuthStore } from '@/stores/auth';

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    // Check if route requires auth
    if (to.meta.requiresAuth) {
        if (!authStore.isAuthenticated) {
            // Try to fetch user if we suspect they might be logged in (e.g. cookie exists)
            // But usually fetchUser is called on App mount. 
            // If checking fails here, redirect.
            if (!authStore.user) {
                try {
                    await authStore.fetchUser();
                    if (authStore.isAuthenticated) next();
                    else next('/login');
                } catch (e) {
                    next('/login');
                }
            } else {
                next();
            }
        } else {
            next();
        }
    } else {
        next();
    }
});

export default router
