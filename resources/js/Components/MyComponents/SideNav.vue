<template>
    <!-- sidebar -->
    <div class="h-screen hidden md:block shadow-lg relative">
        <div class="bg-[#17141D] h-full overflow-auto">
            <!-- Logo -->
            <div class="flex items-center justify-center mt-7">
                <Link v-if="small" :href="route('dashboard')">
                <figure>
                    <img class="w-12 px-2 mb-[52px]" src="@/../../public/images/isologo.png" alt="logo">
                </figure>
                </Link>
                <Link v-else :href="route('dashboard')">
                <figure>
                    <img class="w-32 px-2 mb-[49px]" src="@/../../public/images/white_logo.png" alt="logo">
                </figure>
                </Link>
            </div>
            <nav class="pr-2 text-white">
                <!-- Con barra pequeña -->
                <section v-if="small">
                    <div v-for="(menu, index) in menus" :key="index">
                        <button v-if="menu.show" @click="handleClickInMenu(index)" :active="menu.active" :title="menu.label"
                            class="w-full pl-3 rounded-ld my-2 size-10 transition ease-linear duration-200">
                            <p :class="menu.active ? 'bg-[#272829] text-white' : 'hover:text-white hover:bg-[#272829] text-[#999999]'"
                                class="rounded-lg size-10 flex items-center justify-center" v-html="menu.icon"></p>
                        </button>
                    </div>
                </section>

                <!-- Con barra grande -->
                <section v-else v-for="(menu, index) in menus" :key="index">
                    <!-- Con submenues -->
                    <div v-if="menu.show">
                        <Accordion v-if="menu.options.length" :icon="menu.icon" :active="menu.active"
                            :title="menu.label" :id="index">
                            <div v-for="(option, index2) in menu.options" :key="index2">
                                <button @click="handleClickInMenu(index)" v-if="option.show" :active="option.active"
                                    :title="option.label"
                                    class="w-full pl-3 rounded-full mt-2 size-10 transition ease-linear duration-200"
                                    :class="option.active ? 'bg-[#393939] text-white' : 'hover:text-white hover:bg-gradient-to-r from-gray-800 to-black1 text-gray-700'">
                                    <p class="w-full truncate"> {{ option.label }}</p>
                                </button>
                            </div>
                        </Accordion>
                        <!-- Sin submenues -->
                        <button v-else-if="menu.show" @click="handleClickInMenu(index)" :active="menu.active"
                            :title="menu.label"
                            class="w-full pl-3 rounded-full mt-2 size-10 transition ease-linear duration-200">
                            <div class="flex items-center rounded-lg"
                                :class="menu.active ? 'bg-[#272829] text-white' : 'hover:text-white hover:bg-[#272829] text-[#999999]'">
                                <p class="rounded-lg size-10 flex items-center justify-center" v-html="menu.icon">
                                </p>
                                <span class="font-bold text-sm mr-2">{{ menu.label }}</span>
                            </div>
                        </button>
                    </div>
                </section>
            </nav>
        </div>

        <!-- Abrir y cerrar sidenav -->
        <button class="pl-3 rounded-ld my-2 size-10 transition ease-linear duration-200 absolute bottom-4 -left-1">
            <svg @click="small = false" v-if="small" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                class="size-10 px-2 hover:bg-[#272829] hover:text-white text-[#999999] rounded-lg flex items-center justify-center">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
            </svg>
            <svg @click="small = true" v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                class="size-10 px-2 hover:bg-[#272829] hover:text-white text-[#999999] rounded-lg flex items-center justify-center">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18" />
            </svg>

        </button>
    </div>

    <ConfirmationModal :show="showGoToRouteConfirmation" @close="showGoToRouteConfirmation = false">
        <template #title>
            <h1>Proceso pendiente</h1>
        </template>
        <template #content>
            <p>
                Tienes un proceso sin completar en esta vista. Si cambias de vista, se borrarán los cambios o
                procesos que no has finalizado. ¿Continuar de todas formas?
            </p>
        </template>
        <template #footer>
            <div class="flex items-center space-x-1">
                <CancelButton @click="showGoToRouteConfirmation = false">Cancelar</CancelButton>
                <DangerButton @click="goToRoute()">Continuar</DangerButton>
            </div>
        </template>
    </ConfirmationModal>
