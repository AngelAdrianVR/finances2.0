<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import { ElMessage } from 'element-plus';
import { Bell, Clock, Delete, Download, Upload } from '@element-plus/icons-vue';

const props = defineProps({
    notification: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['notification-deleted']);

const type = computed(() => props.notification.data?.type);

const typeTone = computed(() => ({
    income: 'success',
    outcome: 'danger',
    schedule: 'info',
    reminder: 'warning',
})[type.value] || 'neutral');

const typeIcon = computed(() => ({
    income: Download,
    outcome: Upload,
    schedule: Clock,
    reminder: Bell,
})[type.value] || Bell);

async function deleteNotification() {
    try {
        const response = await axios.post(route('users.delete-user-notification'), {
            notification_id: props.notification.id,
        });

        if (response.status === 200) {
            emit('notification-deleted', props.notification.id);
            ElMessage.success(response.data.message || 'Notificación eliminada.');
        }
    } catch (error) {
        console.error(error);
        ElMessage.error('No se pudo eliminar la notificación. Inténtalo más tarde.');
    }
}
</script>

<template>
    <div class="notification-card" :class="{ 'is-unread': notification.read_at === null }">
        <Link :href="notification.data.url || '#'" class="notification-card__main">
            <span class="notification-card__icon" :class="`notification-card__icon--${typeTone}`">
                <el-icon :size="16"><component :is="typeIcon" /></el-icon>
            </span>

            <span class="notification-card__body">
                <span class="notification-card__description">{{ notification.data.description }}</span>
                <span class="notification-card__time">{{ notification.created_at_for_humans }}</span>
            </span>
        </Link>

        <span v-if="notification.read_at === null" class="notification-card__dot" aria-label="Sin leer" />

        <el-popconfirm
            confirm-button-text="Sí"
            cancel-button-text="No"
            icon-color="#D64545"
            :title="'¿Deseas eliminar esta notificación?'"
            @confirm="deleteNotification"
        >
            <template #reference>
                <button type="button" class="notification-card__delete" title="Eliminar notificación" aria-label="Eliminar notificación">
                    <el-icon :size="13"><Delete /></el-icon>
                </button>
            </template>
        </el-popconfirm>
    </div>
</template>
