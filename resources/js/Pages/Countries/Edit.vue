<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
  country: Object,
});

const form = useForm({
  name: props.country.name,
  code: props.country.code,
  currency_code: props.country.currency_code,
  wikiDataId: props.country.wikiDataId,
  currency: {
    code: props.country.currency?.code || '',
    symbol: props.country.currency?.symbol || '',
  },
});

const submit = () => {
  form.put(`/countries/${props.country.id}`, {
    onSuccess: () => {
      form.reset();
    },
  });
};
</script>

<template>
  <AppLayout>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
      <div class="bg-white shadow-sm rounded-lg p-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
          <h2 class="text-2xl font-bold text-gray-900">Edit Country</h2>
          <Link
            href="/countries"
            class="text-gray-600 hover:text-gray-900 flex items-center text-sm font-medium"
          >
            ← Back to Countries
          </Link>
        </div>

        <!-- Form Container -->
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Name Field -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Country Name</label>
            <input
              v-model="form.name"
              id="name"
              type="text"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3 border"
              :class="{ 'border-red-500': form.errors.name }"
            >
            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
          </div>

          <!-- Code Fields -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Country Code</label>
              <input
                v-model="form.code"
                id="code"
                type="text"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3 border uppercase"
                :class="{ 'border-red-500': form.errors.code }"
                @input="form.code = form.code.toUpperCase()"
                maxlength="2"
              >
              <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">{{ form.errors.code }}</p>
              <p class="mt-1 text-xs text-gray-500">2-letter country code (e.g. US, GB)</p>
            </div>

            <div>
              <label for="currency_code" class="block text-sm font-medium text-gray-700 mb-1">Currency Code (Country)</label>
              <input
                v-model="form.currency_code"
                id="currency_code"
                type="text"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3 border uppercase"
                :class="{ 'border-red-500': form.errors.currency_code }"
                @input="form.currency_code = form.currency_code.toUpperCase()"
                maxlength="3"
              >
              <p v-if="form.errors.currency_code" class="mt-1 text-sm text-red-600">{{ form.errors.currency_code }}</p>
              <p class="mt-1 text-xs text-gray-500">3-letter currency code (e.g. USD, EUR)</p>
            </div>
          </div>

          <div>
            <label for="wikiDataId" class="block text-sm font-medium text-gray-700 mb-1">WikiData ID</label>
            <input
              v-model="form.wikiDataId"
              id="wikiDataId"
              type="text"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3 border"
              :class="{ 'border-red-500': form.errors.wikiDataId }"
            >
            <p v-if="form.errors.wikiDataId" class="mt-1 text-sm text-red-600">{{ form.errors.wikiDataId }}</p>
            <p class="mt-1 text-xs text-gray-500">Optional Wikidata identifier (e.g. Q30 for United States)</p>
          </div>

          <!-- Currency Fields -->
          <div class="space-y-6 border-t pt-6">
            <h3 class="text-lg font-medium text-gray-900">Currency Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <label for="currency_code_field" class="block text-sm font-medium text-gray-700 mb-1">Currency Code</label>
                <input
                  v-model="form.currency.code"
                  id="currency_code_field"
                  type="text"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3 border uppercase"
                  :class="{ 'border-red-500': form.errors['currency.code'] }"
                  @input="form.currency.code = form.currency.code.toUpperCase()"
                  maxlength="3"
                >
                <p v-if="form.errors['currency.code']" class="mt-1 text-sm text-red-600">{{ form.errors['currency.code'] }}</p>
                <p class="mt-1 text-xs text-gray-500">3-letter currency code (e.g. USD, EUR)</p>
              </div>

              <div>
                <label for="currency_symbol" class="block text-sm font-medium text-gray-700 mb-1">Currency Symbol</label>
                <input
                  v-model="form.currency.symbol"
                  id="currency_symbol"
                  type="text"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 px-3 border"
                  :class="{ 'border-red-500': form.errors['currency.symbol'] }"
                  maxlength="10"
                >
                <p v-if="form.errors['currency.symbol']" class="mt-1 text-sm text-red-600">{{ form.errors['currency.symbol'] }}</p>
                <p class="mt-1 text-xs text-gray-500">e.g. $, €, £</p>
              </div>
            </div>
          </div>

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
              <span v-if="form.processing">Updating...</span>
              <span v-else>Update Country</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
