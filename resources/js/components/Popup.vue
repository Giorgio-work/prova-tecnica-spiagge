<script setup lang="ts">

import { AlertCircle, AlertTriangle, CheckCircle, Info, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

interface Notify {
    title: string|null
    icon: string|null
}

const $page = usePage();

const popups = computed(() => $page.props.popups as Notify);

const showNotification = ref(popups.value != null && popups.value.title != null);
const notificationTimeout = ref<number | null>(null);
const getIconComponent = (iconName: string | null) => {
    switch (iconName) {
        case 'CheckCircle':
            return CheckCircle;
        case 'AlertCircle':
            return AlertCircle;
        case 'Info':
            return Info;
        case 'AlertTriangle':
            return AlertTriangle;
        default:
            return Info;
    }
};

const closeNotification = () => {
    showNotification.value = false;
    if (notificationTimeout.value) {
        clearTimeout(notificationTimeout.value);
        notificationTimeout.value = null;
    }
};

watch(
    () => $page.props.popups,
    (newPopups) => {
        if (newPopups && newPopups.title) {
            showNotification.value = true;

            // Clear existing timeout
            if (notificationTimeout.value) {
                clearTimeout(notificationTimeout.value);
            }

            // Set new timeout to auto-close after 30 seconds
            notificationTimeout.value = setTimeout(() => {
                showNotification.value = false;
                notificationTimeout.value = null;
            }, 30000);
        }
    },
    { immediate: true },
);
</script>

<template>
    <!-- Notification Popup -->
    <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0 translate-y-2 scale-95"
        enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0 scale-100"
        leave-to-class="opacity-0 translate-y-2 scale-95"
    >
        <div
            v-if="showNotification && popups?.title"
            class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg p-4"
        >
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <component
                        :is="getIconComponent(popups.icon)"
                        class="w-5 h-5 text-blue-500"
                    />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ popups.title }}
                    </p>
                </div>
                <button
                    @click="closeNotification"
                    class="flex-shrink-0 ml-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                >
                    <X class="w-4 h-4" />
                </button>
            </div>
        </div>
    </Transition>
</template>

<style scoped>

</style>
