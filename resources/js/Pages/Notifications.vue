<script setup>
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';
import { ElMessage } from 'element-plus';
import { Bell, Check } from '@element-plus/icons-vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import NotificationCard from '@/Components/MyComponents/NotificationCard.vue';

const notifications = ref([]);
const loading = ref(true);

const unread = computed(() => notifications.value.filter((item) => item.read_at === null));

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
        const now = new Date().toISOString();
        notifications.value = notifications.value.map((item) =>
            item.read_at ? item : { ...item, read_at: now }
        );
        ElMessage.success('Todas las notificaciones se marcaron como leídas.');
    } catch (error) {
        console.error(error);
        ElMessage.error('No se pudieron marcar las notificaciones como leídas.');
    }
}

onMounted(fetchNotifications);
</script>

<template>
    <AppLayout title="Notificaciones">
        <div class="page-shell">
            <header class="page-heading">
                <div>
                    <p class="page-heading__eyebrow">Centro de actividad</p>
                    <h1 class="page-heading__title">Notificaciones</h1>
                    <p class="page-heading__subtitle">
                        Revisa los movimientos, recordatorios y avisos de tu cuenta.
                    </p>
                </div>

                <div class="page-heading__actions">
                    <el-button v-if="unread.length" @click="markAllRead">
                        <el-icon class="mr-1"><Check /></el-icon>
                        Marcar todas como leídas
                    </el-button>
                </div>
            </header>

            <div class="notifications-page">
                <div class="notifications-page__summary">
                    <div class="notifications-page__stat">
                        <span class="notifications-page__stat-icon notifications-page__stat-icon--primary">
                            <el-icon :size="16"><Bell /></el-icon>
                        </span>
                        <div>
                            <p class="notifications-page__stat-label">Sin leer</p>
                            <p class="notifications-page__stat-value">{{ unread.length }}</p>
                        </div>
                    </div>

                    <div class="notifications-page__stat">
                        <span class="notifications-page__stat-icon notifications-page__stat-icon--success">
                            <el-icon :size="16"><Check /></el-icon>
                        </span>
                        <div>
                            <p class="notifications-page__stat-label">Hoy</p>
                            <p class="notifications-page__stat-value">{{ todayNotifications.length }}</p>
                        </div>
                    </div>

                    <div class="notifications-page__stat">
                        <span class="notifications-page__stat-icon notifications-page__stat-icon--neutral">
                            <el-icon :size="16"><Bell /></el-icon>
                        </span>
                        <div>
                            <p class="notifications-page__stat-label">Total</p>
                            <p class="notifications-page__stat-value">{{ notifications.length }}</p>
                        </div>
                    </div>
                </div>

                <section class="notifications-page__card">
                    <div v-if="loading" class="notifications-page__loading">
                        <el-skeleton animated :rows="5" />
                    </div>

                    <div v-else-if="!notifications.length" class="notifications-page__empty">
                        <span class="notifications-page__empty-icon">
                            <el-icon :size="26"><Bell /></el-icon>
                        </span>
                        <p class="notifications-page__empty-title">No tienes notificaciones</p>
                        <p class="notifications-page__empty-text">
                            Cuando registres movimientos o tengas recordatorios próximos, aparecerán aquí.
                        </p>
                    </div>

                    <div v-else class="notifications-page__list">
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
                </section>
            </div>
        </div>
    </AppLayout>
</template>
