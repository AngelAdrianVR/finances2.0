<template>
    <Dropdown @click="readNotifications()" align="left" width="notifications" :closeInClick="false">
        <template #trigger>
            <div class="mr-3 relative">
                <div class="group relative ml-3">
                    <button class="rounded-full size-9 flex items-center justify-center"
                        :class="route().current('login.*') ? 'bg-[#D3F6E6] text-primary' : 'text-gray-600'">
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
                class="size-4 bg-primary text-white text-[9px] rounded-full absolute top-0 -right-1 flex items-center justify-center border border-white">
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
                        <span class="border-grayD9 bg-grayF2 rounded-[5px] px-[5px] py-[2px]">3</span>
                    </h2>
                    <!-- notificacion de hoy -->
                    <article class="space-y-2 mt-3">
                        <div v-for="(item, index) in 3" :key="index"
                            class="group border border-grayD9 rounded-[10px] px-3 py-2 shadow-md flex space-x-1">
                            <div class="w-[10%]">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4 text-gray99">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                </svg>
                            </div>
                            <Link class="w-[80%]">
                            <p class="text-xs">
                                Se registro un ingreso programado, por la cantidad de $1,500.00 por concepto de
                                Nómina.
                            </p>
                            <span class="text-gray99 text-xs">Hace 2 horas</span>
                            </Link>
                            <div class="w-[10%] flex flex-col items-end justify-between">
                                <div class="size-[6px] rounded-full bg-primary"></div>
                                <div class="scale-0 transition-all duration-300 ease-in-out group-hover:scale-100">
                                    <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#373737"
                                        :title="'¿Deseas eliminar la notificacion seleccionada?'"
                                        @confirm="deleteNotifications()">
                                        <template #reference>
                                            <button>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-3 text-[#8D0A0A]">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </template>
                                    </el-popconfirm>
                                </div>
                            </div>
                        </div>
                    </article>
                </section>
                <!-- Ayer -->
                <section class="mt-3">
                    <h2 class="text-xs px-1 flex items-center space-x-1">
                        <span class="text-[#575757]">Ayer</span>
                        <span class="border-grayD9 bg-grayF2 rounded-[5px] px-[5px] py-[2px]">2</span>
                    </h2>
                    <!-- notificacion -->
                    <article class="space-y-2 mt-3">
                        <div v-for="(item, index) in 2" :key="index"
                            class="border border-grayD9 rounded-[10px] px-3 py-2 shadow-md flex space-x-2">
                            <div class="w-[10%]">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5 text-gray99">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                </svg>
                            </div>
                            <div class="w-[80%] text-xs">
                                <p>
                                    Recordatorio. Pago de agua
                                </p>
                                <span class="text-gray99">Hace 15 horas</span>
                            </div>
                            <div class="w-[10%] flex justify-end">
                                <div class="size-[6px] rounded-full bg-primary"></div>
                            </div>
                        </div>
                    </article>
                </section>
                <!-- Este mes -->
                <section class="mt-3">
                    <h2 class="text-xs px-1 flex items-center space-x-1">
                        <span class="text-[#575757]">Este mes</span>
                        <span v-if="false" class="border-grayD9 bg-grayF2 rounded-[5px] px-[5px] py-[2px]">2</span>
                    </h2>
                    <!-- notificacion -->
                    <article class="space-y-2 mt-3">
                        <div v-for="(item, index) in 4" :key="index"
                            class="border border-grayD9 rounded-[10px] px-3 py-2 shadow-md flex space-x-2">
                            <div class="w-[10%]">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5 text-gray99">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                </svg>
                            </div>
                            <div class="w-[80%] text-xs">
                                <p>
                                    Evento. Revisar inversiones hoy a las 02:30
                                </p>
                                <span class="text-gray99">01 enero</span>
                            </div>
                            <div class="w-[10%] flex justify-end">
                                <div v-if="false" class="size-[6px] rounded-full bg-primary"></div>
                            </div>
                        </div>
                    </article>
                </section>
                <!-- <header v-if="notifications.length" class="py-1 px-4 flex justify-between items-center border-b mt-3">
                    <label class="text-gray99 text-xs has-[:checked]:text-primary">
                        <Checkbox v-model:checked="allItems" @change="handleChange" class="size-3 mr-2" />
                        <span>Seleccionar todos</span>
                    </label>
                    <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#373737"
                        :title="'¿Desea eliminar las notificaciones seleccionadas (' + selectedItems.length + ')?'"
                        @confirm="deleteNotifications()">
                        <template #reference>
                            <button
                                class="flex justify-center items-center size-6 text-xs rounded-[5px] bg-primarylight text-primary disabled:cursor-not-allowed disabled:bg-grayF2 disabled:text-gray66"
                                :disabled="!selectedItems.length">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </template>
</el-popconfirm>
</header> -->
                <!-- <main class="h-[220px] overflow-y-auto">
                    <el-empty v-if="!notifications.length" description="No hay notificaciones" :image-size="90" />
                    <DropdownLink v-for="item in notifications" :key="item.id" :href="item.data.url"
                        :class="{ 'bg-primarylight': item.read_at === null }">
                        <div class="relative">
                            <div v-if="item.read_at === null"
                                class="size-1 bg-primary rounded-full absolute top-[6px] -right-2"></div>
                            <div class="flex">
                                <label class="w-[8%]">
                                    <input type="checkbox" @change="handleItemChecked()" @click.stop v-model="selectedItems"
                                        :value="item.id"
                                        class="size-3 rounded-sm border-[#999999] text-primary shadow-sm focus:ring-primary bg-transparent disabled:border-gray-300 disabled:bg-gray-200 disabled:cursor-not-allowed" />
                                </label>
                                <section class="w-[78%] text-xs">
                                    <p :class="{ 'text-primary': item.read_at === null }">
                                        <span class="text-gray66 font-bold mr-1">{{ item.data.title }}.</span>
                                        <span v-html="item.data.description"></span>
                                    </p>
                                </section>
                            </div>
                            <p class="text-right text-gray99 text-[10px]">{{ item.created_at_for_humans }}</p>
                        </div>
                    </DropdownLink>
                </main> -->
            </div>
        </template>
    </Dropdown>
</template>

<script>
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { Link } from '@inertiajs/vue3';

export default {
    data() {
        return {
            // showNotificationPopup: false,
            allItems: false,
            notifications: [],
            selectedItems: [],
        };
    },
    components: {
        Dropdown,
        DropdownLink,
        PrimaryButton,
        Checkbox,
        Link,
    },
    methods: {
        // toggleNotificationPopup() {
        //     this.showNotificationPopup = !this.showNotificationPopup;
        // },
        handleChange() {
            if (this.allItems) {
                this.selectedItems = this.notifications.map(notification => notification.id);
            } else {
                this.selectedItems = [];
            }
        },
        handleItemChecked() {
            if (this.selectedItems.length == this.notifications.length) {
                this.allItems = true;
            } else if (this.selectedItems.length < this.notifications.length && this.allItems) {
                this.allItems = false;
            }
        },
        async deleteNotifications() {
            try {
                const response = await axios.post(route('users.delete-user-notifications'), { notifications_ids: this.selectedItems });

                if (response.status === 200) {
                    // Filtrar el arreglo excluyendo los elementos con IDs en 'selectedItems'
                    this.notifications = this.notifications.filter(item => !this.selectedItems.includes(item.id));
                    this.$notify({
                        title: "Éxito",
                        message: response.data.message,
                        type: "success",
                    });
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: "No se pudo completar la solicitud",
                    message: "El servidor no pudo procesar la petición, inténtalo más tarde",
                    type: "error",
                });
            }
        },
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
                    message: "El servidor no pudo procesar la petición para obtener las notificaciones. Inténtalo más tarde",
                    type: "error",
                });
            }
        },
        async readNotifications() {
            try {
                const response = await axios.post(route("users.read-user-notifications"));

                if (response.data.unread) {
                    this.fetchNotifications();
                }
            } catch (error) {
                console.log(error);
            }
        },
    },
    computed: {
        getUnreadMessages() {
            return this.notifications?.filter(item => item.read_at === null);
        }
    },
    mounted() {
        // this.fetchNotifications();
    },

};
</script>