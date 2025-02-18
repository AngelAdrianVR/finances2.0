<template>
    <Dropdown @click="readNotifications()" align="left" width="notifications" :closeInClick="false">
        <template #trigger>
            <div class="mr-3 relative">
                <div class="group relative ml-3">
                    <button class="rounded-full size-9 flex items-center justify-center text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5 md:size-6 lg:size-7">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" />
                        </svg>
                    </button>
                    <strong
                        class="absolute -bottom-10 left-[50%] -translate-x-[50%] z-20 origin-left scale-0 px-3 rounded-lg border border-gray-300 bg-white py-2 text-sm font-boldshadow-md transition-all duration-300 ease-in-out group-hover:scale-100">
                        notificaciones
                    </strong>
                </div>
            </div>
            <span v-if="getUnreadMessages.length"
                class="size-4 bg-primary text-white text-[9px] rounded-full absolute top-0 -right-0 flex items-center justify-center border border-white">
                {{ getUnreadMessages.length }}
            </span>
        </template>
        <template #content>
            <div class="px-4 py-2 h-[300px] overflow-y-auto">
                <h1 class="text-sm px-1">
                    Notificaciones
                </h1>
                <!-- HOY -->
                <section class="mt-3">
                    <h2 class="text-xs px-1 flex items-center space-x-1">
                        <span class="text-[#575757]">Hoy</span>
                        <span class="border-grayD9 bg-grayF2 rounded-[5px] px-[5px] py-[2px]">
                            {{ todayNotifications.length }}</span>
                    </h2>
                    <!-- notificacion de hoy -->
                    <article class="space-y-2 mt-3">
                        <!-- notificaciones cuya propiedad created_at es de hoy -->
                        <NotificationCard v-for="(item, index) in todayNotifications" :key="index" @notification-deleted="fetchNotifications"
                            :notification="item" />
                        <p v-if="!todayNotifications.length" class="text-[10px] text-center">No hay notificaciones para
                            mostrar</p>
                    </article>
                </section>
                <!-- Ayer -->
                <section class="mt-3">
                    <h2 class="text-xs px-1 flex items-center space-x-1">
                        <span class="text-[#575757]">Ayer</span>
                        <span class="border-grayD9 bg-grayF2 rounded-[5px] px-[5px] py-[2px]">
                            {{ yesterdayNotifications.length }}</span>
                    </h2>
                    <!-- notificacion -->
                    <article class="space-y-2 mt-3">
                        <NotificationCard v-for="(item, index) in yesterdayNotifications" :key="index" @notification-deleted="fetchNotifications"
                            :notification="item" />
                        <p v-if="!yesterdayNotifications.length" class="text-[10px] text-center">No hay notificaciones
                            para mostrar</p>
                    </article>
                </section>
                <!-- Este mes -->
                <section class="mt-3">
                    <h2 class="text-xs px-1 flex items-center space-x-1">
                        <span class="text-[#575757]">Este mes</span>
                        <span class="border-grayD9 bg-grayF2 rounded-[5px] px-[5px] py-[2px]">
                            {{ monthNotifications.length }}</span>
                    </h2>
                    <!-- notificacion -->
                    <article class="space-y-2 mt-3">
                        <NotificationCard v-for="(item, index) in monthNotifications" :key="index" @notification-deleted="fetchNotifications"
                            :notification="item" />
                        <p v-if="!monthNotifications.length" class="text-[10px] text-center">No hay notificaciones para
                            mostrar</p>
                    </article>
                </section>
            </div>
        </template>
    </Dropdown>
</template>

<script>
import Dropdown from '@/Components/Dropdown.vue';
import NotificationCard from './NotificationCard.vue';
import axios from 'axios';

export default {
    data() {
        return {
            notifications: [],
        };
    },
    components: {
        Dropdown,
        NotificationCard,
    },
    emits: ['notifications'],
    methods: {
        async fetchNotifications() {
            try {
                const response = await axios.get(route('users.get-notifications'));

                if (response.status === 200) {
                    this.notifications = response.data.items;
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: "No se pudo completar la solicitud",
                    message: "El servidor no pudo procesar la petición para obtener las notificaciones. Recarga la página",
                    type: "error",
                });
            }
        },
        async readNotifications() {
            try {
                const response = await axios.post(route("users.read-user-notifications"));

                // if (response.data.unread) {
                //     this.fetchNotifications();
                // }
            } catch (error) {
                console.log(error);
            }
        },
    },
    computed: {
        getUnreadMessages() {
            const notReadedNotifications = this.notifications?.filter(item => item.read_at === null);
            this.$emit('notifications', notReadedNotifications.length); //emite la cantidad de notificaciones no leidas

            return notReadedNotifications;
        },
        todayNotifications() {
            const today = new Date().toISOString().split('T')[0];
            return this.notifications.filter(notification => notification.created_at.startsWith(today));
        },
        yesterdayNotifications() {
            const yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            const yesterdayStr = yesterday.toISOString().split('T')[0];
            return this.notifications.filter(notification => notification.created_at.startsWith(yesterdayStr));
        },
        monthNotifications() {
            const today = new Date();
            const month = today.getMonth() + 1;
            const year = today.getFullYear();
            const monthStr = `${year}-${month.toString().padStart(2, '0')}`;
            const todayStr = today.toISOString().split('T')[0];
            const yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            const yesterdayStr = yesterday.toISOString().split('T')[0];
            return this.notifications.filter(notification => {
                const createdAt = notification.created_at.split('T')[0];
                return createdAt.startsWith(monthStr) && createdAt !== todayStr && createdAt !== yesterdayStr;
            });
        },
    },
    mounted() {
        this.fetchNotifications();
    },
};
</script>