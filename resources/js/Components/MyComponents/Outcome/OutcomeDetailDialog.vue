<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { Close, Edit } from '@element-plus/icons-vue';
import PaymentIcon from '@/Components/MyComponents/Forms/PaymentIcon.vue';
import { OUTCOME_CATEGORIES, OUTCOME_PAYMENT_METHODS } from '@/config/finance';
import { formatCalendarDate } from '@/utils/date';

const props = defineProps({
    modelValue: { type: Boolean, default: false },
    outcome: { type: Object, default: null },
});

const emit = defineEmits(['update:modelValue']);

const visible = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const currency = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    minimumFractionDigits: 2,
});

const method = computed(
    () => OUTCOME_PAYMENT_METHODS.find((item) => item.value === props.outcome?.payment_method) ?? null
);

const categoryLabel = computed(() => {
    const category = OUTCOME_CATEGORIES.find((item) => item.value === props.outcome?.category);

    return category?.label ?? props.outcome?.category ?? 'Sin categoría';
});

const formattedAmount = computed(() => currency.format(Number(props.outcome?.amount ?? 0)));

const formattedDate = computed(() => {
    const text = formatCalendarDate(props.outcome?.created_at, "EEEE d 'de' MMMM, yyyy");

    return text ? text.charAt(0).toUpperCase() + text.slice(1) : '';
});

const formattedDueDate = computed(() => {
    if (!props.outcome?.payment_due_date) return null;

    const text = formatCalendarDate(props.outcome.payment_due_date, "d 'de' MMMM, yyyy");

    return text ? text.charAt(0).toUpperCase() + text.slice(1) : null;
});

const isShared = computed(() => Boolean(props.outcome?.split_enabled));

const sharedTotal = computed(() => Number(props.outcome?.split_total_amount ?? props.outcome?.amount ?? 0));

const sharedCount = computed(() => (props.outcome?.split_with?.length ?? 0) + 1);

// Keeps the two-column grid filled when there is an odd number of short items.
const needsFiller = computed(() => {
    const shortItems = 2 + (props.outcome?.is_credit ? 1 : 0) + (isShared.value ? 1 : 0);

    return shortItems % 2 === 1;
});

function close() {
    visible.value = false;
}

function edit() {
    if (!props.outcome) return;

    router.get(route('outcomes.edit', props.outcome.id));
}
</script>

<template>
    <el-dialog
        v-model="visible"
        width="440px"
        align-center
        destroy-on-close
        :show-close="false"
        class="record-dialog record-dialog--expense"
        append-to-body
    >
        <template #header>
            <div class="record-dialog__header">
                <span class="record-dialog__eyebrow">Detalle del gasto</span>
                <button type="button" class="record-dialog__close" aria-label="Cerrar" @click="close">
                    <el-icon :size="15"><Close /></el-icon>
                </button>
            </div>
        </template>

        <template v-if="outcome">
            <div class="record-dialog__hero">
                <div class="record-dialog__hero-top">
                    <span class="record-dialog__badge">Gasto</span>
                    <span v-if="outcome.is_credit" class="record-dialog__badge record-dialog__badge--warning">
                        A crédito
                    </span>
                    <span v-if="outcome.automatically_created" class="record-dialog__badge record-dialog__badge--muted">
                        Recurrente
                    </span>
                    <span v-if="isShared" class="record-dialog__badge record-dialog__badge--muted">
                        Compartido
                    </span>
                </div>

                <p class="record-dialog__amount">-{{ formattedAmount }}</p>
                <p class="record-dialog__concept">{{ outcome.concept }}</p>

                <div class="record-dialog__chips">
                    <span class="category-tag tone-danger">{{ categoryLabel }}</span>
                    <span v-if="method" class="category-tag">{{ method.label }}</span>
                </div>
            </div>

            <dl class="record-dialog__grid">
                <div class="record-dialog__item">
                    <dt>Fecha</dt>
                    <dd class="record-dialog__value--date">{{ formattedDate }}</dd>
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

                <div v-if="outcome.is_credit" class="record-dialog__item">
                    <dt>Fecha de pago</dt>
                    <dd>
                        <span v-if="formattedDueDate">{{ formattedDueDate }}</span>
                        <span v-else class="record-dialog__empty">No definida</span>
                    </dd>
                </div>

                <div v-if="isShared" class="record-dialog__item">
                    <dt>Gasto compartido</dt>
                    <dd>
                        <span class="record-dialog__inline">
                            <strong>{{ currency.format(sharedTotal) }}</strong>
                            <span class="record-dialog__muted">entre {{ sharedCount }} personas</span>
                        </span>
                    </dd>
                </div>

                <div v-if="needsFiller" class="record-dialog__item record-dialog__item--filler" aria-hidden="true" />

                <div class="record-dialog__item record-dialog__item--full">
                    <dt>Descripción</dt>
                    <dd>
                        <span v-if="outcome.description">{{ outcome.description }}</span>
                        <span v-else class="record-dialog__empty">Sin descripción</span>
                    </dd>
                </div>
            </dl>
        </template>

        <template #footer>
            <div class="record-dialog__footer">
                <el-button @click="close">Cerrar</el-button>
                <el-button type="primary" :icon="Edit" @click="edit">Editar gasto</el-button>
            </div>
        </template>
    </el-dialog>
</template>
