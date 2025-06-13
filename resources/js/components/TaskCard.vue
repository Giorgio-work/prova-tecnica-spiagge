<template>
  <Card class="hover:shadow-md transition-shadow duration-200">
    <div class="p-6">
      <div class="flex items-start justify-between mb-4">
        <div class="flex-1">
          <h3 class="text-lg font-semibold text-primary-900 dark:text-primary-100 mb-2">
            {{ task.title }}
          </h3>
          <p v-if="task.description" class="text-gray-700 dark:text-gray-600 text-sm mb-3">
            {{ task.description }}
          </p>
        </div>
        <div class="flex items-center gap-2 ml-4">
          <Badge :variant="getPriorityVariant(task.priority)">
            {{ getPriorityLabel(task.priority) }}
          </Badge>
          <Badge :variant="getStatusVariant(task.status)">
            {{ getStatusLabel(task.status) }}
          </Badge>
        </div>
      </div>

      <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-500 mb-4">
        <div class="flex items-center gap-4">
          <div v-if="task.user" class="flex items-center gap-2">
            <User class="w-4 h-4" />
            <span>Creato da: {{ task.user.name }}</span>
          </div>
          <div v-if="task.assigned_user" class="flex items-center gap-2">
            <UserCheck class="w-4 h-4" />
            <span>Assegnato a: {{ task.assigned_user.name }}</span>
          </div>
        </div>
        <div v-if="task.due_date" class="flex items-center gap-2">
          <Calendar class="w-4 h-4" />
          <span :class="{ 'text-red-500': isOverdue }">
            {{ formatDate(task.due_date) }}
          </span>
        </div>
      </div>

      <div class="flex items-center justify-between">
        <div class="text-xs text-gray-500">
          Creato il {{ formatDateTime(task.created_at) }}
        </div>
        <div class="flex items-center gap-2">
          <Button
            variant="outline"
            size="sm"
            @click="$emit('edit', task)"
          >
            <Edit class="w-4 h-4" />
            Modifica
          </Button>
          <Button
            v-if="task.status !== 'completed'"
            variant="success"
            size="sm"
            @click="$emit('complete', task)"
          >
            <Check class="w-4 h-4" />
            Completa
          </Button>
          <Button
            variant="destructive"
            size="sm"
            @click="$emit('delete', task)"
          >
            <Trash2 class="w-4 h-4" />
            Elimina
          </Button>
        </div>
      </div>
    </div>
  </Card>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Card } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Calendar, Check, Edit, Trash2, User, UserCheck } from 'lucide-vue-next'
import type { TaskWithRelations, TaskCardProps } from '@/types/task'

defineEmits<{
  edit: [task: TaskWithRelations]
  complete: [task: TaskWithRelations]
  delete: [task: TaskWithRelations]
}>()

const props = defineProps<TaskCardProps>()

const isOverdue = computed(() => {
  if (!props.task.due_date) return false
  const dueDate = new Date(props.task.due_date)
  const now = new Date()
  return dueDate < now && !['completed', 'cancelled'].includes(props.task.status)
})

function getPriorityVariant(priority: string) {
  switch (priority) {
    case 'urgent':
      return 'destructive'
    case 'high':
      return 'default'
    case 'medium':
      return 'secondary'
    case 'low':
      return 'outline'
    default:
      return 'outline'
  }
}

function getPriorityLabel(priority: string) {
  switch (priority) {
    case 'urgent':
      return 'Urgente'
    case 'high':
      return 'Alta'
    case 'medium':
      return 'Media'
    case 'low':
      return 'Bassa'
    default:
      return priority
  }
}

function getStatusVariant(status: string) {
  switch (status) {
    case 'completed':
      return 'completed'
    case 'in_progress':
      return 'secondary'
    case 'pending':
      return 'pending'
    case 'cancelled':
      return 'destructive'
    default:
      return 'outline'
  }
}

function getStatusLabel(status: string) {
  switch (status) {
    case 'pending':
      return 'In Attesa'
    case 'in_progress':
      return 'In Corso'
    case 'completed':
      return 'Completato'
    case 'cancelled':
      return 'Annullato'
    default:
      return status
  }
}

function formatDate(date: string) {
  return new Date(date).toLocaleDateString('it-IT')
}

function formatDateTime(date: string) {
  return new Date(date).toLocaleDateString('it-IT', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>
