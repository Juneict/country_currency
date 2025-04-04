<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
  countries: Array,
});

const viewCities = (countryId) => {
  window.location.href = `/cities/${countryId}`;
};
</script>

<template>
  <AppLayout>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
      <!-- Header Section -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Countries Management</h2>
        <div class="flex gap-4">
          <Link
            href="/countries/create"
            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md shadow-sm transition-colors duration-200"
          >
            Add New Country
          </Link>
        </div>
      </div>

      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Currency Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5">Currencies</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">wikiDataId</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="country in countries" :key="country.id" class="hover:bg-gray-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ country.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ country.code }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ country.currency_code || 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <span v-if="country.currency" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    {{ country.currency.code }} ({{ country.currency.symbol || 'N/A' }})
                  </span>
                  <span v-else class="text-gray-400">No currency</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ country.wikiDataId || 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center space-x-3">
                    <button
                      @click="viewCities(country.id)"
                      class="text-purple-600 hover:text-purple-900 transition-colors duration-200"
                    >
                      View Cities
                    </button>
                    <span class="text-gray-300">|</span>
                    <Link
                      :href="`/countries/${country.id}`"
                      class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200"
                    >
                    View
                    </Link>
                    <span class="text-gray-300">|</span>
                    <Link
                      :href="`/countries/${country.id}/edit`"
                      class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200"
                    >
                      Edit
                    </Link>
                    <span class="text-gray-300">|</span>
                    <Link
                      :href="`/countries/${country.id}`"
                      method="delete"
                      as="button"
                      class="text-red-600 hover:text-red-900 transition-colors duration-200"
                    >
                      Delete
                    </Link>
                  </div>
                </td>
              </tr>
              <tr v-if="countries.length === 0">
                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                  No countries found. Add your first country!
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
