<template>
    <AppLayout title="Notificaciones">
        <LoadingLogo v-if="loading" class="my-20" />
        <div v-else class="w-[90%] border rounded-t-[10px] border-grayD9 mx-auto mt-4">
            <h1 class="text-gray66 text-center text-sm my-2">
                Notificaciones
                <span class="text-primary">({{ notifications.length }})</span>
            </h1>
            <div class="px-4 py-2 h-[400px] overflow-y-auto">
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
                        <NotificationCard v-for="(item, index) in todayNotifications" :key="index" :delete-at-hover="false"
                            @notification-deleted="fetchNotifications" :notification="item" />
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
                        <NotificationCard v-for="(item, index) in yesterdayNotifications" :key="index" :delete-at-hover="false"
                            @notification-deleted="fetchNotifications" :notification="item" />
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
                        <NotificationCard v-for="(item, index) in monthNotifications" :key="index" :delete-at-hover="false"
                            @notification-deleted="fetchNotifications" :notification="item" />
                        <p v-if="!monthNotifications.length" class="text-[10px] text-center">
                            No hay notificaciones para mostrar
                        </p>
                    </article>
                </section>
            </div>
            <!-- <main class="h-[60vh] overflow-y-auto">
                <el-empty v-if="!notifications.length" description="No hay notificaciones" :image-size="90" />
                <div @click="handleClickNotification(item.id)" v-for="item in notifications" :key="item.id"
                    :href="item.data.url"
                    class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                    :class="{ 'bg-primarylight': item.read_at === null }">
                    <div class="relative">
                        <div v-if="item.read_at === null"
                            class="size-1 bg-primary rounded-full absolute top-[6px] -right-2">
                        </div>
                        <div class="flex">
                            <label class="w-[8%]">
                                <input type="checkbox" @change="handleItemChecked()" @click.stop v-model="selectedItems"
                                    :value="item.id"
                                    class="size-3 rounded-sm border-[#999999] text-primary shadow-sm focus:ring-primary bg-transparent disabled:border-gray-300 disabled:bg-gray-200 disabled:cursor-not-allowed" />
                            </label>
                            <figure class="w-[14%] rounded-full">
                                <img class="size-7 rounded-full object-cover" :src="item.data.user_photo"
                                    :alt="item.data.user_name">
                            </figure>
                            <section class="w-[78%] text-xs">
                                <p :class="{ 'text-primary': item.read_at === null }">
                                    <span class="text-gray66 mr-1">{{ item.data.user_name }}</span>
                                    {{ item.data.description }}
                                </p>
                            </section>
                        </div>
                        <small class="mt-1 text-gray2 text-[10px]">{{ item.created_at_for_humans }}</small>
                    </div>
                </div>
            </main>
            <footer v-if="notifications.length" class="py-1 px-2 flex justify-between items-center border-t mt-3">
                <label class="text-gray99 text-xs has-[:checked]:text-primary">
                    <Checkbox v-model:checked="allItems" @change="handleChange" class="size-3 mr-2" />
                    <span>Seleccionar todos</span>
                </label>
                <div class="flex space-x-2">
                    <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#D72C8A"
                        :title="'¿Desea marcar como leidas las notificaciones seleccionadas (' + selectedItems.length + ')?'"
                        @confirm="readNotifications()">
                        <template #reference>
                            <button
                                class="flex justify-center items-center size-6 text-xs rounded-[5px] bg-primarylight text-primary disabled:cursor-not-allowed disabled:bg-grayED disabled:text-gray66"
                                :disabled="!selectedItems.length">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </button>
                        </template>
</el-popconfirm>
<el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#D72C8A"
    :title="'¿Desea eliminar las notificaciones seleccionadas (' + selectedItems.length + ')?'"
    @confirm="deleteNotifications()">
    <template #reference>
                            <button
                                class="flex justify-center items-center size-6 text-xs rounded-[5px] bg-primarylight text-primary disabled:cursor-not-allowed disabled:bg-grayED disabled:text-gray66"
                                :disabled="!selectedItems.length">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </template>
</el-popconfirm>
</div>
</footer> -->
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
