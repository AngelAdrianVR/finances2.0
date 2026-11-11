<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ElMessage } from 'element-plus';
import { ArrowLeft, InfoFilled, User } from '@element-plus/icons-vue';
import { format } from 'date-fns';
import PaymentIcon from '@/Components/MyComponents/Forms/PaymentIcon.vue';
import { toDateInput } from '@/utils/date';
import {
    OUTCOME_CATEGORIES,
    OUTCOME_PAYMENT_METHODS,
    OUTCOME_PERIODICITIES,
} from '@/config/finance';
import axios from 'axios';

const props = defineProps({
    mode: { type: String, default: 'create' },
    outcome: { type: Object, default: null },
});

const isEdit = computed(() => props.mode === 'edit');

// Un gasto compartido guarda solo la parte del creador, así que el formulario
// se inicializa con el monto total para no volver a dividir la parte ya dividida.
const initialAmount = props.outcome?.split_total_amount ?? props.outcome?.amount ?? null;

const form = useForm({
    amount: initialAmount,
    concept: props.outcome?.concept ?? null,
    category: props.outcome?.category ?? null,
    payment_method: props.outcome?.payment_method ?? null,
    created_at: toDateInput(props.outcome?.created_at, format(new Date(), 'yyyy-MM-dd')),
    description: props.outcome?.description ?? null,
    is_recurring_outcome: false,
    periodicity: null,
    split_enabled: props.outcome?.split_enabled ?? false,
    split_with: props.outcome?.split_with ?? [],
    is_credit: props.outcome?.is_credit ?? false,
    payment_due_date: props.outcome?.payment_due_date
        ? toDateInput(props.outcome.payment_due_date)
        : null,
});

const linkedUsers = ref([]);

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
        ? `Se descontarán ${currency.format(parsedAmount.value)} de tu saldo disponible.`
        : 'Ingresa el monto total de este gasto.'
);

const selectedMethod = computed(
    () => OUTCOME_PAYMENT_METHODS.find((method) => method.value === form.payment_method) ?? null
);

const participants = computed(() => form.split_with?.length + 1);

const splitAmount = computed(() => {
    if (!form.split_enabled || !form.split_with?.length || !parsedAmount.value) return null;

    return parsedAmount.value / participants.value;
});

const paymentDueDateLabel = computed(() =>
    form.payment_due_date ? toDateInput(form.payment_due_date).split('-').reverse().join('/') : ''
);

watch(
    () => form.is_recurring_outcome,
    (value) => {
        if (!value) form.periodicity = null;
    }
);

watch(
    () => form.is_credit,
    (value) => {
        if (!value) form.payment_due_date = null;
    }
);

watch(
    () => form.split_enabled,
    (value) => {
        if (!value) form.split_with = [];
    }
);

async function fetchLinkedUsers() {
    try {
        const { data } = await axios.get(route('outcomes.get-linked-users'));
        linkedUsers.value = data.linked_users ?? [];
    } catch (error) {
        console.error(error);
    }
}

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
                isEdit.value ? 'Gasto actualizado correctamente.' : 'Gasto registrado correctamente.'
            ),
    };

    if (isEdit.value) {
        form.put(route('outcomes.update', props.outcome.id), options);

        return;
    }

    form.post(route('outcomes.store'), options);
}

function cancel() {
    window.history.back();
}

onMounted(fetchLinkedUsers);
</script>

