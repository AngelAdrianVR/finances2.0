<template>
    <AppLayout title="Notificaciones">
        <LoadingLogo v-if="loading" class="my-20" />
        <div v-else class="w-[90%] border rounded-t-[10px] border-grayD9 dark:border-gray-600 dark:bg-[#1E2424] mx-auto mt-4">
            <h1 class="text-gray66 dark:text-gray-300 text-center text-sm my-2">
                Notificaciones
                <span class="text-primary">({{ notifications.length }})</span>
            </h1>
            <div class="px-4 py-2 h-[400px] overflow-y-auto">
                <h1 class="text-sm px-1 dark:text-gray-200">
                    Notificaciones
                </h1>
                <!-- HOY -->
                <section class="mt-3">
                    <h2 class="text-xs px-1 flex items-center space-x-1">
                        <span class="text-[#575757] dark:text-gray-400">Hoy</span>
                        <span class="border-grayD9 dark:border-gray-600 bg-grayF2 dark:bg-gray-800 rounded-[5px] px-[5px] py-[2px] dark:text-gray-300">
                            {{ todayNotifications.length }}</span>
                    </h2>
                    <!-- notificacion de hoy -->
                    <article class="space-y-2 mt-3">
                        <!-- notificaciones cuya propiedad created_at es de hoy -->
                        <NotificationCard v-for="(item, index) in todayNotifications" :key="index" :delete-at-hover="false"
                            @notification-deleted="fetchNotifications" :notification="item" />
                        <p v-if="!todayNotifications.length" class="text-[10px] text-center dark:text-gray-400">No hay notificaciones para
                            mostrar</p>
                    </article>
                </section>
                <!-- Ayer -->
                <section class="mt-3">
                    <h2 class="text-xs px-1 flex items-center space-x-1">
                        <span class="text-[#575757] dark:text-gray-400">Ayer</span>
                        <span class="border-grayD9 dark:border-gray-600 bg-grayF2 dark:bg-gray-800 rounded-[5px] px-[5px] py-[2px] dark:text-gray-300">
                            {{ yesterdayNotifications.length }}</span>
                    </h2>
                    <!-- notificacion -->
                    <article class="space-y-2 mt-3">
                        <NotificationCard v-for="(item, index) in yesterdayNotifications" :key="index" :delete-at-hover="false"
                            @notification-deleted="fetchNotifications" :notification="item" />
                        <p v-if="!yesterdayNotifications.length" class="text-[10px] text-center dark:text-gray-400">No hay notificaciones
                            para mostrar</p>
                    </article>
                </section>
                <!-- Este mes -->
                <section class="mt-3">
                    <h2 class="text-xs px-1 flex items-center space-x-1">
                        <span class="text-[#575757] dark:text-gray-400">Este mes</span>
                        <span class="border-grayD9 dark:border-gray-600 bg-grayF2 dark:bg-gray-800 rounded-[5px] px-[5px] py-[2px] dark:text-gray-300">
                            {{ monthNotifications.length }}</span>
                    </h2>
                    <!-- notificacion -->
                    <article class="space-y-2 mt-3">
                        <NotificationCard v-for="(item, index) in monthNotifications" :key="index" :delete-at-hover="false"
                            @notification-deleted="fetchNotifications" :notification="item" />
                        <p v-if="!monthNotifications.length" class="text-[10px] text-center dark:text-gray-400">
                            No hay notificaciones para mostrar
                        </p>
                    </article>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
<script>
import LoadingLogo from '@/Components/MyComponents/LoadingLogo.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from "axios";
import NotificationCard from '@/Components/MyComponents/NotificationCard.vue';

export default {
    data() {
        return {
            notifications: [],
            loading: false,
        }
    },
    props: {
    },
    components: {
        AppLayout,
        NotificationCard,
        LoadingLogo,
    },
    methods: {
        async fetchNotifications() {
            this.loading = true;
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
            } finally {
                this.loading = false;
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
            return this.notifications?.filter(item => item.read_at === null);
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
}
</script>
