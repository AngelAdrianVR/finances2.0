<script setup>
import { computed } from 'vue';
import { formatCalendarDate } from '@/utils/date';
import {
    isLoanOverdue,
    loanFolio,
    loanInterestLabel,
    loanPersonName,
    loanStatusLabel,
} from '@/utils/loans';

const props = defineProps({
    loan: { type: Object, required: true },
});

const personLabel = computed(() => (props.loan.type === 'Otorgado' ? 'Beneficiario' : 'Prestamista'));

const statusTone = computed(() => {
    if (props.loan.status === 'Pagado') return 'tone-success';

    return isLoanOverdue(props.loan) ? 'tone-danger' : 'tone-warning';
});

function formatDate(value) {
    return value ? formatCalendarDate(value, "d 'de' MMMM, yyyy") : 'Sin fecha';
}
</script>

<template>
    <article class="data-card loan-info">
        <header class="loan-info__head">
            <h2 class="loan-info__title">Información del préstamo</h2>
            <span class="category-tag" :class="statusTone">{{ loanStatusLabel(loan) }}</span>
        </header>

        <dl class="record-dialog__grid loan-info__grid">
            <div class="record-dialog__item">
                <dt>Folio</dt>
                <dd class="record-dialog__value--date">{{ loanFolio(loan) }}</dd>
            </div>

            <div class="record-dialog__item">
                <dt>Tipo</dt>
                <dd>{{ loan.type }}</dd>
            </div>

            <div class="record-dialog__item">
                <dt>{{ personLabel }}</dt>
                <dd>{{ loanPersonName(loan) }}</dd>
            </div>

            <div class="record-dialog__item">
                <dt>Fecha del préstamo</dt>
                <dd class="record-dialog__value--date">{{ formatDate(loan.loan_date) }}</dd>
            </div>

            <div class="record-dialog__item">
                <dt>Vencimiento</dt>
                <dd class="record-dialog__value--date">{{ formatDate(loan.expired_date) }}</dd>
            </div>

            <div class="record-dialog__item">
                <dt>Frecuencia de pago</dt>
                <dd>{{ loan.payment_periodicity ?? 'No definida' }}</dd>
            </div>

            <div class="record-dialog__item">
                <dt>Interés</dt>
                <dd>{{ loanInterestLabel(loan) }}</dd>
            </div>

            <div class="record-dialog__item">
                <dt>Gestión automática</dt>
                <dd>{{ loan.automatic ? 'Activada' : 'Desactivada' }}</dd>
            </div>

            <div class="record-dialog__item record-dialog__item--full">
                <dt>Descripción</dt>
                <dd>
                    <span v-if="loan.description">{{ loan.description }}</span>
                    <span v-else class="record-dialog__empty">Sin descripción</span>
                </dd>
            </div>
        </dl>
    </article>
</template>
