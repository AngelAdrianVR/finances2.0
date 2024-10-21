<template>
    <!-- sidebar -->
    <div class="h-screen hidden md:block shadow-lg relative">
        <div class="bg-[#17141D] h-full overflow-auto">
            <!-- Logo -->
            <div class="flex items-center justify-center mt-7">
                <Link class="h-24" v-if="small" :href="route('dashboard')">
                    <figure>
                        <img class="px-2 mb-[52px]" src="@/../../public/images/isologo.png" alt="logo">
                    </figure>
                </Link>
                <Link v-else class="h-24" :href="route('dashboard')">
                    <figure>
                        <img class="px-2 mb-[49px]" src="@/../../public/images/white_logo.png" alt="logo">
                    </figure>
                </Link>
            </div>

            <!-- Avatar de usuario -->
            <div class="flex items-center justify-center text-center w-full px-2 mb-7">
                <button v-if="$page.props.jetstream.managesProfilePhotos"
                    @click="showProfileCard = !showProfileCard"
                    class="flex items-center space-x-2 text-sm border rounded-full focus:outline-none transition"
                    :class="{ 'border-primary': showProfileCard, 'border-[#797878]': !showProfileCard, 'size-12 justify-center': small, 'h-12 w-full px-2 justify-between': !small }">
                    <div class="flex items-center">
                        <img class="size-9 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url"
                            :alt="$page.props.auth.user.name">
                        <p v-if="!small" class="text-[11px] text-gray99 leading-snug text-start mt-1 ml-2">
                            {{ $page.props.auth.user.name }}</p>
                    </div>
                    <i v-if="!small" class="fa-solid fa-angle-right text-center text-xs text-[#999999]"></i>
                </button>
                <ProfileCard v-if="showProfileCard" @close="showProfileCard = false" class="top-0 left-[calc(100%+0.3rem)]" />
            </div>

            <nav class="pr-2 text-white">
                <!-- Con barra pequeña -->
                <section v-if="small">
                    <div v-for="(menu, index) in menus" :key="index">
                        <SideNavLink v-if="menu.show" :href="menu.route" :active="menu.active" :dropdown="menu.dropdown"
                            class="mb-px">
                            <template #trigger>
                                <button v-if="menu.show" :active="menu.active" :title="menu.label"
                                    class="w-full pl-2 rounded-lg my-[5px] size-10 transition ease-linear duration-200">
                                    <p :class="menu.active ? 'bg-[#272829] text-white' : 'hover:text-white hover:bg-[#272829] text-[#999999]'"
                                        class="rounded-lg size-10 flex items-center justify-center" v-html="menu.icon"></p>
                                </button>
                                <i v-if="menu.notifications" class="fa-solid fa-circle fa-flip text-primary text-[10px] absolute bottom-7 right-1"></i>
                            </template>
                            <template #content>
                                <template v-for="option in menu.options" :key="option">
                                    <DropdownNavLink v-if="option.show" :href="option.route" :notifications="option.notifications" :active="option.active">
                                        {{ option.label }}
                                    </DropdownNavLink>
                                </template>
                            </template>
                        </SideNavLink>
                    </div>
                </section>

                <!-- Con barra grande -->
                <section v-else v-for="(menu, index) in menus" :key="index">
                    <!-- Con submenues -->
                    <div v-if="menu.show">
                        <Accordion v-if="menu.options.length" :icon="menu.icon" :active="menu.active" :title="menu.label" :id="index">
                            <!-- Opciones del menu -->
                            <template #content>
                                <div class="relative" v-for="(option, index2) in menu.options" :key="index2">
                                    <Link :href="option.route" class="flex items-center">
                                        <button v-if="option.show" :active="option.active" :title="option.label"
                                            class="w-full pl-2 ml-8 rounded-lg size-7 mb-1 transition ease-linear duration-200 text-left text-xs"
                                            :class="option.active ? 'bg-[#272829] text-white' : 'hover:text-white hover:bg-[#272829] text-[#999999]'">
                                            <p class="truncate">{{ option.label }}</p>
                                        </button>
                                    
                                        <!-- Adorno lateral de subcategorias-->
                                        <i v-if="option.active" class="absolute left-[13px] fa-solid fa-circle text-[7px] z-10 bg-[#17141D] p-1"></i>
                                        <div class="border-l border-[#999999] absolute left-5 h-full"></div>
                                    </Link>

                                </div>
                            </template>
                        </Accordion>
                        <!-- Sin submenues -->
                        <button v-else-if="menu.show" :active="menu.active"
                            :title="menu.label"
                            class="w-full pl-2 rounded-full my-1 size-10 transition ease-linear duration-200 relative">
                            <Link :href="menu.route" class="flex items-center rounded-lg"
                                :class="menu.active ? 'bg-[#272829] text-white' : 'hover:text-white hover:bg-[#272829] text-[#999999]'">
                                <p class="rounded-lg size-10 flex items-center justify-center" v-html="menu.icon"></p>
                                <span class="font-bold text-sm mr-2">{{ menu.label }}</span>
                            </Link>
                            <i v-if="menu.notifications" class="fa-solid fa-circle fa-flip text-primary text-[10px] absolute bottom-7 right-1"></i>
                        </button>
                    </div>
                </section>
            </nav>
        </div>

        <!-- Abrir y cerrar sidenav -->
        <button class="pl-2 rounded-ld my-1 size-10 transition ease-linear duration-200 absolute bottom-4 -left-1">
            <svg @click="updateSideNavSize(false)" v-if="small" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                class="size-10 px-2 hover:bg-[#272829] hover:text-white text-[#999999] rounded-lg flex items-center justify-center">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
            </svg>
            <svg @click="updateSideNavSize(true)" v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                class="size-10 px-2 hover:bg-[#272829] hover:text-white text-[#999999] rounded-lg flex items-center justify-center">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18" />
            </svg>

        </button>
    </div>
