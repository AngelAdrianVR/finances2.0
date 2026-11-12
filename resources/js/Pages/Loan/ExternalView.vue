<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import LoanSummaryCards from '@/Components/MyComponents/Loan/LoanSummaryCards.vue';
import LoanInfoPanel from '@/Components/MyComponents/Loan/LoanInfoPanel.vue';
import PaymentsTable from '@/Components/MyComponents/Loan/PaymentsTable.vue';
import { formatCalendarDate } from '@/utils/date';
import {
    formatCurrency,
    loanFolio,
    loanPersonName,
    loanRemaining,
    loanStatusLabel,
    loanTotalPaid,
} from '@/utils/loans';

const props = defineProps({
    loan: { type: Object, required: true },
});

const personName = computed(() => loanPersonName(props.loan));
const folio = computed(() => loanFolio(props.loan));
const statusLabel = computed(() => loanStatusLabel(props.loan));
const remaining = computed(() => loanRemaining(props.loan));
const totalPaid = computed(() => loanTotalPaid(props.loan));
const paymentCount = computed(() => props.loan.payments?.length ?? 0);

const loanDateLabel = computed(() => {
    const text = formatCalendarDate(props.loan.loan_date, "d 'de' MMMM, yyyy");

    return text ? `Registrado el ${text}` : 'Sin fecha de registro';
});
</script>

<template>
    <Head title="Detalle del préstamo" />

    <main class="external-shell">
        <header class="external-header">
            <p class="external-header__eyebrow">Finanzas · Préstamos</p>
            <h1 class="external-header__title">{{ personName }}</h1>

            <p class="external-header__meta">
                <span class="category-tag tone-primary">{{ folio }}</span>
                <span class="category-tag" :class="loan.status === 'Pagado' ? 'tone-success' : 'tone-warning'">
                    {{ statusLabel }}
                </span>
                <span>{{ loanDateLabel }}</span>
            </p>

            <p class="external-header__note">
                Vista informativa de solo lectura. Puedes consultar el avance del préstamo, pero no editarlo ni
                registrar abonos.
            </p>
        </header>

        <LoanSummaryCards :loan="loan" />

        <LoanInfoPanel :loan="loan" />

        <section class="data-card">
            <header class="data-card__toolbar">
                <div class="data-card__filters loan-payments__title">
                    <h2 class="loan-info__title">Abonos</h2>
                    <span class="tab-count">{{ paymentCount }}</span>
                </div>
            </header>

            <PaymentsTable :payments="loan.payments" readonly />

            <footer class="data-card__footer loan-payments__footer">
                <span class="loan-table-meta">
                    Total abonado <strong>{{ formatCurrency(totalPaid) }}</strong>
                </span>
                <span class="loan-table-meta" :class="remaining <= 0 ? 'is-settled' : 'is-pending'">
                    Saldo pendiente <strong>{{ formatCurrency(remaining) }}</strong>
                </span>
            </footer>
        </section>

        <footer class="external-footer">
            Enlace compartido desde Finanzas · Solo lectura
        </footer>
    </main>
</template>
