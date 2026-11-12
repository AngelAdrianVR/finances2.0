<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Edit, Tickets } from '@element-plus/icons-vue';
import PaymentIcon from '@/Components/MyComponents/Forms/PaymentIcon.vue';
import { formatCalendarDate } from '@/utils/date';
import { formatCurrency } from '@/utils/loans';
import { LOAN_PAYMENT_METHODS } from '@/config/loans';

const props = defineProps({
    payments: { type: Array, default: () => [] },
    readonly: { type: Boolean, default: false },
});

const emit = defineEmits(['edit']);

const viewportWidth = ref(window.innerWidth);
const tableMaxHeight = ref(420);

function updateViewport() {
    viewportWidth.value = window.innerWidth;
    tableMaxHeight.value = Math.max(260, window.innerHeight - 520);
}

onMounted(() => {
    updateViewport();
    window.addEventListener('resize', updateViewport);
});

onUnmounted(() => window.removeEventListener('resize', updateViewport));

const showMethodColumn = computed(() => viewportWidth.value >= 1280);
const showBreakdownColumns = computed(() => viewportWidth.value >= 900);

const rows = computed(() =>
    [...props.payments].sort((a, b) => Number(b.id) - Number(a.id))
);

function methodOf(payment) {
    return LOAN_PAYMENT_METHODS.find((method) => method.value === payment.payment_method) ?? null;
}

function editPayment(payment) {
    emit('edit', payment);
}
</script>

<template>
    <el-table :data="rows" :max-height="tableMaxHeight" class="data-table" style="width: 100%">
        <el-table-column label="Abono" width="105" align="right">
            <template #default="{ row }">
                <span class="money-cell">{{ formatCurrency(row.amount) }}</span>
            </template>
        </el-table-column>

        <el-table-column v-if="showBreakdownColumns" label="Capital" width="105" align="right">
            <template #default="{ row }">
                <span class="payment-cell">{{ formatCurrency(row.capital) }}</span>
            </template>
        </el-table-column>

        <el-table-column v-if="showBreakdownColumns" label="Interés" width="100" align="right">
            <template #default="{ row }">
                <span class="payment-cell payment-cell--interest">{{ formatCurrency(row.interest) }}</span>
            </template>
        </el-table-column>

        <el-table-column label="Restante" width="110" align="right">
            <template #default="{ row }">
                <span class="money-cell" :class="Number(row.remaining) <= 0 ? 'is-settled' : 'is-pending'">
                    {{ formatCurrency(row.remaining) }}
                </span>
            </template>
        </el-table-column>

        <el-table-column label="Fecha" width="112">
            <template #default="{ row }">
                <span class="date-cell">{{ formatCalendarDate(row.date) }}</span>
            </template>
        </el-table-column>

        <el-table-column v-if="showMethodColumn" label="Método" width="150">
            <template #default="{ row }">
                <span v-if="methodOf(row)" class="record-dialog__inline muted-cell">
                    <PaymentIcon :name="methodOf(row).icon" :size="14" />
                    {{ methodOf(row).label }}
                </span>
                <span v-else class="muted-cell">{{ row.payment_method ?? '—' }}</span>
            </template>
        </el-table-column>

        <el-table-column label="Comentarios" min-width="105" show-overflow-tooltip>
            <template #default="{ row }">
                <span :class="row.notes ? 'payment-notes' : 'muted-cell'">{{ row.notes ?? 'Sin comentarios' }}</span>
            </template>
        </el-table-column>

        <el-table-column v-if="!readonly" align="right" width="50" fixed="right">
            <template #default="{ row }">
                <button type="button" class="row-actions-btn" title="Editar abono" @click="editPayment(row)">
                    <el-icon :size="15"><Edit /></el-icon>
                </button>
            </template>
        </el-table-column>

        <template #empty>
            <div class="empty-state py-8">
                <p class="empty-state-title">Aún no hay abonos registrados</p>
                <p class="empty-state-text">
                    {{ readonly
                        ? 'Cuando se registre un abono aparecerá en este listado.'
                        : 'Registra el primer abono para empezar a liquidar este préstamo.' }}
                </p>
            </div>
        </template>
    </el-table>

    <div v-if="!readonly && rows.length" class="payment-table-hint">
        <el-icon :size="13"><Tickets /></el-icon>
        El interés y el capital de cada abono se calculan automáticamente.
    </div>
</template>
