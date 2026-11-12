<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { ElMessage } from 'element-plus';
import { ArrowLeft, Calendar } from '@element-plus/icons-vue';
import { format } from 'date-fns';
import PaymentIcon from '@/Components/MyComponents/Forms/PaymentIcon.vue';
import { toDateInput } from '@/utils/date';
import {
    INCOME_CATEGORIES,
    OUTCOME_CATEGORIES,
    INCOME_PAYMENT_METHODS,
    INCOME_PERIODICITIES,
} from '@/config/finance';

const props = defineProps({
    type: { type: String, default: 'income' },
    mode: { type: String, default: 'create' },
    record: { type: Object, default: null },
});

const isIncome = computed(() => props.type === 'income');
const isEdit = computed(() => props.mode === 'edit');

const categories = computed(() => (isIncome.value ? INCOME_CATEGORIES : OUTCOME_CATEGORIES));

const copy = computed(() =>
    isIncome.value
        ? {
              eyebrow: 'Transacciones · Ingresos recurrentes',
              title: isEdit.value ? 'Editar ingreso recurrente' : 'Nuevo ingreso recurrente',
              subtitle: 'Se registra una sola vez y se repite según la frecuencia que elijas.',
              amountLabel: 'Monto por ocurrencia',
              amountHint: 'Se sumará a tu saldo en cada ocurrencia.',
              submit: isEdit.value ? 'Guardar cambios' : 'Crear ingreso recurrente',
              success: isEdit.value
                  ? 'Ingreso recurrente actualizado.'
                  : 'Ingreso recurrente registrado.',
              expense: false,
          }
        : {
              eyebrow: 'Transacciones · Gastos recurrentes',
              title: isEdit.value ? 'Editar gasto recurrente' : 'Nuevo gasto recurrente',
              subtitle: 'Se registra una sola vez y se repite según la frecuencia que elijas.',
              amountLabel: 'Monto por ocurrencia',
              amountHint: 'Se descontará de tu saldo en cada ocurrencia.',
              submit: isEdit.value ? 'Guardar cambios' : 'Crear gasto recurrente',
              success: isEdit.value
                  ? 'Gasto recurrente actualizado.'
                  : 'Gasto recurrente registrado.',
              expense: true,
          }
);

const form = useForm({
    amount: props.record?.amount ?? null,
    concept: props.record?.concept ?? null,
    category: props.record?.category ?? null,
    payment_method: props.record?.payment_method ?? null,
    periodicity: props.record?.periodicity ?? null,
    description: props.record?.description ?? null,
    created_at: toDateInput(props.record?.created_at, format(new Date(), 'yyyy-MM-dd')),
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
        ? `${copy.value.amountHint} ${currency.format(parsedAmount.value)} por ocurrencia.`
        : copy.value.amountHint
);

const periodicityHint = computed(() => {
    const periodicity = INCOME_PERIODICITIES.find((item) => item.value === form.periodicity);

    return periodicity?.hint ?? null;
});

function formatThousands(value) {
    return `${value ?? ''}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function parseAmount(value) {
    return `${value ?? ''}`.replace(/[^\d.]/g, '');
}

function submit() {
    if (form.processing) return;

    const base = isIncome.value ? 'recurring-incomes' : 'recurring-outcomes';

    const options = {
        preserveScroll: true,
        onSuccess: () => ElMessage.success(copy.value.success),
    };

    if (isEdit.value) {
        form.put(route(`${base}.update`, props.record.id), options);

        return;
    }

    form.post(route(`${base}.store`), options);
}

function cancel() {
    window.history.back();
}
</script>

<template>
    <div class="form-shell">
        <!-- Heading -->
        <header class="form-shell__bar">
            <button type="button" class="form-back" title="Volver" @click="cancel">
                <el-icon :size="16"><ArrowLeft /></el-icon>
            </button>

            <div class="form-shell__heading">
                <p class="form-shell__eyebrow" :class="{ 'form-shell__eyebrow--expense': copy.expense }">
                    {{ copy.eyebrow }}
                </p>
                <h1 class="form-shell__title">{{ copy.title }}</h1>
            </div>

            <span
                v-if="isEdit && record"
                class="category-tag form-shell__badge"
                :class="record.is_active ? 'tone-success' : 'tone-warning'"
            >
                {{ record.is_active ? 'Activa' : 'Pausada' }}
            </span>
        </header>

        <!-- Panel -->
        <section class="form-panel">
            <div class="form-panel__body">
                <el-form :model="form" label-position="top" class="income-form" @submit.prevent="submit">
                    <!-- Amount -->
                    <div class="form-amount-block">
                        <el-form-item :label="copy.amountLabel" :error="form.errors.amount" class="form-item--amount">
                            <el-input
                                v-model="form.amount"
                                class="amount-input"
                                :class="{ 'amount-input--expense': copy.expense }"
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
                            :placeholder="isIncome ? 'Ej. Renta de propiedad' : 'Ej. Renta departamento'"
                        />
                    </el-form-item>

                    <div class="form-row form-row--2">
                        <!-- Start date -->
                        <el-form-item label="Fecha de inicio" :error="form.errors.created_at">
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

                        <!-- Periodicity -->
                        <el-form-item label="Recurrencia" :error="form.errors.periodicity">
                            <el-select
                                v-model="form.periodicity"
                                placeholder="Selecciona la frecuencia"
                                class="!w-full"
                            >
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

                    <div class="form-row form-row--2">
                        <!-- Category -->
                        <el-form-item label="Categoría" :error="form.errors.category">
                            <el-select
                                v-model="form.category"
                                clearable
                                filterable
                                placeholder="Clasifica este movimiento"
                                class="!w-full"
                            >
                                <el-option
                                    v-for="category in categories"
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

                        <!-- Payment method -->
                        <el-form-item label="Método de pago" :error="form.errors.payment_method">
                            <el-select
                                v-model="form.payment_method"
                                clearable
                                filterable
                                placeholder="Selecciona un método"
                                class="!w-full"
                            >
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

                    <!-- Schedule note -->
                    <div class="schedule-note">
                        <span class="schedule-note__icon">
                            <el-icon :size="16"><Calendar /></el-icon>
                        </span>
                        <div>
                            <p class="schedule-note__title">
                                Se programará automáticamente en tu calendario
                            </p>
                            <p class="schedule-note__text">
                                La primera ocurrencia se agenda a partir de la fecha de inicio
                                <template v-if="periodicityHint"> y se repite: {{ periodicityHint.toLowerCase() }}.</template>
                                <template v-else> y se repite según la frecuencia que elijas.</template>
                            </p>
                        </div>
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
                        {{ copy.submit }}
                    </el-button>
                </div>
            </footer>
        </section>
    </div>
</template>
