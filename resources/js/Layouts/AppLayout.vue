<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import SideNav from '@/Components/MyComponents/SideNav.vue';
import ResponsiveNavMobil from '@/Components/MyComponents/ResponsiveNavMobil.vue';
import NotificationsCenter from '@/Components/MyComponents/NotificationsCenter.vue';

defineProps({
    title: String,
});

const notifications = ref(null);

const updateNotifications = (value) => {
    notifications.value = value; // Guarda el número emitido en la variable
};
// const showingNavigationDropdown = ref(false);

// const switchToTeam = (team) => {
//     router.put(route('current-team.update'), {
//         team_id: team.id,
//     }, {
//         preserveState: false,
//     });
// };

// const logout = () => {
//     router.post(route('logout'));
// };
</script>

<template>
    <div>

        <Head :title="(notifications > 0 ? '(' + notifications + ') ' : '') + title" />

        <Banner />

        <div class="overflow-hidden h-screen md:flex bg-white selection:bg-[#D4F3C9]">

            <!-- sidenav -->
            <aside class="col-span-2 w-auto">
                <SideNav />
            </aside>

            <!-- resto de pagina -->
            <main class="w-full">
                <nav class="bg-white border-b border-grayD9">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-12">
                            <div class="flex space-x-3">
                                <!-- Logo -->
                                <div class="md:hidden shrink-0 flex items-center">
                                    <Link :href="route('dashboard')">
                                    <ApplicationMark class="block h-11 w-auto" />
                                    </Link>
                                </div>

                                <!-- notificaciones -->
                                <div class="hidden md:inline mr-3 mt-2 relative">
                                    <NotificationsCenter @notifications="updateNotifications" />
                                </div>
                                <!-- calendario -->
                                <div class="mr-3 mt-2 relative">
                                    <div class="group relative ml-3">
                                        <Link :href="route('calendars.index')">
                                        <button class="rounded-full size-9 flex items-center justify-center"
                                            :class="route().current('calendars.*') ? 'bg-[#D3F6E6] text-primary' : 'text-gray-600'">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor"
                                                class="size-5 md:size-6 lg:size-7">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                                            </svg>
                                        </button>
                                        </Link>
                                        <span class="absolute -bottom-10 left-[50%] -translate-x-[50%] 
                                            z-20 origin-left scale-0 px-3 rounded-lg border 
                                            border-gray-300 bg-white py-2 text-sm font-bold
                                            shadow-md transition-all duration-300 ease-in-out 
                                            group-hover:scale-100">Calendario<span>
                                            </span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="self-center bg-gray-100 px-2 py-1 rounded-full">
                                <a href="https://ezyventas.com" target="_blank" class="flex items-center">
                                    <img class="w-8 object-contain" src="@/../../public/images/isologoEzy.png">
                                    <span class="text-xs">Punto de venta para tu negocio!</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Responsive Navigation Menu -->
                    <!-- <div :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }"
                        class="sm:hidden">
                        <div class="pt-2 pb-3 space-y-px">
                            <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                <div class="flex items-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                                    </svg>
                                    <span>Panel de inicio</span>
                                </div>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('products.index')" :active="route().current('products.*')">
                                <div class="flex items-center space-x-2">
                                    <svg width="20" height="20" viewBox="0 0 17 17" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M2.25 8.38583C2.25 10.0434 2.90848 11.6331 4.08058 12.8053C5.25268 13.9774 6.8424 14.6358 8.5 14.6358C10.1576 14.6358 11.7473 13.9774 12.9194 12.8053C14.0915 11.6331 14.75 10.0434 14.75 8.38583M2.25 8.38583C2.25 6.72823 2.90848 5.13852 4.08058 3.96642C5.25268 2.79431 6.8424 2.13583 8.5 2.13583C10.1576 2.13583 11.7473 2.79431 12.9194 3.96642C14.0915 5.13852 14.75 6.72823 14.75 8.38583M2.25 8.38583H1M14.75 8.38583H16M14.75 8.38583H8.5L4.75 1.89M1.4525 10.95L2.6275 10.5225M14.3733 6.2475L15.5483 5.82M2.755 13.2067L3.71333 12.4033M13.2883 4.36833L14.2458 3.565M4.75083 14.8817L5.37583 13.7983L8.50167 8.38583M11.6258 2.97333L12.2508 1.89M7.19833 15.7717L7.415 14.5408M9.58583 2.23083L9.8025 1M9.8025 15.7717L9.58583 14.5408M7.415 2.23083L7.19833 1M12.25 14.8808L11.625 13.7983M14.245 13.2067L13.2875 12.4033M3.71333 4.3675L2.755 3.56417M15.5483 10.9508L14.3733 10.5233M2.62833 6.24833L1.45333 5.82"
                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    <span>Productos</span>
                                </div>
                            </ResponsiveNavLink>
                        </div>

                        Responsive Settings Options
                        <div class="pt-4 pb-1 border-t border-gray-200">
                            <div class="flex items-center px-4">
                                <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 me-3">
                                    <img class="h-10 w-10 rounded-full object-cover"
                                        :src="$page.props.auth.user.profile_photo_url"
                                        :alt="$page.props.auth.user.name">
                                </div>

                                <div>
                                    <div class="font-medium text-base text-white">
                                        {{ $page.props.auth.user.name }}
                                    </div>
                                    <div class="font-medium text-sm text-gray-100">
                                        {{ $page.props.auth.user.email }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 space-y-1">
                                <ResponsiveNavLink :href="route('profile.show')"
                                    :active="route().current('profile.show')">
                                    Perfil
                                </ResponsiveNavLink>

                                Authentication
                                <form method="POST" @submit.prevent="logout">
                                    <ResponsiveNavLink as="button">
                                        <i
                                            class="fa-solid fa-arrow-right-from-bracket text-xs text-red-600 rotate-180 mr-2"></i>
                                        <span class="text-red-600">Cerrar sesión</span>
                                    </ResponsiveNavLink>
                                </form>

                            </div>
                        </div>
                    </div> -->
                </nav>

                <div class="overflow-y-auto h-[calc(100vh-7.2rem)] md:h-[calc(100vh-3rem)] bg-white">
                    <slot />
                </div>

                <!-- ---------------- footer nave mobile view --------------- -->
                <nav class="md:hidden fixed bottom-0 w-full z-10">
                    <ResponsiveNavMobil />
                </nav>
            </main>
        </div>
    </div>
</template>