<template>
    <div class="form-shell">
        <!-- Heading -->
        <header class="form-shell__bar">
            <button type="button" class="form-back" title="Volver" @click="cancel">
                <el-icon :size="16"><ArrowLeft /></el-icon>
            </button>

            <div class="form-shell__heading">
                <p class="form-shell__eyebrow form-shell__eyebrow--expense">Transacciones · Gastos</p>
                <h1 class="form-shell__title">
                    {{ isEdit ? 'Editar gasto' : 'Registrar gasto' }}
                </h1>
            </div>

            <span v-if="outcome?.automatically_created" class="category-tag tone-warning form-shell__badge">
                Generado automáticamente
            </span>
        </header>

        <!-- Panel -->
        <section class="form-panel">
            <div class="form-panel__body">
                <el-form :model="form" label-position="top" class="income-form" @submit.prevent="submit">
                    <!-- Amount -->
                    <div class="form-amount-block">
                        <el-form-item label="Monto" :error="form.errors.amount" class="form-item--amount">
                            <el-input
                                v-model="form.amount"
                                class="amount-input amount-input--expense"
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
                            placeholder="Ej. Compra de supermercado"
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
                                    v-for="method in OUTCOME_PAYMENT_METHODS"
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
                            placeholder="Clasifica este gasto"
                            class="!w-full"
                        >
                            <el-option
                                v-for="category in OUTCOME_CATEGORIES"
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

                    <!-- Credit -->
                    <div class="switch-block switch-block--expense" :class="{ 'is-on': form.is_credit }">
                        <div class="switch-block__main">
                            <span class="switch-block__icon">
                                <PaymentIcon name="calendar" :size="16" />
                            </span>

                            <div class="switch-block__text">
                                <p>Gasto a crédito</p>
                                <span>Lo pagarás en una fecha posterior.</span>
                            </div>

                            <el-tooltip
                                content="Te recordaremos por correo antes de la fecha de pago."
                                placement="top"
                            >
                                <el-icon class="switch-block__info" :size="15"><InfoFilled /></el-icon>
                            </el-tooltip>

                            <el-switch v-model="form.is_credit" />
                        </div>

                        <el-collapse-transition>
                            <div v-show="form.is_credit" class="switch-block__extra">
                                <el-form-item label="Fecha de pago (opcional)" :error="form.errors.payment_due_date">
                                    <el-date-picker
                                        v-model="form.payment_due_date"
                                        type="date"
                                        value-format="YYYY-MM-DD"
                                        format="DD MMM YYYY"
                                        placeholder="Selecciona la fecha de pago"
                                        :shortcuts="shortcuts"
                                        class="!w-full"
                                    />
                                </el-form-item>

                                <p v-if="form.payment_due_date" class="switch-block__note">
                                    Recibirás <strong>3 avisos</strong> por correo antes del {{ paymentDueDateLabel }}.
                                    Puedes ajustarlos en
                                    <Link :href="route('settings.index')" class="switch-block__link">Configuraciones</Link>.
                                </p>
                                <p v-else class="switch-block__note">
                                    Sin fecha no se programan recordatorios de pago.
                                </p>
                            </div>
                        </el-collapse-transition>
                    </div>

                    <!-- Split -->
                    <div
                        v-if="linkedUsers.length"
                        class="switch-block switch-block--expense"
                        :class="{ 'is-on': form.split_enabled }"
                    >                        <div class="switch-block__main">
                            <span class="switch-block__icon">
                                <el-icon :size="16"><User /></el-icon>
                            </span>

                            <div class="switch-block__text">
                                <p>Dividir gasto</p>
                                <span>Reparte el monto en partes iguales.</span>
                            </div>

                            <el-tooltip
                                content="El monto se dividirá entre ti y las personas que elijas."
                                placement="top"
                            >
                                <el-icon class="switch-block__info" :size="15"><InfoFilled /></el-icon>
                            </el-tooltip>

                            <el-switch v-model="form.split_enabled" />
                        </div>

                        <el-collapse-transition>
                            <div v-show="form.split_enabled" class="switch-block__extra">
                                <el-form-item label="Dividir con" :error="form.errors['split_with.0'] ?? form.errors.split_with">
                                    <el-select
                                        v-model="form.split_with"
                                        filterable
                                        multiple
                                        placeholder="Selecciona personas vinculadas"
                                        class="!w-full"
                                    >
                                        <el-option
                                            v-for="user in linkedUsers"
                                            :key="user.id"
                                            :label="user.name"
                                            :value="user.id"
                                        >
                                            <span class="option-inline option-inline--user">
                                                <img :src="user.profile_photo_url" :alt="user.name" class="option-avatar" />
                                                <span>{{ user.name }}</span>
                                                <span class="option-muted">{{ user.email }}</span>
                                            </span>
                                        </el-option>
                                    </el-select>
                                </el-form-item>

                                <p v-if="splitAmount" class="switch-block__note">
                                    {{ participants }} personas ·
                                    <strong>{{ currency.format(splitAmount) }}</strong> por persona
                                </p>
                            </div>
                        </el-collapse-transition>
                    </div>

                    <p v-if="!linkedUsers.length" class="form-hint form-hint--inline">
                        <Link :href="route('settings.index')" class="switch-block__link">Vincula personas</Link>
                        en Configuraciones para poder dividir gastos.
                    </p>

                    <!-- Recurring -->
                    <div class="switch-block switch-block--expense" :class="{ 'is-on': form.is_recurring_outcome }">
                        <div class="switch-block__main">
                            <span class="switch-block__icon">
                                <PaymentIcon name="refresh" :size="16" />
                            </span>

                            <div class="switch-block__text">
                                <p>Gasto recurrente</p>
                                <span>Se programa automáticamente en tu calendario.</span>
                            </div>

                            <el-tooltip
                                content="Registra este gasto una vez y el sistema creará las siguientes ocurrencias."
                                placement="top"
                            >
                                <el-icon class="switch-block__info" :size="15"><InfoFilled /></el-icon>
                            </el-tooltip>

                            <el-switch v-model="form.is_recurring_outcome" />
                        </div>

                        <el-collapse-transition>
                            <div v-show="form.is_recurring_outcome" class="switch-block__extra">
                                <el-form-item label="Frecuencia" :error="form.errors.periodicity">
                                    <el-select v-model="form.periodicity" placeholder="Selecciona la frecuencia" class="!w-full">
                                        <el-option
                                            v-for="periodicity in OUTCOME_PERIODICITIES"
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
                        {{ isEdit ? 'Guardar cambios' : 'Registrar gasto' }}
                    </el-button>
                </div>
            </footer>
        </section>
    </div>
</template>
