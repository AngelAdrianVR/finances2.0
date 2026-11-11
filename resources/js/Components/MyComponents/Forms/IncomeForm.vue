<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { ElMessage } from 'element-plus';
import { ArrowLeft, InfoFilled } from '@element-plus/icons-vue';
import { format } from 'date-fns';
import PaymentIcon from '@/Components/MyComponents/Forms/PaymentIcon.vue';
import { toDateInput } from '@/utils/date';
import {
    INCOME_CATEGORIES,
    INCOME_PAYMENT_METHODS,
    INCOME_PERIODICITIES,
} from '@/config/finance';

const props = defineProps({
    mode: { type: String, default: 'create' },
    income: { type: Object, default: null },
});

const isEdit = computed(() => props.mode === 'edit');

const form = useForm({
    amount: props.income?.amount ?? null,
    concept: props.income?.concept ?? null,
    category: props.income?.category ?? null,
    payment_method: props.income?.payment_method ?? null,
    created_at: toDateInput(props.income?.created_at, format(new Date(), 'yyyy-MM-dd')),
    description: props.income?.description ?? null,
    is_recurring_income: false,
    periodicity: null,
});

const shortcuts = [
    { text: 'Hoy', value: new Date() },
    {
        text: 'Ayer',
        value: () => {
            const date = new Date();
            date.setDate(date.getDate() - 1);

            return date;
        },
    },
    {
        text: 'Hace una semana',
        value: () => {
            const date = new Date();
            date.setDate(date.getDate() - 7);

            return date;
        },
    },
];

const currency = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    minimumFractionDigits: 2,
});

const parsedAmount = computed(() => {
    const value = Number(String(form.amount ?? '').replace(/[^\d.-]/g, ''));

    return Number.isFinite(value) ? value : 0;
});

const amountHint = computed(() =>
    parsedAmount.value > 0
        ? `Se sumarán ${currency.format(parsedAmount.value)} a tu saldo disponible.`
        : 'Ingresa el monto total de este ingreso.'
);

const selectedMethod = computed(
    () => INCOME_PAYMENT_METHODS.find((method) => method.value === form.payment_method) ?? null
);

