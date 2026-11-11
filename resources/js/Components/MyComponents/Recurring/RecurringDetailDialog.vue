<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { ElMessage } from 'element-plus';
import axios from 'axios';
import { Close, Edit, VideoPause, VideoPlay } from '@element-plus/icons-vue';
import PaymentIcon from '@/Components/MyComponents/Forms/PaymentIcon.vue';
import {
    INCOME_CATEGORIES,
    OUTCOME_CATEGORIES,
    INCOME_PAYMENT_METHODS,
    INCOME_PERIODICITIES,
} from '@/config/finance';
import { formatCalendarDate } from '@/utils/date';

const props = defineProps({
    modelValue: { type: Boolean, default: false },
    record: { type: Object, default: null },
    type: { type: String, default: 'income' },
});

const emit = defineEmits(['update:modelValue', 'toggled']);

const isIncome = computed(() => props.type === 'income');
const baseRoute = computed(() => (isIncome.value ? 'recurring-incomes' : 'recurring-outcomes'));

const visible = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const currency = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    minimumFractionDigits: 2,
});

const categories = computed(() => (isIncome.value ? INCOME_CATEGORIES : OUTCOME_CATEGORIES));

const categoryLabel = computed(() => {
    const category = categories.value.find((item) => item.value === props.record?.category);

    return category?.label ?? props.record?.category ?? 'Sin categoría';
});

const method = computed(
    () => INCOME_PAYMENT_METHODS.find((item) => item.value === props.record?.payment_method) ?? null
);

const periodicity = computed(
    () => INCOME_PERIODICITIES.find((item) => item.value === props.record?.periodicity) ?? null
);

const formattedAmount = computed(() => {
    const amount = currency.format(Number(props.record?.amount ?? 0));

    return isIncome.value ? `+${amount}` : `-${amount}`;
});

const formattedStartDate = computed(() => {
    const text = formatCalendarDate(props.record?.created_at, "d 'de' MMMM, yyyy");

    return text ? text.charAt(0).toUpperCase() + text.slice(1) : '';
});

// Even count of short items keeps the two-column grid filled.
function close() {
    visible.value = false;
}

function edit() {
    if (!props.record) return;

    router.get(route(`${baseRoute.value}.edit`, props.record.id));
}

async function toggleStatus() {
    if (!props.record) return;

    try {
        const { data } = await axios.get(route(`${baseRoute.value}.toggle-status`, props.record.id));

        emit('toggled', { id: props.record.id, isActive: data.is_active });

        ElMessage.success(
            data.is_active ? 'Recurrencia reactivada.' : 'Recurrencia pausada.'
        );
    } catch (error) {
        console.error(error);
        ElMessage.error('No se pudo cambiar el estado.');
    }
}
</script>

<template>
    <el-dialog
        v-model="visible"
        width="440px"
        align-center
        destroy-on-close
        :show-close="false"
        class="record-dialog"
        :class="{ 'record-dialog--expense': !isIncome, 'record-dialog--paused': record && !record.is_active }"
        append-to-body
    >
        <template #header>
            <div class="record-dialog__header">
                <span class="record-dialog__eyebrow">
                    {{ isIncome ? 'Detalle del ingreso recurrente' : 'Detalle del gasto recurrente' }}
                </span>
                <button type="button" class="record-dialog__close" aria-label="Cerrar" @click="close">
                    <el-icon :size="15"><Close /></el-icon>
                </button>
            </div>
        </template>

        <template v-if="record">
            <div class="record-dialog__hero">
                <div class="record-dialog__hero-top">
                    <span class="record-dialog__badge">
                        {{ isIncome ? 'Ingreso recurrente' : 'Gasto recurrente' }}
                    </span>
                    <span class="record-dialog__badge record-dialog__badge--muted">
                        {{ periodicity?.label ?? record.periodicity }}
                    </span>
                    <span class="record-dialog__badge" :class="record.is_active ? 'record-dialog__badge--active' : 'record-dialog__badge--paused'">
                        {{ record.is_active ? 'Activa' : 'Pausada' }}
                    </span>
                </div>

                <p class="record-dialog__amount">{{ formattedAmount }}</p>
                <p class="record-dialog__concept">{{ record.concept }}</p>

                <div class="record-dialog__chips">
                    <span class="category-tag" :class="isIncome ? 'tone-success' : 'tone-danger'">
                        {{ categoryLabel }}
                    </span>
                    <span v-if="method" class="category-tag">{{ method.label }}</span>
                </div>
            </div>

            <dl class="record-dialog__grid">
                <div class="record-dialog__item">
                    <dt>Inicio</dt>
                    <dd class="record-dialog__value--date">{{ formattedStartDate }}</dd>
                </div>

                <div class="record-dialog__item">
                    <dt>Frecuencia</dt>
                    <dd>
                        <span class="record-dialog__inline">
                            {{ periodicity?.label ?? record.periodicity }}
                            <span v-if="periodicity" class="record-dialog__muted">· {{ periodicity.hint }}</span>
                        </span>
                    </dd>
                </div>

                <div class="record-dialog__item">
                    <dt>Método de pago</dt>
                    <dd>
                        <span v-if="method" class="record-dialog__inline">
                            <PaymentIcon :name="method.icon" :size="15" />
                            {{ method.label }}
                        </span>
                        <span v-else class="record-dialog__empty">Sin especificar</span>
                    </dd>
                </div>

                <div class="record-dialog__item">
                    <dt>Estado</dt>
                    <dd>
                        <span class="record-dialog__inline">
                            <span
                                class="record-dialog__dot"
                                :class="record.is_active ? 'is-active' : 'is-paused'"
                            />
                            {{ record.is_active ? 'Se registra automáticamente' : 'Pausada, no se registra' }}
                        </span>
                    </dd>
                </div>

                <div class="record-dialog__item record-dialog__item--full">
                    <dt>Descripción</dt>
                    <dd>
                        <span v-if="record.description">{{ record.description }}</span>
                        <span v-else class="record-dialog__empty">Sin descripción</span>
                    </dd>
                </div>
            </dl>

            <p class="record-dialog__footnote">
                Las ocurrencias se agregan a tu calendario y a tus movimientos automáticamente.
            </p>
        </template>

        <template #footer>
            <div class="record-dialog__footer">
                <button
                    v-if="record"
                    type="button"
                    class="record-dialog__ghost"
                    @click="toggleStatus"
                >
                    <el-icon :size="14">
                        <VideoPause v-if="record.is_active" />
                        <VideoPlay v-else />
                    </el-icon>
                    {{ record.is_active ? 'Pausar' : 'Reactivar' }}
                </button>

                <div class="record-dialog__footer-actions">
                    <el-button @click="close">Cerrar</el-button>
                    <el-button type="primary" :icon="Edit" @click="edit">
                        {{ isIncome ? 'Editar ingreso' : 'Editar gasto' }}
                    </el-button>
                </div>
            </div>
        </template>
    </el-dialog>
</template>
