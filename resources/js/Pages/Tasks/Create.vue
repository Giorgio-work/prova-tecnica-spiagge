<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="container mx-auto p-4">
      <div class="flex items-center mb-6">
        <Button variant="outline" @click="goBack" class="mr-4">
          <ArrowLeft class="w-4 h-4 mr-2" />
          Indietro
        </Button>
        <h1 class="text-2xl font-bold">Crea Nuovo Task</h1>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <form @submit.prevent="submitForm" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
              <label for="title" class="block text-sm font-medium mb-2">
                Titolo <span class="text-red-500">*</span>
              </label>
              <Input
                id="title"
                v-model="form.title"
                type="text"
                placeholder="Inserisci il titolo del task"
                :class="{ 'border-red-500': form.errors.title }"
              />
              <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">
                {{ form.errors.title }}
              </div>
            </div>

            <div class="md:col-span-2">
              <label for="description" class="block text-sm font-medium mb-2">
                Descrizione
              </label>
              <textarea
                id="description"
                v-model="form.description"
                rows="4"
                placeholder="Inserisci una descrizione dettagliata del task"
                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent"
                :class="{ 'border-red-500': form.errors.description }"
              ></textarea>
              <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">
                {{ form.errors.description }}
              </div>
            </div>

            <div>
              <label for="priority" class="block text-sm font-medium mb-2">
                Priorità <span class="text-red-500">*</span>
              </label>
              <select
                id="priority"
                v-model="form.priority"
                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                :class="{ 'border-red-500': form.errors.priority }"
              >
                <option value="">Seleziona priorità</option>
                <option value="low">Bassa</option>
                <option value="medium">Media</option>
                <option value="high">Alta</option>
                <option value="urgent">Urgente</option>
              </select>
              <div v-if="form.errors.priority" class="text-red-500 text-sm mt-1">
                {{ form.errors.priority }}
              </div>
            </div>

            <div>
              <label for="status" class="block text-sm font-medium mb-2">
                Stato <span class="text-red-500">*</span>
              </label>
              <select
                id="status"
                v-model="form.status"
                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                :class="{ 'border-red-500': form.errors.status }"
              >
                <option value="">Seleziona stato</option>
                <option value="pending">In Attesa</option>
                <option value="in_progress">In Corso</option>
                <option value="completed">Completato</option>
                <option value="cancelled">Annullato</option>
              </select>
              <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">
                {{ form.errors.status }}
              </div>
            </div>



            <div>
              <label for="due_date" class="block text-sm font-medium mb-2">
                Data di scadenza
              </label>
              <Input
                id="due_date"
                v-model="form.due_date"
                type="datetime-local"
                :class="{ 'border-red-500': form.errors.due_date }"
              />
              <div v-if="form.errors.due_date" class="text-red-500 text-sm mt-1">
                {{ form.errors.due_date }}
              </div>
            </div>
          </div>

          <div class="flex justify-end space-x-4 pt-6 border-t">
            <Button type="button" variant="outline" @click="goBack">
              Annulla
            </Button>
            <Button type="submit" :disabled="form.processing">
              <Loader2 v-if="form.processing" class="w-4 h-4 mr-2 animate-spin" />
              {{ form.processing ? 'Creazione...' : 'Crea Task' }}
            </Button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { ArrowLeft, Loader2 } from 'lucide-vue-next'
import type { CreateTaskProps } from '@/types/task'

const props = defineProps<CreateTaskProps>()

const form = useForm({
  title: '',
  description: '',
  priority: '',
  status: 'pending',
  due_date: '',
})

const breadcrumbs = computed(() => [
  { title: 'Dashboard', href: '/' },
  { title: 'Task', href: '/tasks' },
  { title: 'Crea Nuovo', href: '/tasks/create' },
])

function submitForm() {
  form.post('/tasks', {
    onSuccess: () => {
      router.visit('/tasks')
    },
  })
}

function goBack() {
  router.visit('/tasks')
}
</script>
