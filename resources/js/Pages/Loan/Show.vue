<script setup>
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { ArrowLeft, Close, CopyDocument, Delete, Edit, Plus, Share, View } from '@element-plus/icons-vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import AppLayout from '@/Layouts/AppLayout.vue';
import LoanSummaryCards from '@/Components/MyComponents/Loan/LoanSummaryCards.vue';
import LoanInfoPanel from '@/Components/MyComponents/Loan/LoanInfoPanel.vue';
import PaymentsTable from '@/Components/MyComponents/Loan/PaymentsTable.vue';
import PaymentFormDialog from '@/Components/MyComponents/Loan/PaymentFormDialog.vue';
import { formatCalendarDate } from '@/utils/date';
import {
    formatCurrency,
    isLoanOverdue,
    loanFolio,
    loanPersonName,
    loanRemaining,
    loanStatusLabel,
    loanTotalPaid,
} from '@/utils/loans';

const props = defineProps({
    loan: { type: Object, required: true },
    loans: { type: Array, default: () => [] },
});

const loanSelected = ref(props.loan.id);
const showPaymentDialog = ref(false);
const selectedPayment = ref(null);
const showShareDialog = ref(false);

const personName = computed(() => loanPersonName(props.loan));
const folio = computed(() => loanFolio(props.loan));
const remaining = computed(() => loanRemaining(props.loan));
const totalPaid = computed(() => loanTotalPaid(props.loan));
const statusLabel = computed(() => loanStatusLabel(props.loan));

const statusTone = computed(() => {
    if (props.loan.status === 'Pagado') return 'tone-success';

    return isLoanOverdue(props.loan) ? 'tone-danger' : 'tone-warning';
});

const paymentCount = computed(() => props.loan.payments?.length ?? 0);

const loanDateLabel = computed(() => {
    const text = formatCalendarDate(props.loan.loan_date, "d 'de' MMMM, yyyy");

    return text ? `Registrado el ${text}` : 'Sin fecha de registro';
});

const typeTab = computed(() => (props.loan.type === 'Otorgado' ? 1 : 2));

const loanOptions = computed(() =>
    (props.loans ?? []).map((item) => ({
        value: item.id,
        label: `${loanFolio(item)} · ${loanPersonName(item)}`,
        amount: formatCurrency(item.amount),
    }))
);

const externalUrl = computed(() => route('loans.external-view', btoa(String(props.loan.id))));

function goBack() {
    router.get(route('loans.index', { currentTab: typeTab.value }));
}

function changeLoan(id) {
    if (!id) return;

    router.get(route('loans.show', id));
}

function openNewPayment() {
    selectedPayment.value = null;
    showPaymentDialog.value = true;
}

function openEditPayment(payment) {
    selectedPayment.value = payment;
    showPaymentDialog.value = true;
}