function formatThousands(value) {
    return `${value ?? ''}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function parseAmount(value) {
    return `${value ?? ''}`.replace(/[^\d.]/g, '');
}

function submit() {
    if (form.processing) return;

    const options = {
        preserveScroll: true,
        onSuccess: () =>
            ElMessage.success(
                isEdit.value ? 'Ingreso actualizado correctamente.' : 'Ingreso registrado correctamente.'
            ),
    };

    if (isEdit.value) {
        form.put(route('incomes.update', props.income.id), options);

        return;
    }

    form.post(route('incomes.store'), options);
}

function cancel() {
    window.history.back();
}
</script>

<template>
    <div class="form-shell">
        <!-- Heading -->
        <header class="form-shell__bar">
            <button type="button" class="form-back" :title="'Volver'" @click="cancel">
                <el-icon :size="16"><ArrowLeft /></el-icon>
            </button>

            <div class="form-shell__heading">
                <p class="form-shell__eyebrow">Transacciones · Ingresos</p>
                <h1 class="form-shell__title">
                    {{ isEdit ? 'Editar ingreso' : 'Registrar ingreso' }}
                </h1>
            </div>

            <span v-if="income?.automatically_created" class="category-tag tone-success form-shell__badge">
                Generado automáticamente
            </span>
        </header>

        <!-- Panel -->
        <section class="form-panel">
            <div class="form-panel__body">
                <el-form
                    :model="form"
                    label-position="top"
                    class="income-form"
                    @submit.prevent="submit"
                >
                    <!-- Amount -->
                    <div class="form-amount-block">
                        <el-form-item label="Monto" :error="form.errors.amount" class="form-item--amount">
                            <el-input
                                v-model="form.amount"
                                class="amount-input"
                                inputmode="decimal"
                                placeholder="0.00"
                                :formatter="formatThousands"
                                :parser="parseAmount"
                            >
                                <template #prefix>
                                    <span class="amount-input__symbol">$</span>
                                </template>
                                <template #suffix>
                                    <span class="amount-input__currency">MXN</span>
                                </template>
                            </el-input>
                        </el-form-item>
                        <p class="form-hint">{{ amountHint }}</p>
                    </div>

                    <!-- Concept -->
                    <el-form-item label="Concepto" :error="form.errors.concept">
                        <el-input
                            v-model="form.concept"
                            maxlength="50"
                            show-word-limit
                            placeholder="Ej. Venta de ropa"
                        />
                    </el-form-item>

                    <div class="form-row form-row--2">
                        <!-- Date -->
                        <el-form-item label="Fecha" :error="form.errors.created_at">
                            <el-date-picker
                                v-model="form.created_at"
                                type="date"
                                value-format="YYYY-MM-DD"
                                format="DD MMM YYYY"
                                placeholder="Selecciona la fecha"
                                :shortcuts="shortcuts"
                                :clearable="false"
                                class="!w-full"
                            />
                        </el-form-item>

                        <!-- Payment method -->
                        <el-form-item label="Método de pago" :error="form.errors.payment_method">
                            <el-select
                                v-model="form.payment_method"
                                clearable
                                filterable
                                placeholder="Selecciona un método"
                                class="!w-full"
                            >
                                <template v-if="selectedMethod" #prefix>
                                    <PaymentIcon :name="selectedMethod.icon" :size="15" />
                                </template>
                                <el-option
                                    v-for="method in INCOME_PAYMENT_METHODS"
                                    :key="method.value"
                                    :label="method.label"
                                    :value="method.value"
                                >
                                    <span class="option-inline">
                                        <PaymentIcon :name="method.icon" :size="15" />
                                        {{ method.label }}
                                    </span>
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </div>

                    <!-- Category -->
                    <el-form-item label="Categoría" :error="form.errors.category">
                        <el-select
                            v-model="form.category"
                            clearable
                            filterable
                            placeholder="Clasifica este ingreso"
                            class="!w-full"
                        >
                            <el-option
                                v-for="category in INCOME_CATEGORIES"
                                :key="category.value"
                                :label="category.label"
                                :value="category.value"
                                class="option-rich"
                            >
                                <span class="option-stacked">
                                    <span class="option-stacked__label">{{ category.label }}</span>
                                    <span class="option-stacked__hint">{{ category.hint }}</span>
                                </span>
                            </el-option>
                        </el-select>
                    </el-form-item>

                    <!-- Recurring -->
                    <div class="switch-block" :class="{ 'is-on': form.is_recurring_income }">
                        <div class="switch-block__main">
                            <span class="switch-block__icon">
                                <PaymentIcon name="refresh" :size="16" />
                            </span>

                            <div class="switch-block__text">
                                <p>Ingreso recurrente</p>
                                <span>Se programa automáticamente en tu calendario.</span>
                            </div>

                            <el-tooltip
                                content="Registra este ingreso una vez y el sistema creará las siguientes ocurrencias."
                                placement="top"
                            >
                                <el-icon class="switch-block__info" :size="15"><InfoFilled /></el-icon>
                            </el-tooltip>

                            <el-switch v-model="form.is_recurring_income" />
                        </div>

                        <el-collapse-transition>
                            <div v-show="form.is_recurring_income" class="switch-block__extra">
                                <el-form-item label="Frecuencia" :error="form.errors.periodicity">
                                    <el-select v-model="form.periodicity" placeholder="Selecciona la frecuencia" class="!w-full">
                                        <el-option
                                            v-for="periodicity in INCOME_PERIODICITIES"
                                            :key="periodicity.value"
                                            :label="periodicity.label"
                                            :value="periodicity.value"
                                            class="option-rich"
                                        >
                                            <span class="option-stacked">
                                                <span class="option-stacked__label">{{ periodicity.label }}</span>
                                                <span class="option-stacked__hint">{{ periodicity.hint }}</span>
                                            </span>
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </div>
                        </el-collapse-transition>
                    </div>

                    <!-- Description -->
                    <el-form-item label="Descripción" :error="form.errors.description">
                        <el-input
                            v-model="form.description"
                            type="textarea"
                            :rows="2"
                            maxlength="255"
                            show-word-limit
                            resize="none"
                            placeholder="Notas adicionales (opcional)"
                        />
                    </el-form-item>
                </el-form>
            </div>

            <footer class="form-panel__footer">
                <div class="form-actions">
                    <el-button @click="cancel">Cancelar</el-button>
                    <el-button type="primary" :loading="form.processing" @click="submit">
                        {{ isEdit ? 'Guardar cambios' : 'Registrar ingreso' }}
                    </el-button>
                </div>
            </footer>
        </section>
    </div>
</template>
