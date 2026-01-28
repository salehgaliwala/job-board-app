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
            path: '/contact',
            name: 'contact',
            component: () => import('../views/ContactView.vue')
        }
    ]
})

export default router
