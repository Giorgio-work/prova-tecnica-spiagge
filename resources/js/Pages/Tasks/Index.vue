<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Gestione Task</h1>
                <Button @click="navigateToCreate">
                    <Plus class="mr-2 h-4 w-4" />
                    Nuovo Task
                </Button>
            </div>

            <div class="mb-6 rounded-lg bg-white p-4 shadow">
                <h2 class="mb-4 text-lg font-semibold">Filtri</h2>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Stato</label>
                        <select
                            v-model="filters.status"
                            class="border-input bg-background w-full rounded-md border px-3 py-2 text-sm"
                            @change="applyFilters"
                        >
                            <option value="">Tutti gli stati</option>
                            <option v-for="status in statuses" :key="status" :value="status">
                                {{ getStatusLabel(status) }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Priorità</label>
                        <select
                            v-model="filters.priority"
                            class="border-input bg-background w-full rounded-md border px-3 py-2 text-sm"
                            @change="applyFilters"
                        >
                            <option value="">Tutte le priorità</option>
                            <option v-for="priority in priorities" :key="priority" :value="priority">
                                {{ getPriorityLabel(priority) }}
                            </option>
                        </select>
                    </div>
                    <div class="flex items-center">
                        <input
                            id="overdue"
                            v-model="filters.overdue"
                            type="checkbox"
                            class="text-primary focus:ring-primary rounded border-gray-300"
                            @change="applyFilters"
                        />
                        <label for="overdue" class="ml-2 text-sm">In ritardo</label>
                    </div>
                </div>
            </div>

            <div v-if="tasks.data.length === 0" class="py-8 text-center">
                <div class="text-gray-500 dark:text-gray-400">
                    <ClipboardList class="mx-auto mb-4 h-12 w-12" />
                    <p class="text-lg font-medium">Nessun task trovato</p>
                    <p class="mt-1">Crea un nuovo task per iniziare</p>
                </div>
            </div>

            <div v-else class="grid grid-cols-1 gap-4">
                <TaskCard
                    v-for="task in tasks.data"
                    :key="task.id"
                    :task="task"
                    @edit="editTask"
                    @complete="e => changeStatus(e, 'completed')"
                    @delete="confirmDeleteTask"
                />
            </div>

            <div v-if="tasks.links && tasks.links.length > 3" class="mt-6">
                <Pagination :links="tasks.links" />
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Dialog :open="showDeleteModal" @update:open="showDeleteModal = $event">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Conferma eliminazione</DialogTitle>
                    <DialogDescription> Sei sicuro di voler eliminare questo task? Questa azione non può essere annullata. </DialogDescription>
                </DialogHeader>
                <div class="mt-4 flex justify-end space-x-2">
                    <Button variant="outline" @click="showDeleteModal = false">Annulla</Button>
                    <Button variant="destructive" @click="deleteTask">Elimina</Button>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-vue-next';
import TaskCard from '@/components/TaskCard.vue';
import Pagination from '@/components/Pagination.vue';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import type { TaskIndexProps, TaskWithRelations } from '@/types/task';

const props = defineProps<TaskIndexProps>();

const filters = ref({
    status: props.filters.status || null,
    priority: props.filters.priority || null,
    user_id: props.filters.user_id || null,
    overdue: props.filters.overdue || null,
    created_by_me: props.filters.created_by_me || null,
    order_by: props.filters.order_by || 'created_at',
    order_direction: props.filters.order_direction || 'desc',
});

const showDeleteModal = ref(false);
const taskToDelete = ref<TaskWithRelations | null>(null);

const breadcrumbs = computed(() => [
    { title: 'Dashboard', href: '/' },
    { title: 'Task', href: '/tasks' }
]);

function navigateToCreate() {
    router.visit('/tasks/create');
}

function editTask(task: TaskWithRelations) {
    router.visit(`/tasks/${task.id}/edit`);
}

function changeStatus(task: TaskWithRelations, status: string) {
    router.post(`/tasks/${task.id}/changeStatus`, { status });
}

function confirmDeleteTask(task: TaskWithRelations) {
    taskToDelete.value = task;
    showDeleteModal.value = true;
}

function deleteTask() {
    if (taskToDelete.value) {
        router.delete(`/tasks/${taskToDelete.value.id}`, {
            onSuccess: () => {
                showDeleteModal.value = false;
                taskToDelete.value = null;
            },
        });
    }
}

function applyFilters() {
    const tempFilters = Object.fromEntries(Object.entries(filters.value).filter(([, value]) => value != null));

    router.get(
        '/tasks',
        {
            ...tempFilters,
            page: 1, // Reset to first page when filters change
        },
        {
            preserveState: true,
            preserveScroll: false,
            only: ['tasks'],
        },
    );
}

function getStatusLabel(status: string) {
    switch (status) {
        case 'pending':
            return 'In Attesa';
        case 'in_progress':
            return 'In Corso';
        case 'completed':
            return 'Completato';
        case 'cancelled':
            return 'Annullato';
        default:
            return status;
    }
}

function getPriorityLabel(priority: string) {
    switch (priority) {
        case 'urgent':
            return 'Urgente';
        case 'high':
            return 'Alta';
        case 'medium':
            return 'Media';
        case 'low':
            return 'Bassa';
        default:
            return priority;
    }
}
</script>
