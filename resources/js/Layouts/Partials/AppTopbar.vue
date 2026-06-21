<script setup>
import { Link } from '@inertiajs/vue3';
import { Menu, Moon, Sunny } from '@element-plus/icons-vue';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import NotificationsCenter from '@/Components/MyComponents/NotificationsCenter.vue';
import { useDarkMode } from '@/composables/useDarkMode';

defineProps({
    notifications: { type: Number, default: 0 },
});

const emit = defineEmits(['update:notifications', 'openMobileMenu']);
const { isDark, toggleDark } = useDarkMode();

function onNotificationsUpdate(value) {
    emit('update:notifications', value);
}
</script>

<template>
    <header class="h-12 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 flex items-center shrink-0 transition-colors duration-200">
        <div class="w-full max-w-full mx-auto px-4 sm:px-6 flex items-center justify-between">
            <!-- Left -->
            <div class="flex items-center space-x-2">
                <!-- Hamburger -->
                <button
                    class="md:hidden size-9 flex items-center justify-center rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                    @click="emit('openMobileMenu')"
                >
                    <el-icon :size="20"><Menu /></el-icon>
                </button>

                <!-- Mobile logo -->
                <Link :href="route('dashboard')" class="md:hidden shrink-0 flex items-center">
                    <ApplicationMark class="block h-9 w-auto" />
                </Link>

                <!-- Notifications -->
                <div class="hidden md:flex items-center">
                    <NotificationsCenter @notifications="onNotificationsUpdate" />
                </div>

                <!-- Calendar -->
                <Link :href="route('calendars.index')" class="flex items-center group relative">
                    <button
                        class="rounded-full size-9 flex items-center justify-center transition-colors duration-200"
                        :class="route().current('calendars.*')
                            ? 'bg-[#EAF7EF] dark:bg-[#1a3a3b] text-[#2F9E5B] dark:text-[#3BB970]'
                            : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                        </svg>
                    </button>
                    <span
                        class="absolute -bottom-9 left-1/2 -translate-x-1/2 z-50 origin-bottom scale-0
                        px-3 py-1.5 rounded-md border border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-600 text-xs font-medium
                        text-gray-700 dark:text-gray-200 shadow-sm transition-all duration-200 ease-in-out
                        group-hover:scale-100 whitespace-nowrap pointer-events-none"
                    >Calendario</span>
                </Link>
            </div>

            <!-- Right -->
            <div class="flex items-center gap-2">
                <!-- Dark mode toggle -->
                <button
                    @click="toggleDark"
                    class="size-9 flex items-center justify-center rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                    :title="isDark ? 'Modo claro' : 'Modo oscuro'"
                >
                    <el-icon :size="18">
                        <Sunny v-if="isDark" />
                        <Moon v-else />
                    </el-icon>
                </button>

                <!-- Ezyventas promo -->
                <a href="https://ezyventas.com" target="_blank"
                    class="hidden sm:flex items-center space-x-2 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700
                    px-3 py-1.5 rounded-full transition-colors duration-200"
                >
                    <img class="w-6 object-contain opacity-70" src="@/../../public/images/isologoEzy.png" alt="EzyVentas">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Punto de venta para tu negocio</span>
                </a>
            </div>
        </div>
    </header>
</template>