</template>

<script>
import Accordion from '@/Components/MyComponents/Accordion.vue';
import ProfileCard from './ProfileCard.vue';
import SideNavLink from "@/Components/MyComponents/SideNavLink.vue";
import DropdownNavLink from "@/Components/MyComponents/DropdownNavLink.vue";
import { Link } from '@inertiajs/vue3';

export default {
    data() {
        return {
            small: true,
            showProfileCard: false,
            collapsedMenu: null,
            routeToGo: null,
            menus: [
                {
                    label: 'Inicio',
                    icon: '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M1 11.125C1 10.504 1.504 10 2.125 10H4.375C4.996 10 5.5 10.504 5.5 11.125V17.875C5.5 18.496 4.996 19 4.375 19H2.125C1.82663 19 1.54048 18.8815 1.3295 18.6705C1.11853 18.4595 1 18.1734 1 17.875V11.125ZM7.75 6.625C7.75 6.004 8.254 5.5 8.875 5.5H11.125C11.746 5.5 12.25 6.004 12.25 6.625V17.875C12.25 18.496 11.746 19 11.125 19H8.875C8.57663 19 8.29048 18.8815 8.0795 18.6705C7.86853 18.4595 7.75 18.1734 7.75 17.875V6.625ZM14.5 2.125C14.5 1.504 15.004 1 15.625 1H17.875C18.496 1 19 1.504 19 2.125V17.875C19 18.496 18.496 19 17.875 19H15.625C15.3266 19 15.0405 18.8815 14.8295 18.6705C14.6185 18.4595 14.5 18.1734 14.5 17.875V2.125Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                    route: route('dashboard'),
                    active: route().current('dashboard'),
                    options: [],
                    dropdown: false,
                    notifications: false,
                    show: true
                },
                {
                    label: 'Transacciones',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>',
                    // route: route('profile.show'),
                    active: route().current('incomes.*') || route().current('outcomes.*'),
                    options: [
                        {
                            label: 'Ingresos',
                            route: route('incomes.index'),
                            show: true,
                            active: route().current('incomes.*'),
                            notifications: false,
                        },
                        {
                            label: 'Gastos',
                            route: route('outcomes.index'),
                            show: true,
                            active: route().current('outcomes.*'),
                            notifications: false,
                        },
                    ],
                    dropdown: true,
                    show: true
                },
                {
                    label: 'Préstamos',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" /></svg>',
                    route: route('login'),
                    active: route().current('login'),
                    options: [],
                    dropdown: false,
                    notifications: false,
                    show: true
                },
                {
                    label: 'Inversiones',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>',
                    route: route('login'),
                    active: route().current('login'),
                    options: [],
                    dropdown: false,
                    notifications: false,
                    show: true
                },
                {
                    label: 'Configuraciones',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>',
                    route: route('login'),
                    active: route().current('login'),
                    options: [],
                    dropdown: false,
                    notifications: false,
                    show: true
                },


                //ejemplo para usar submenues
                // {
                //     label: 'Comunidad',
                //     icon: '<i class="fa-solid fa-people-roof text-sm mr-2"></i>',
                //     // route: route('posts.index'),
                //     active: route().current('posts.*') || route().current('community-events.*')|| route().current('neighbors.*'),
                //     options: [
                //         {
                //             label: 'Muro de noticias',
                //             route: route('posts.index'),
                //             show: true,
                //         },
                //         {
                //             label: 'Eventos',
                //             route: route('community-events.index'),
                //             show: true,
                //         },
                //         {
                //             label: 'Directorio de vecinos',
                //             route: route('neighbors.index'),
                //             show: true,
                //         },
                //     ],
                //     dropdown: true,
                //     show: true
                // },
            ],
        }
    },
    components: {
        DropdownNavLink,
        SideNavLink,
        ProfileCard,
        Accordion,
        Link,
    },
    methods: {
        updateSideNavSize(is_small){
            this.small = is_small;
            localStorage.setItem('is_sidenav_small', is_small);
        }
    },
    mounted() {
        const is_small = localStorage.getItem('is_sidenav_small');
        if (is_small !== null) {
            this.small = JSON.parse(is_small); // Convertirlo a booleano si es necesario
        }
    }
}
</script>