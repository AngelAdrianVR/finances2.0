<script setup>
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { Close } from '@element-plus/icons-vue';
import { ElMessage } from 'element-plus';
import PaymentIcon from '@/Components/MyComponents/Forms/PaymentIcon.vue';
import { toDateInput } from '@/utils/date';
import { LOAN_PAYMENT_METHODS } from '@/config/loans';
import { formatCurrency, loanRemaining } from '@/utils/loans';

const props = defineProps({
    modelValue: { type: Boolean, default: false },
    loan: { type: Object, required: true },
    payment: { type: Object, default: null },
});

const emit = defineEmits(['update:modelValue']);

const visible = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const isEdit = computed(() => Boolean(props.payment));

const form = useForm({
    amount: null,
    date: format(new Date(), 'yyyy-MM-dd'),
    payment_method: 'Transferencia',
    notes: null,
    loan_id: props.loan.id,
});

const today = () => format(new Date(), 'yyyy-MM-dd');

const balanceBefore = computed(() => {
    if (!props.payment) return loanRemaining(props.loan);

    const previous = (props.loan.payments ?? [])
        .filter((payment) => Number(payment.id) < Number(props.payment.id))
        .sort((a, b) => Number(b.id) - Number(a.id))[0];

    return Number(previous?.remaining ?? props.loan.amount ?? 0);
});

const parsedAmount = computed(() => {
    const value = Number(String(form.amount ?? '').replace(/[^\d.-]/g, ''));

    return Number.isFinite(value) ? value : 0;
});

const amountHint = computed(() => {
    if (parsedAmount.value <= 0) {
        return 'Ingresa el monto que se abonará al préstamo.';
    }

    return `Se aplicarán ${formatCurrency(parsedAmount.value)} al préstamo. El interés y el capital se calculan automáticamente.`;
});

function formatThousands(value) {
    return `${value ?? ''}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function parseAmount(value) {
    return `${value ?? ''}`.replace(/[^\d.]/g, '');
}

function hydrate() {
    form.clearErrors();
    form.loan_id = props.loan.id;

    if (props.payment) {
        form.amount = props.payment.amount;
        form.date = toDateInput(props.payment.date, today());
        form.payment_method = props.payment.payment_method;
        form.notes = props.payment.notes;

        return;
    }

    form.amount = null;
    form.date = today();
    form.payment_method = 'Transferencia';
    form.notes = null;
}

watch(
    () => props.modelValue,
    (open) => {
        if (open) hydrate();
    }
);

function useFullBalance() {
    if (balanceBefore.value <= 0) return;

    form.amount = balanceBefore.value;
}

function submit() {
    if (form.processing) return;

    const options = {
        preserveScroll: true,
        only: ['loan'],
        onSuccess: () => {
            visible.value = false;
            ElMessage.success(isEdit.value ? 'Abono actualizado correctamente.' : 'Abono registrado correctamente.');
        },
    };

    if (isEdit.value) {
        form.put(route('payments.update', props.payment.id), options);

        return;
    }

    form.post(route('payments.store'), options);
}

function close() {
    visible.value = false;
}
</script>

<template>
    <el-dialog
        v-model="visible"
        width="520px"
        align-center
        destroy-on-close
        append-to-body
        :show-close="false"
        class="record-dialog payment-dialog"
    >
        <template #header>
            <div class="record-dialog__header">
                <span class="record-dialog__eyebrow">{{ isEdit ? 'Editar abono' : 'Nuevo abono' }}</span>
                <button type="button" class="record-dialog__close" aria-label="Cerrar" @click="close">
                    <el-icon :size="15"><Close /></el-icon>
                </button>
            </div>
        </template>

        <div class="payment-dialog__hero">
            <p class="payment-dialog__label">Saldo pendiente antes de este abono</p>
            <p class="payment-dialog__balance">{{ formatCurrency(balanceBefore) }}</p>

            <button
                v-if="!isEdit && balanceBefore > 0"
                type="button"
                class="payment-dialog__link"
                @click="useFullBalance"
            >
                Usar saldo completo
            </button>
        </div>

        <el-form :model="form" label-position="top" class="income-form payment-form" @submit.prevent="submit">
            <div class="form-amount-block">
                <el-form-item label="Monto del abono" :error="form.errors.amount" class="form-item--amount">
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

            <div class="form-row form-row--2">
                <el-form-item label="Fecha del abono" :error="form.errors.date">
                    <el-date-picker
                        v-model="form.date"
                        type="date"
                        value-format="YYYY-MM-DD"
                        format="DD MMM YYYY"
                        placeholder="Selecciona la fecha"
                        :clearable="false"
                        class="!w-full"
                    />
                </el-form-item>

                <el-form-item label="Método de pago" :error="form.errors.payment_method">
                    <el-select v-model="form.payment_method" class="!w-full">
                        <el-option
                            v-for="method in LOAN_PAYMENT_METHODS"
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

            <el-form-item label="Comentarios" :error="form.errors.notes">
                <el-input
                    v-model="form.notes"
                    type="textarea"
                    :rows="2"
                    maxlength="500"
                    show-word-limit
                    resize="none"
                    placeholder="Ej. Abono del mes de septiembre (opcional)"
                />
            </el-form-item>

            <p v-if="isEdit" class="payment-dialog__note">
                Al guardar, los abonos posteriores se recalculan automáticamente.
            </p>
        </el-form>

        <template #footer>
            <div class="record-dialog__footer">
                <el-button @click="close">Cancelar</el-button>
                <el-button type="primary" :loading="form.processing" @click="submit">
                    {{ isEdit ? 'Guardar cambios' : 'Registrar abono' }}
                </el-button>
            </div>
        </template>
    </el-dialog>
</template>