</template>

<script>
import Accordion from './Accordion.vue';
import { Link } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from "@/Components/DangerButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";

export default {
    data() {
        return {
            small: true,
            collapsedMenu: null,
            routeToGo: null,
            showGoToRouteConfirmation: false,
            menus: [
                {
                    label: 'Inicio',
                    icon: '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M1 11.125C1 10.504 1.504 10 2.125 10H4.375C4.996 10 5.5 10.504 5.5 11.125V17.875C5.5 18.496 4.996 19 4.375 19H2.125C1.82663 19 1.54048 18.8815 1.3295 18.6705C1.11853 18.4595 1 18.1734 1 17.875V11.125ZM7.75 6.625C7.75 6.004 8.254 5.5 8.875 5.5H11.125C11.746 5.5 12.25 6.004 12.25 6.625V17.875C12.25 18.496 11.746 19 11.125 19H8.875C8.57663 19 8.29048 18.8815 8.0795 18.6705C7.86853 18.4595 7.75 18.1734 7.75 17.875V6.625ZM14.5 2.125C14.5 1.504 15.004 1 15.625 1H17.875C18.496 1 19 1.504 19 2.125V17.875C19 18.496 18.496 19 17.875 19H15.625C15.3266 19 15.0405 18.8815 14.8295 18.6705C14.6185 18.4595 14.5 18.1734 14.5 17.875V2.125Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                    route: route('dashboard'),
                    active: route().current('dashboard'),
                    options: [],
                    dropdown: false,
                    show: true
                },
                {
                    label: 'Transacciones',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>',
                    route: route('login'),
                    active: route().current('login'),
                    options: [],
                    dropdown: false,
                    show: true
                },
                {
                    label: 'Categorías',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" /></svg>',
                    route: route('login'),
                    active: route().current('login'),
                    options: [],
                    dropdown: false,
                    show: true
                },
                {
                    label: 'Deudas y pagos pendientes',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" /></svg>',
                    route: route('login'),
                    active: route().current('login'),
                    options: [],
                    dropdown: false,
                    show: true
                },
                {
                    label: 'Configuraciones',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>',
                    route: route('login'),
                    active: route().current('login'),
                    options: [],
                    dropdown: false,
                    show: true
                },


                //ejemplo para usar submenues
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
        ApplicationMark,
        ConfirmationModal,
        DropdownLink,
        DangerButton,
        CancelButton,
        Accordion,
        Dropdown,
        Link,
    },
    methods: {
        handleClickInMenu(index) {
            if (this.menus[index].options.length) {
                if (this.collapsedMenu === index) {
                    this.collapsedMenu = null;
                } else {
                    this.collapsedMenu = index;
                }
            } else {
                // revisar si hay proceso pendiente para no cambiar de vista sin preguntar
                const pendentProcess = JSON.parse(localStorage.getItem('pendentProcess'));
                if (pendentProcess) {
                    this.routeToGo = this.menus[index].route;
                    this.showGoToRouteConfirmation = true;
                } else {
                    this.goToRoute(this.menus[index].route)
                }
            }
        },
        goToRoute(route = null) {
            // resetear variable de local storage a false
            localStorage.setItem('pendentProcess', false);

            // ir a la ruta solicitada
            if (route) {
                this.$inertia.get(route);
            } else {
                this.$inertia.get(this.routeToGo);
            }
        },
        logout() {
            this.$inertia.post(route('logout'));
        }
    },
    mounted() {
    }
}
</script>