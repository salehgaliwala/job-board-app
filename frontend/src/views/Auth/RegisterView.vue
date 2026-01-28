<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'employer' // Defaulting to employer for demo purposes as user likely wants to post jobs
});

const handleRegister = async () => {
    try {
        await authStore.register(form.value);
        router.push('/jobs');
    } catch (error) {
        console.error("Register Error", error);
    }
}
</script>

<template>
  <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
      <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Create your account</h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <form class="space-y-6" @submit.prevent="handleRegister">
        
        <div v-if="authStore.errors && Object.keys(authStore.errors).length > 0" class="bg-red-50 text-red-500 p-3 rounded text-sm mb-4">
             <ul>
                <li v-for="(errors, field) in authStore.errors" :key="field">{{ errors[0] }}</li>
             </ul>
        </div>
        
        <div>
          <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Full Name</label>
          <div class="mt-2">
            <input v-model="form.name" id="name" name="name" type="text" required="" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 px-2" />
          </div>
        </div>

        <div>
          <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
          <div class="mt-2">
            <input v-model="form.email" id="email" name="email" type="email" autocomplete="email" required="" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 px-2" />
          </div>
        </div>

        <div>
          <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
          <div class="mt-2">
            <input v-model="form.password" id="password" name="password" type="password" autocomplete="new-password" required="" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 px-2" />
          </div>
        </div>

        <div>
           <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">Confirm Password</label>
            <div class="mt-2">
             <input v-model="form.password_confirmation" id="password_confirmation" name="password_confirmation" type="password" required="" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 px-2" />
            </div>
        </div>

        <!-- Role Selection -->
         <div>
            <label for="role" class="block text-sm font-medium leading-6 text-gray-900">I am a...</label>
            <select v-model="form.role" id="role" name="role" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                <option value="seeker">Job Seeker</option>
                <option value="employer">Employer</option>
            </select>
        </div>


        <div>
          <button type="submit" class="flex w-full justify-center rounded-md bg-primary-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">Register</button>
        </div>
      </form>

      <p class="mt-10 text-center text-sm text-gray-500">
        Already have an account?
        {{ ' ' }}
        <router-link to="/login" class="font-semibold leading-6 text-primary-600 hover:text-primary-500">Sign in</router-link>
      </p>
    </div>
  </div>
</template>
