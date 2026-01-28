<script setup>
import { ref, onMounted } from 'vue'
import { RouterView, useRouter } from 'vue-router'
import { Bars3Icon, XMarkIcon, UserCircleIcon } from '@heroicons/vue/24/outline'
import Footer from '@/components/layout/Footer.vue'
import { useAuthStore } from '@/stores/auth'

const mobileMenuOpen = ref(false)
const authStore = useAuthStore()
const router = useRouter()

onMounted(() => {
    authStore.fetchUser();
})

const handleLogout = async () => {
    await authStore.logout();
    router.push('/');
}

const navigation = [
  { name: 'Home', to: '/' },
  { name: 'Find Jobs', to: '/jobs' },
  { name: 'About Us', to: '/about' },
  { name: 'Contact Us', to: '/contact' },
];
</script>

<template>
  <div class="min-h-screen bg-slate-50">
    <header class="bg-white shadow">
      <nav class="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8" aria-label="Global">
        <div class="flex lg:flex-1">
          <router-link to="/" class="-m-1.5 p-1.5">
            <span class="text-2xl font-bold text-gray-900">Job Board</span>
          </router-link>
        </div>
        <div class="flex lg:hidden">
          <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700" @click="mobileMenuOpen = true">
            <span class="sr-only">Open main menu</span>
            <Bars3Icon class="h-6 w-6" aria-hidden="true" />
          </button>
        </div>
        <div class="hidden lg:flex lg:gap-x-12">
            <router-link 
            v-for="item in navigation" 
            :key="item.name" 
            :to="item.to" 
            class="text-sm font-semibold leading-6 text-gray-900 hover:text-primary-600"
            active-class="text-primary-600 font-bold"
          >
            {{ item.name }}
          </router-link>
        </div>
        <div class="hidden lg:flex lg:flex-1 lg:justify-end lg:gap-x-4 items-center" v-if="!authStore.isAuthenticated">
             <router-link to="/login" class="text-sm font-semibold leading-6 text-gray-900">Log in</router-link>
             <router-link to="/register" class="text-sm font-semibold leading-6 text-white bg-primary-600 px-3 py-2 rounded-md hover:bg-primary-500">Register</router-link>
        </div>
        <div class="hidden lg:flex lg:flex-1 lg:justify-end lg:gap-x-4 items-center" v-else>
             <div class="flex items-center gap-x-2">
                <UserCircleIcon class="h-8 w-8 text-gray-500" />
                <span class="text-sm font-semibold leading-6 text-gray-900">{{ authStore.user?.name }}</span>
             </div>
             <button @click="handleLogout" class="text-sm font-semibold leading-6 text-gray-900 hover:text-red-600">Log out</button>
             <router-link v-if="authStore.user?.role === 'employer'" to="/jobs/create" class="text-sm font-semibold leading-6 text-white bg-primary-600 px-3 py-2 rounded-md hover:bg-primary-500">Post a Job</router-link>
        </div>
      </nav>
      <!-- Mobile menu, show/hide based on menu state. -->
        <div v-show="mobileMenuOpen" class="lg:hidden" role="dialog" aria-modal="true">
            <!-- Background backdrop, show/hide based on slide-over state. -->
            <div class="fixed inset-0 z-10"></div>
            <div class="fixed inset-y-0 right-0 z-10 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
                <div class="flex items-center justify-between">
                    <router-link to="/" class="-m-1.5 p-1.5">
                        <span class="text-2xl font-bold text-gray-900">Job Board</span>
                    </router-link>
                    <button type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700" @click="mobileMenuOpen = false">
                        <span class="sr-only">Close menu</span>
                        <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                    </button>
                </div>
                <div class="mt-6 flow-root">
                    <div class="-my-6 divide-y divide-gray-500/10">
                        <div class="space-y-2 py-6">
                            <router-link 
                                v-for="item in navigation" 
                                :key="item.name" 
                                :to="item.to" 
                                class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50"
                                active-class="text-primary-600 bg-gray-50"
                                @click="mobileMenuOpen = false"
                            >
                                {{ item.name }}
                            </router-link>
                        </div>
                        <div class="py-6 space-y-2" v-if="!authStore.isAuthenticated">
                            <router-link to="/login" class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50" @click="mobileMenuOpen = false">Log in</router-link>
                             <router-link to="/register" class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-primary-600 hover:bg-gray-50" @click="mobileMenuOpen = false">Register</router-link>
                        </div>
                         <div class="py-6 space-y-2" v-else>
                            <div class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900">
                                <span class="flex items-center gap-2">
                                    <UserCircleIcon class="h-5 w-5" />
                                    {{ authStore.user?.name }}
                                </span>
                            </div>
                            <router-link v-if="authStore.user?.role === 'employer'" to="/jobs/create" class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-primary-600 hover:bg-gray-50" @click="mobileMenuOpen = false">Post a Job</router-link>
                             <button @click="handleLogout(); mobileMenuOpen = false" class="-mx-3 block w-full text-left rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-red-600 hover:bg-gray-50">Log out</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main class="flex-grow">
        <RouterView />
    </main>
    <Footer />
  </div>
</template>
