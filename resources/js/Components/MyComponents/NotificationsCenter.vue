<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import { ElMessage } from 'element-plus';
import { Bell } from '@element-plus/icons-vue';
import NotificationCard from './NotificationCard.vue';

const emit = defineEmits(['notifications']);

const notifications = ref([]);
const loading = ref(true);
const visible = ref(false);
const markedOnOpen = ref(false);

const unread = computed(() => notifications.value.filter((item) => item.read_at === null));

watch(
    unread,
    (list) => emit('notifications', list.length),
    { immediate: true }
);

const todayNotifications = computed(() => {
    const today = new Date().toISOString().split('T')[0];

    return notifications.value.filter((notification) => notification.created_at.startsWith(today));
});

const yesterdayNotifications = computed(() => {
    const yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);
    const yesterdayStr = yesterday.toISOString().split('T')[0];

    return notifications.value.filter((notification) => notification.created_at.startsWith(yesterdayStr));
});

const monthNotifications = computed(() => {
    const today = new Date();
    const month = today.getMonth() + 1;
    const year = today.getFullYear();
    const monthStr = `${year}-${month.toString().padStart(2, '0')}`;
    const todayStr = today.toISOString().split('T')[0];
    const yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);
    const yesterdayStr = yesterday.toISOString().split('T')[0];

    return notifications.value.filter((notification) => {
        const createdAt = notification.created_at.split('T')[0];

        return createdAt.startsWith(monthStr) && createdAt !== todayStr && createdAt !== yesterdayStr;
    });
});

const groups = computed(() => [
    { key: 'today', label: 'Hoy', items: todayNotifications.value },
    { key: 'yesterday', label: 'Ayer', items: yesterdayNotifications.value },
    { key: 'month', label: 'Este mes', items: monthNotifications.value },
]);

const hasNotifications = computed(() => notifications.value.length > 0);

async function fetchNotifications() {
    try {
        loading.value = true;
        const response = await axios.get(route('users.get-notifications'));

        if (response.status === 200) {
            notifications.value = response.data.items || [];
        }
    } catch (error) {
        console.error(error);
        ElMessage.error('No se pudieron cargar las notificaciones. Recarga la página.');
    } finally {
        loading.value = false;
    }
}

async function markAllRead() {
    if (!unread.value.length) return;

    try {
        await axios.post(route('users.read-user-notifications'));
    } catch (error) {
        console.error(error);
    }
}

function applyReadState() {
    const now = new Date().toISOString();
    notifications.value = notifications.value.map((item) =>
        item.read_at ? item : { ...item, read_at: now }
    );
    markedOnOpen.value = false;
}

function handleShow() {
    if (!unread.value.length) return;

    markedOnOpen.value = true;
    markAllRead();
}

function handleHide() {
    if (!markedOnOpen.value) return;

    applyReadState();
}

async function markAllReadNow() {
    await markAllRead();
    applyReadState();
}

onMounted(fetchNotifications);
</script>

<template>
    <el-popover
        v-model:visible="visible"
        placement="bottom-end"
        :width="380"
        :offset="10"
        :show-arrow="false"
        trigger="click"
        popper-class="notifications-popover"
        @show="handleShow"
        @hide="handleHide"
    >
        <template #reference>
            <button
                type="button"
                class="app-topbar__icon-btn app-topbar__icon-btn--bell"
                :class="{ 'is-active': visible }"
                title="Notificaciones"
            >
                <el-icon :size="18"><Bell /></el-icon>
                <span v-if="unread.length" class="app-topbar__badge">
                    {{ unread.length > 9 ? '9+' : unread.length }}
                </span>
            </button>
        </template>

        <div class="notifications-panel">
            <header class="notifications-panel__header">
                <div>
                    <p class="notifications-panel__title">Notificaciones</p>
                    <p class="notifications-panel__subtitle">
                        {{ unread.length ? `${unread.length} sin leer` : 'Todo al día' }}
                    </p>
                </div>
                <el-button
                    v-if="unread.length"
                    text
                    size="small"
                    class="notifications-panel__mark"
                    @click="markAllReadNow"
                >
                    Marcar como leídas
                </el-button>
            </header>

            <div v-if="loading" class="notifications-panel__body">
                <el-skeleton animated :rows="4" />
            </div>

            <div v-else-if="!hasNotifications" class="notifications-panel__empty">
                <span class="notifications-panel__empty-icon">
                    <el-icon :size="24"><Bell /></el-icon>
                </span>
                <p class="notifications-panel__empty-title">No tienes notificaciones</p>
                <p class="notifications-panel__empty-text">
                    Aquí verás los movimientos y recordatorios de tu cuenta.
                </p>
            </div>

            <div v-else class="notifications-panel__body">
                <section
                    v-for="group in groups"
                    v-show="group.items.length"
                    :key="group.key"
                    class="notification-group"
                >
                    <div class="notification-group__head">
                        <span>{{ group.label }}</span>
                        <span class="notification-group__count">{{ group.items.length }}</span>
                    </div>

                    <NotificationCard
                        v-for="item in group.items"
                        :key="item.id"
                        :notification="item"
                        @notification-deleted="fetchNotifications"
                    />
                </section>
            </div>

            <footer class="notifications-panel__footer">
                <Link :href="route('notifications')" class="notifications-panel__link">
                    Ver todas las notificaciones
                </Link>
            </footer>
        </div>
    </el-popover>
</template>
