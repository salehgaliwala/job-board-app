<script setup>
import { ref } from 'vue';

const form = ref({
  name: '',
  email: '',
  message: ''
});

const submitted = ref(false);

const submitForm = () => {
  // Simulate form submission
  console.log('Form submitted:', form.value);
  submitted.value = true;
};
</script>

<template>
  <div class="max-w-xl mx-auto py-12 px-4">
    <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">Contact Us</h1>
    
    <div v-if="submitted" class="bg-green-50 border border-green-200 rounded-md p-4 text-center">
      <p class="text-green-700 font-medium">Thank you for contacting us! We'll get back to you soon.</p>
      <button @click="submitted = false; form = { name: '', email: '', message: '' }" class="mt-4 text-sm text-green-600 hover:text-green-500 underline">Send another message</button>
    </div>

    <form v-else @submit.prevent="submitForm" class="space-y-6 bg-white p-8 rounded-lg shadow-sm border border-gray-100">
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
        <div class="mt-1">
          <input v-model="form.name" type="text" name="name" id="name" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm border p-2">
        </div>
      </div>

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <div class="mt-1">
          <input v-model="form.email" type="email" name="email" id="email" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm border p-2">
        </div>
      </div>

      <div>
        <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
        <div class="mt-1">
          <textarea v-model="form.message" id="message" name="message" rows="4" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm border p-2"></textarea>
        </div>
      </div>

      <div>
        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
          Send Message
        </button>
      </div>
    </form>
  </div>
</template>
