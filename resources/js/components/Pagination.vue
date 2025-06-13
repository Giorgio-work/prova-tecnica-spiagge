<template>
  <div class="flex items-center justify-between border-t border-gray-200 bg-white dark:border-gray-700 px-4 py-3 sm:px-6">
    <div class="flex flex-1 justify-between sm:hidden">
      <Link
        v-if="hasPrevPage"
        :href="prevPageUrl"
        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600"
      >
        Precedente
      </Link>
      <span v-else class="relative inline-flex items-center rounded-md border border-gray-300 bg-gray-100 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400">
        Precedente
      </span>

      <Link
        v-if="hasNextPage"
        :href="nextPageUrl"
        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600"
      >
        Successivo
      </Link>
      <span v-else class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-gray-100 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400">
        Successivo
      </span>
    </div>

    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700 dark:text-gray-300">
          Visualizzazione
          <span class="font-medium">{{ from }}</span>
          a
          <span class="font-medium">{{ to }}</span>
          di
          <span class="font-medium">{{ total }}</span>
          risultati
        </p>
      </div>
      <div>
        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
          <Link
            v-if="hasPrevPage"
            :href="prevPageUrl"
            class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0"
          >
            <span class="sr-only">Precedente</span>
            <ChevronLeft class="h-5 w-5" aria-hidden="true" />
          </Link>
          <span
            v-else
            class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-300 dark:text-gray-600 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:outline-offset-0"
          >
            <span class="sr-only">Precedente</span>
            <ChevronLeft class="h-5 w-5" aria-hidden="true" />
          </span>

          <template v-for="(link, i) in links" :key="i">
            <Link
              v-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
              :href="link.url"
              :class="[
                link.active
                  ? 'relative z-10 inline-flex items-center bg-primary text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary'
                  : 'relative inline-flex items-center text-gray-900 dark:text-gray-200 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0',
                'px-4 py-2 text-sm font-semibold',
              ]"
            >
              {{ link.label }}
            </Link>
          </template>

          <Link
            v-if="hasNextPage"
            :href="nextPageUrl"
            class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0"
          >
            <span class="sr-only">Successivo</span>
            <ChevronRight class="h-5 w-5" aria-hidden="true" />
          </Link>
          <span
            v-else
            class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-300 dark:text-gray-600 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:outline-offset-0"
          >
            <span class="sr-only">Successivo</span>
            <ChevronRight class="h-5 w-5" aria-hidden="true" />
          </span>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
import type { PaginationProps } from '@/types/task'

const props = defineProps<PaginationProps>()

const hasPrevPage = computed(() => {
  return props.links.some(link => link.label.includes('Previous') && link.url !== null)
})

const hasNextPage = computed(() => {
  return props.links.some(link => link.label.includes('Next') && link.url !== null)
})

const prevPageUrl = computed(() => {
  const prevLink = props.links.find(link => link.label.includes('Previous'))
  return prevLink?.url || '#'
})

const nextPageUrl = computed(() => {
  const nextLink = props.links.find(link => link.label.includes('Next'))
  return nextLink?.url || '#'
})

const from = computed(() => {
  // Extract from value from the first active link or default to 1
  const currentPage = parseInt(props.links.find(link => link.active)?.label || '1')
  const perPage = 15 // Default per page value, adjust if needed
  return (currentPage - 1) * perPage + 1
})

const to = computed(() => {
  // This is an approximation, ideally should come from the pagination data
  return Math.min(from.value + 14, total.value)
})

const total = computed(() => {
  // This should ideally come from the pagination data
  // For now, we'll estimate based on the number of pages
  const lastLink = props.links[props.links.length - 2] // Last page link (before Next)
  const lastPage = parseInt(lastLink?.label || '1')
  const perPage = 15 // Default per page value, adjust if needed
  return lastPage * perPage
})
</script>
