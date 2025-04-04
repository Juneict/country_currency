<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

defineProps({
  countries: Array,
});

const form = useForm({
  name: '',
  country_id: '',
  region: '',
  region_code: '',
  latitude: '',
  longitude: '',
  population: '',
});
</script>

<template>
  <AppLayout>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
      <div class="bg-white shadow-sm rounded-lg p-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
          <h2 class="text-2xl font-bold text-gray-900">Add New City</h2>
          <Link
            href="/countries"
            class="text-gray-600 hover:text-gray-900 flex items-center text-sm font-medium"
          >
            ← Back to Countries
          </Link>
        </div>

        <!-- Form Container -->
        <form @submit.prevent="form.post('/cities')" class="space-y-6">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">City Name</label>
            <input
              v-model="form.name"
              id="name"
              type="text"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3 border"
              :class="{ 'border-red-500': form.errors.name }"
            >
            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="country_id" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
              <select
                v-model="form.country_id"
                id="country_id"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3 border"
                :class="{ 'border-red-500': form.errors.country_id }"
              >
                <option value="">Select a country</option>
                <option
                  v-for="country in countries"
                  :key="country.id"
                  :value="country.id"
                >
                  {{ country.name }} ({{ country.code }})
                </option>
              </select>
              <p v-if="form.errors.country_id" class="mt-1 text-sm text-red-600">{{ form.errors.country_id }}</p>
            </div>

            <div>
              <label for="region" class="block text-sm font-medium text-gray-700 mb-1">Region</label>
              <input
                v-model="form.region"
                id="region"
                type="text"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3 border"
                :class="{ 'border-red-500': form.errors.region }"
              >
              <p v-if="form.errors.region" class="mt-1 text-sm text-red-600">{{ form.errors.region }}</p>
            </div>
          </div>

          <div>
            <label for="region_code" class="block text-sm font-medium text-gray-700 mb-1">Region Code</label>
            <input
              v-model="form.region_code"
              id="region_code"
              type="text"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3 border"
              :class="{ 'border-red-500': form.errors.region_code }"
            >
            <p v-if="form.errors.region_code" class="mt-1 text-sm text-red-600">{{ form.errors.region_code }}</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="latitude" class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
              <input
                v-model="form.latitude"
                id="latitude"
                type="text"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3 border"
                :class="{ 'border-red-500': form.errors.latitude }"
              >
              <p v-if="form.errors.latitude" class="mt-1 text-sm text-red-600">{{ form.errors.latitude }}</p>
            </div>

            <div>
              <label for="longitude" class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
              <input
                v-model="form.longitude"
                id="longitude"
                type="text"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3 border"
                :class="{ 'border-red-500': form.errors.longitude }"
              >
              <p v-if="form.errors.longitude" class="mt-1 text-sm text-red-600">{{ form.errors.longitude }}</p>
            </div>
          </div>

          <div>
            <label for="population" class="block text-sm font-medium text-gray-700 mb-1">Population</label>
            <input
              v-model="form.population"
              id="population"
              type="text"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3 border"
              :class="{ 'border-red-500': form.errors.population }"
            >
            <p v-if="form.errors.population" class="mt-1 text-sm text-red-600">{{ form.errors.population }}</p>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
            <Link
              href="/countries"
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Cancel
            </Link>
            <button
              type="submit"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              :disabled="form.processing"
              :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
            >
              <span v-if="form.processing">Saving...</span>
              <span v-else>Save City</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
