<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import Banner from '@/Components/Banner.vue';
import AppTopbar from '@/Layouts/Partials/AppTopbar.vue';
import AppMenu from '@/Layouts/Partials/AppMenu.vue';

defineProps({
    title: String,
});

const notifications = ref(0);
const mobileMenuOpen = ref(false);

function onNotificationsUpdate(value) {
    notifications.value = value;
}
</script>

<template>
    <div>
        <Head :title="(notifications > 0 ? '(' + notifications + ') ' : '') + title" />

        <Banner />

        <!-- Shell -->
        <div class="flex h-screen bg-gray-50 dark:bg-[#151A1A]">

            <!-- Desktop sidebar + Mobile drawer (unified AppMenu) -->
            <AppMenu v-model:mobile-open="mobileMenuOpen" />

            <!-- Main content area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                <!-- Topbar -->
                <AppTopbar
                    :notifications="notifications"
                    @update:notifications="onNotificationsUpdate"
                    @open-mobile-menu="mobileMenuOpen = true"
                />

                <!-- Page content -->
                <main class="flex-1 overflow-y-auto">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
