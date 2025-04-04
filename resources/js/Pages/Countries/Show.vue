<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
  country: Object,
});
</script>

<template>
  <AppLayout>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 p-6 border-b border-gray-200">
          <h2 class="text-2xl font-bold text-gray-900">{{ country.name }}</h2>
          <div class="flex gap-4">
            <Link
              :href="`/countries/${country.id}/edit`"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Edit
            </Link>
            <Link
              href="/countries"
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Back to Countries
            </Link>
          </div>
        </div>

        <!-- Country Details -->
        <div class="p-6">
          <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div>
              <dt class="text-sm font-medium text-gray-500">Country Code</dt>
              <dd class="mt-1 text-sm text-gray-900">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  {{ country.code }}
                </span>
              </dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Currency Code (Country)</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ country.currency_code || 'N/A' }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">WikiData ID</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ country.wikiDataId || 'N/A' }}</dd>
            </div>
          </dl>
        </div>

        <!-- Currency Details -->
        <div class="p-6 border-t border-gray-200">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Currency</h3>
          <dl v-if="country.currency" class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div>
              <dt class="text-sm font-medium text-gray-500">Code</dt>
              <dd class="mt-1 text-sm text-gray-900">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  {{ country.currency.code }}
                </span>
              </dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Symbol</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ country.currency.symbol || 'N/A' }}</dd>
            </div>
          </dl>
          <p v-else class="text-sm text-gray-500">No currency information available.</p>
        </div>

        <!-- Cities List -->
        <div class="p-6 border-t border-gray-200">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Cities</h3>
          <div v-if="country.cities && country.cities.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Population</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coordinates</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="city in country.cities" :key="city.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ city.name }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ city.region || 'N/A' }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ city.population || 'N/A' }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ city.latitude }}, {{ city.longitude }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <p v-else class="text-sm text-gray-500">No cities found for this country.</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
