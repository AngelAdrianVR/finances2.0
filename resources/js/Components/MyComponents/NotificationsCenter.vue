<template>
    <Dropdown @click="readNotifications()" align="right" width="notifications" class="mt-2 mr-2" :closeInClick="false">
        <template #trigger>
            <button class="relative text-[#7a7a7a] rounded-full bg-[#D9D9D9] hover:text-gray-700 focus:outline-none p-2 mt-3 focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150" :class="getUnreadMessages.length ? 'text-primary' : 'text-[#97989A]'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
            </button>
            <span v-if="getUnreadMessages.length"
                class="size-4 bg-primary text-white text-[9px] rounded-full absolute top-0 -right-1 flex items-center justify-center border border-white">
                {{ getUnreadMessages.length }}
            </span>
        </template>
        <template #content>
            <h1 class="text-gray66 text-center text-sm my-2">Notificaciones <span class="text-primary">({{
                notifications.length }})</span></h1>
            <header v-if="notifications.length" class="py-1 px-4 flex justify-between items-center border-b mt-3">
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
            </header>
            <main class="h-[220px] overflow-y-auto">
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
            </main>
        </template>
    </Dropdown>
</template>

<script>
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';

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
        this.fetchNotifications();
    },

};
</script>