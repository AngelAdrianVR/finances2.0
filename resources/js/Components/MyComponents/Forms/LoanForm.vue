<script setup>
import { computed, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { ElMessage } from 'element-plus';
import { ArrowLeft, InfoFilled, QuestionFilled } from '@element-plus/icons-vue';
import { format } from 'date-fns';
import PaymentIcon from '@/Components/MyComponents/Forms/PaymentIcon.vue';
import { toDateInput } from '@/utils/date';
import {
    LOAN_PERIODICITIES,
    LOAN_PROFITABILITY_MODES,
    LOAN_PROFITABILITY_TYPES,
    LOAN_STATUSES,
    LOAN_TYPES,
} from '@/config/loans';

const props = defineProps({
    mode: { type: String, default: 'create' },
    loan: { type: Object, default: null },
});

const isEdit = computed(() => props.mode === 'edit');

const form = useForm({
    type: props.loan?.type ?? 'Otorgado',
    beneficiary_name: props.loan?.beneficiary_name ?? null,
    lender_name: props.loan?.lender_name ?? null,
    amount: props.loan?.amount ?? null,
    loan_date: toDateInput(props.loan?.loan_date, format(new Date(), 'yyyy-MM-dd')),
    status: props.loan?.status ?? 'En curso',
    payment_periodicity: props.loan?.payment_periodicity ?? null,
    expired_date: toDateInput(props.loan?.expired_date),
    no_interest: props.loan ? !props.loan.profitability : false,
    profitability: props.loan?.profitability ?? null,
    profitability_mode: props.loan?.profitability_mode ?? 'Porcentaje',
    profitability_type: props.loan?.profitability_type ?? 'Amortizable',
    profitability_period: props.loan?.profitability_period ?? null,
    automatic: props.loan ? Boolean(props.loan.automatic) : true,
    description: props.loan?.description ?? null,
});

const showGuide = ref(false);

const typeOptions = LOAN_TYPES.map((type) => ({ label: type.label, value: type.value }));

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

const amountHint = computed(() => {
    const amount = currency.format(parsedAmount.value);

    if (parsedAmount.value <= 0) {
        return form.type === 'Otorgado'
            ? 'Ingresa el monto que prestaste.'
            : 'Ingresa el monto que te prestaron.';
    }

    return form.type === 'Otorgado'
        ? `Se descontarán ${amount} de tu saldo disponible si la gestión automática está activa.`
        : `Se sumarán ${amount} a tu saldo disponible si la gestión automática está activa.`;
});

const personLabel = computed(() =>
    form.type === 'Otorgado' ? 'Nombre del beneficiario' : 'Nombre del prestamista'
);

const personPlaceholder = computed(() =>
    form.type === 'Otorgado' ? '¿A quién le prestaste?' : '¿Quién te prestó?'
);

const typeHint = computed(
    () => LOAN_TYPES.find((type) => type.value === form.type)?.hint ?? ''
);

watch(
    () => form.type,
    (type) => {
        if (type === 'Otorgado') {
            form.lender_name = null;

            return;
        }

        form.beneficiary_name = null;
    }
);

watch(
    () => form.no_interest,
    (noInterest) => {
        if (!noInterest) return;

        form.profitability = null;
        form.profitability_mode = 'Porcentaje';
        form.profitability_period = null;
    }
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
                isEdit.value ? 'Préstamo actualizado correctamente.' : 'Préstamo registrado correctamente.'
            ),
    };

    if (isEdit.value) {
        form.put(route('loans.update', props.loan.id), options);

        return;
    }

    form.post(route('loans.store'), options);
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
                <p class="form-shell__eyebrow">Finanzas · Préstamos</p>
                <h1 class="form-shell__title">
                    {{ isEdit ? 'Editar préstamo' : 'Registrar préstamo' }}
                </h1>
            </div>

            <span v-if="isEdit" class="category-tag tone-primary form-shell__badge">
                {{ form.type === 'Otorgado' ? 'Otorgado' : 'Recibido' }}
            </span>
        </header>

        <!-- Panel -->
        <section class="form-panel">
            <div class="form-panel__body">
                <el-form :model="form" label-position="top" class="income-form" @submit.prevent="submit">
                    <!-- Amount -->
                    <div class="form-amount-block">
                        <el-form-item label="Monto del préstamo" :error="form.errors.amount" class="form-item--amount">
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

                    <!-- Type -->
                    <el-form-item label="Tipo de préstamo" :error="form.errors.type">
                        <el-segmented v-model="form.type" :options="typeOptions" block class="loan-type-toggle" />
                        <p class="form-hint">{{ typeHint }}</p>
                    </el-form-item>

                    <div class="form-row form-row--2">
                        <!-- Person -->
                        <el-form-item
                            :label="personLabel"
                            :error="form.type === 'Otorgado' ? form.errors.beneficiary_name : form.errors.lender_name"
                        >
                            <el-input
                                v-if="form.type === 'Otorgado'"
                                v-model="form.beneficiary_name"
                                maxlength="100"
                                show-word-limit
                                :placeholder="personPlaceholder"
                            />
                            <el-input
                                v-else
                                v-model="form.lender_name"
                                maxlength="100"
                                show-word-limit
                                :placeholder="personPlaceholder"
                            />
                        </el-form-item>

                        <!-- Loan date -->
                        <el-form-item label="Fecha del préstamo" :error="form.errors.loan_date">
                            <el-date-picker
                                v-model="form.loan_date"
                                type="date"
                                value-format="YYYY-MM-DD"
                                format="DD MMM YYYY"
                                placeholder="Selecciona la fecha"
                                :shortcuts="shortcuts"
                                :clearable="false"
                                class="!w-full"
                            />
                        </el-form-item>
                    </div>

                    <div class="form-row form-row--3">
                        <!-- Payment frequency -->
                        <el-form-item label="Frecuencia de pago" :error="form.errors.payment_periodicity">
                            <el-select
                                v-model="form.payment_periodicity"
                                clearable
                                filterable
                                placeholder="Selecciona"
                                class="!w-full"
                            >
                                <el-option
                                    v-for="periodicity in LOAN_PERIODICITIES"
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

                        <!-- Due date -->
                        <el-form-item label="Fecha de vencimiento" :error="form.errors.expired_date">
                            <el-date-picker
                                v-model="form.expired_date"
                                type="date"
                                value-format="YYYY-MM-DD"
                                format="DD MMM YYYY"
                                placeholder="Opcional"
                                :shortcuts="shortcuts"
                                class="!w-full"
                            />
                        </el-form-item>

                        <!-- Status -->
                        <el-form-item label="Estado del préstamo" :error="form.errors.status">
                            <el-select v-model="form.status" class="!w-full">
                                <el-option
                                    v-for="status in LOAN_STATUSES"
                                    :key="status.value"
                                    :label="status.label"
                                    :value="status.value"
                                />
                            </el-select>
                        </el-form-item>
                    </div>

                    <!-- Interests -->
                    <div class="switch-block" :class="{ 'is-on': !form.no_interest }">
                        <div class="switch-block__main">
                            <span class="switch-block__icon">
                                <PaymentIcon name="refresh" :size="16" />
                            </span>

                            <div class="switch-block__text">
                                <p>Genera intereses</p>
                                <span>Configura cómo se calculan los intereses de este préstamo.</span>
                            </div>

                            <el-tooltip
                                content="Si lo desactivas, los abonos se aplicarán completos al capital."
                                placement="top"
                            >
                                <el-icon class="switch-block__info" :size="15"><InfoFilled /></el-icon>
                            </el-tooltip>

                            <el-switch
                                :model-value="!form.no_interest"
                                @update:model-value="form.no_interest = !$event"
                            />
                        </div>

                        <el-collapse-transition>
                            <div v-show="!form.no_interest" class="switch-block__extra">
                                <div class="form-row form-row--2">
                                    <el-form-item label="Interés" :error="form.errors.profitability">
                                        <el-input
                                            v-model="form.profitability"
                                            inputmode="decimal"
                                            placeholder="0.00"
                                            :formatter="formatThousands"
                                            :parser="parseAmount"
                                        >
                                            <template #append>
                                                <el-select v-model="form.profitability_mode" class="loan-mode-select">
                                                    <el-option
                                                        v-for="mode in LOAN_PROFITABILITY_MODES"
                                                        :key="mode.value"
                                                        :label="mode.label"
                                                        :value="mode.value"
                                                    />
                                                </el-select>
                                            </template>
                                        </el-input>
                                    </el-form-item>

                                    <el-form-item label="Periodo de interés" :error="form.errors.profitability_period">
                                        <el-select
                                            v-model="form.profitability_period"
                                            clearable
                                            filterable
                                            placeholder="Selecciona"
                                            class="!w-full"
                                        >
                                            <el-option
                                                v-for="periodicity in LOAN_PERIODICITIES"
                                                :key="periodicity.value"
                                                :label="periodicity.label"
                                                :value="periodicity.value"
                                            />
                                        </el-select>
                                    </el-form-item>
                                </div>

                                <el-form-item label="Tipo de interés" :error="form.errors.profitability_type">
                                    <div class="loan-inline-field">
                                        <el-select
                                            v-model="form.profitability_type"
                                            class="!w-full"
                                            disabled
                                        >
                                            <el-option
                                                v-for="type in LOAN_PROFITABILITY_TYPES"
                                                :key="type"
                                                :label="type"
                                                :value="type"
                                            />
                                        </el-select>
                                        <el-button link type="primary" :icon="QuestionFilled" @click="showGuide = true">
                                            Guía
                                        </el-button>
                                    </div>
                                </el-form-item>
                            </div>
                        </el-collapse-transition>
                    </div>

                    <!-- Automatic management -->
                    <div class="switch-block" :class="{ 'is-on': form.automatic }">
                        <div class="switch-block__main">
                            <span class="switch-block__icon">
                                <PaymentIcon name="transfer" :size="16" />
                            </span>

                            <div class="switch-block__text">
                                <p>Gestión automática</p>
                                <span>Registra ingresos, gastos e intereses y actualiza tu saldo en cada abono.</span>
                            </div>

                            <el-tooltip
                                content="Si lo desactivas, los abonos solo actualizarán el préstamo y no tu saldo disponible."
                                placement="top"
                            >
                                <el-icon class="switch-block__info" :size="15"><InfoFilled /></el-icon>
                            </el-tooltip>

                            <el-switch v-model="form.automatic" />
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
                <p class="form-hint form-hint--inline">
                    Los campos marcados como opcionales pueden completarse después.
                </p>
                <div class="form-actions">
                    <el-button @click="cancel">Cancelar</el-button>
                    <el-button type="primary" :loading="form.processing" @click="submit">
                        {{ isEdit ? 'Guardar cambios' : 'Registrar préstamo' }}
                    </el-button>
                </div>
            </footer>
        </section>

        <!-- Interest guide -->
        <el-dialog v-model="showGuide" title="Guía de tipos de interés" width="540px" append-to-body class="record-dialog">
            <div class="loan-guide">
                <div class="loan-guide__item">
                    <p class="loan-guide__title">Interés simple</p>
                    <p class="loan-guide__text">
                        Pagas una sola vez una cantidad fija de intereses. Ejemplo: pediste $100 y acordaron $10 extra
                        después de un año. Total: $110.
                    </p>
                </div>

                <div class="loan-guide__item">
                    <p class="loan-guide__title">Interés compuesto</p>
                    <p class="loan-guide__text">
                        Los intereses se calculan sobre lo que ya debías. Año 1: $100 + $10 = $110. Año 2: $110 + $11 =
                        $121.
                    </p>
                </div>

                <div class="loan-guide__item">
                    <p class="loan-guide__title">Interés fijo</p>
                    <p class="loan-guide__text">
                        Se acuerda pagar siempre la misma cantidad de intereses. Ejemplo: $1,000 y cada mes pagas $100.
                    </p>
                </div>

                <div class="loan-guide__item">
                    <p class="loan-guide__title">Interés amortizable</p>
                    <p class="loan-guide__text">
                        Cada abono cubre poco a poco la deuda y los intereses. Al principio pagas más intereses y menos
                        capital; al final, más capital y menos intereses.
                    </p>
                </div>

                <p class="loan-guide__note">
                    Actualmente el sistema calcula los abonos con el modelo amortizable, prorrateando el interés según
                    los días transcurridos.
                </p>
            </div>

            <template #footer>
                <el-button @click="showGuide = false">Cerrar</el-button>
            </template>
        </el-dialog>
    </div>
</template>