function shareByWhatsapp() {
    const message = `Hola, te comparto el detalle de este préstamo: ${externalUrl.value}`;

    window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(message)}`, '_blank');
}

async function copyToClipboard() {
    try {
        await navigator.clipboard.writeText(externalUrl.value);
        ElMessage.success('Enlace copiado al portapapeles.');
    } catch (error) {
        console.error(error);
        ElMessage.error('No se pudo copiar el enlace.');
    }
}

async function deleteItem() {
    try {
        await ElMessageBox.confirm(
            '¿Estás seguro que deseas eliminar este préstamo? También se eliminarán sus abonos.',
            'Confirmar eliminación',
            { confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar', type: 'warning' }
        );

        router.delete(route('loans.destroy', props.loan.id), {
            onSuccess: () => ElMessage.success('Préstamo eliminado correctamente.'),
        });
    } catch (error) {
        if (error !== 'cancel') {
            ElMessage.error('Algo salió mal.');
            console.error(error);
        }
    }
}
</script>

<template>
    <AppLayout title="Detalle del préstamo">
        <main class="page-shell">
            <!-- Heading -->
            <header class="page-heading">
                <div class="page-heading__text loan-heading">
                    <button type="button" class="form-back" title="Volver a préstamos" @click="goBack">
                        <el-icon :size="16"><ArrowLeft /></el-icon>
                    </button>

                    <div class="loan-heading__text">
                        <p class="page-heading__eyebrow">Préstamos · {{ loan.type }}</p>
                        <h1 class="page-heading__title">{{ personName }}</h1>
                        <p class="page-heading__subtitle loan-heading__meta">
                            <span class="category-tag tone-primary">{{ folio }}</span>
                            <span class="category-tag" :class="statusTone">{{ statusLabel }}</span>
                            <span>{{ loanDateLabel }}</span>
                        </p>
                    </div>
                </div>

                <div class="page-heading__actions loan-heading__actions">
                    <el-select
                        v-model="loanSelected"
                        filterable
                        placeholder="Cambiar de préstamo"
                        class="loan-selector"
                        @change="changeLoan"
                    >
                        <el-option
                            v-for="option in loanOptions"
                            :key="option.value"
                            :label="option.label"
                            :value="option.value"
                        >
                            <span class="loan-option">
                                <span>{{ option.label }}</span>
                                <span class="loan-option__amount">{{ option.amount }}</span>
                            </span>
                        </el-option>
                    </el-select>

                    <el-button :icon="Edit" @click="router.get(route('loans.edit', loan.id))">
                        Editar
                    </el-button>

                    <el-tooltip content="Compartir enlace" placement="top">
                        <el-button :icon="Share" @click="showShareDialog = true" />
                    </el-tooltip>

                    <el-tooltip content="Eliminar préstamo" placement="top">
                        <el-button type="danger" plain :icon="Delete" @click="deleteItem" />
                    </el-tooltip>
                </div>
            </header>

            <!-- Summary -->
            <LoanSummaryCards :loan="loan" />

            <!-- Detail grid -->
            <div class="loan-detail-grid">
                <LoanInfoPanel :loan="loan" />

                <section class="data-card loan-payments">
                    <header class="data-card__toolbar">
                        <div class="data-card__filters loan-payments__title">
                            <h2 class="loan-info__title">Abonos</h2>
                            <span class="tab-count">{{ paymentCount }}</span>
                        </div>

                        <div class="data-card__meta">
                            <el-button type="primary" :icon="Plus" @click="openNewPayment">
                                Registrar abono
                            </el-button>
                        </div>
                    </header>

                    <PaymentsTable :payments="loan.payments" @edit="openEditPayment" />

                    <footer class="data-card__footer loan-payments__footer">
                        <span class="loan-table-meta">
                            Total abonado <strong>{{ formatCurrency(totalPaid) }}</strong>
                        </span>
                        <span class="loan-table-meta" :class="remaining <= 0 ? 'is-settled' : 'is-pending'">
                            Saldo pendiente <strong>{{ formatCurrency(remaining) }}</strong>
                        </span>
                    </footer>
                </section>
            </div>
        </main>

        <!-- Payment dialog -->
        <PaymentFormDialog v-model="showPaymentDialog" :loan="loan" :payment="selectedPayment" />

        <!-- Share dialog -->
        <el-dialog
            v-model="showShareDialog"
            width="480px"
            align-center
            append-to-body
            :show-close="false"
            class="record-dialog share-dialog"
        >
            <template #header>
                <div class="record-dialog__header">
                    <span class="record-dialog__eyebrow">Compartir préstamo</span>
                    <button type="button" class="record-dialog__close" aria-label="Cerrar" @click="showShareDialog = false">
                        <el-icon :size="15"><Close /></el-icon>
                    </button>
                </div>
            </template>

            <p class="share-dialog__text">
                El enlace muestra el estado completo del préstamo en modo de solo lectura. No permite editar, eliminar
                ni registrar abonos.
            </p>

            <div class="share-dialog__link">
                <el-icon :size="14"><Share /></el-icon>
                <span class="share-dialog__url">{{ externalUrl }}</span>
            </div>

            <div class="share-dialog__actions">
                <button type="button" class="share-dialog__action" @click="shareByWhatsapp">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#25D366" class="share-dialog__icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                    </svg>
                    <span>WhatsApp</span>
                </button>

                <button type="button" class="share-dialog__action" @click="copyToClipboard">
                    <el-icon :size="22" class="share-dialog__icon share-dialog__icon--brand"><CopyDocument /></el-icon>
                    <span>Copiar enlace</span>
                </button>

                <a :href="externalUrl" target="_blank" rel="noopener" class="share-dialog__action">
                    <el-icon :size="22" class="share-dialog__icon share-dialog__icon--brand"><View /></el-icon>
                    <span>Vista previa</span>
                </a>
            </div>

            <template #footer>
                <div class="record-dialog__footer">
                    <el-button @click="showShareDialog = false">Cerrar</el-button>
                </div>
            </template>
        </el-dialog>
    </AppLayout>
</template>
