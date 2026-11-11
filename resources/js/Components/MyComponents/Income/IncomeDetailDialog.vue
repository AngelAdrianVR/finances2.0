<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { Close, Edit } from '@element-plus/icons-vue';
import PaymentIcon from '@/Components/MyComponents/Forms/PaymentIcon.vue';
import { INCOME_CATEGORIES, INCOME_PAYMENT_METHODS } from '@/config/finance';
import { formatCalendarDate } from '@/utils/date';

const props = defineProps({
    modelValue: { type: Boolean, default: false },
    income: { type: Object, default: null },
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
    () => INCOME_PAYMENT_METHODS.find((item) => item.value === props.income?.payment_method) ?? null
);

const categoryLabel = computed(() => {
    const category = INCOME_CATEGORIES.find((item) => item.value === props.income?.category);

    return category?.label ?? props.income?.category ?? 'Sin categoría';
});

const formattedAmount = computed(() => currency.format(Number(props.income?.amount ?? 0)));

const formattedDate = computed(() => {
    const text = formatCalendarDate(props.income?.created_at, "EEEE d 'de' MMMM, yyyy");

    return text ? text.charAt(0).toUpperCase() + text.slice(1) : '';
});

function close() {
    visible.value = false;
}

function edit() {
    if (!props.income) return;

    router.get(route('incomes.edit', props.income.id));
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
        append-to-body
    >
        <template #header>
            <div class="record-dialog__header">
                <span class="record-dialog__eyebrow">Detalle del ingreso</span>
                <button type="button" class="record-dialog__close" aria-label="Cerrar" @click="close">
                    <el-icon :size="15"><Close /></el-icon>
                </button>
            </div>
        </template>

        <template v-if="income">
            <div class="record-dialog__hero">
                <div class="record-dialog__hero-top">
                    <span class="record-dialog__badge">Ingreso</span>
                    <span v-if="income.automatically_created" class="record-dialog__badge record-dialog__badge--muted">
                        Recurrente
                    </span>
                </div>

                <p class="record-dialog__amount">+{{ formattedAmount }}</p>
                <p class="record-dialog__concept">{{ income.concept }}</p>

                <div class="record-dialog__chips">
                    <span class="category-tag tone-primary">{{ categoryLabel }}</span>
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

                <div class="record-dialog__item record-dialog__item--full">
                    <dt>Descripción</dt>
                    <dd>
                        <span v-if="income.description">{{ income.description }}</span>
                        <span v-else class="record-dialog__empty">Sin descripción</span>
                    </dd>
                </div>
            </dl>
        </template>

        <template #footer>
            <div class="record-dialog__footer">
                <el-button @click="close">Cerrar</el-button>
                <el-button type="primary" :icon="Edit" @click="edit">Editar ingreso</el-button>
            </div>
        </template>
    </el-dialog>
</template>
