<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import Popup from '@/components/Popup.vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}


withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});


</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
        <Popup />
    </AppLayout>
</template>
