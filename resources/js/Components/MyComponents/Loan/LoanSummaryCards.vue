<script setup>
import { computed } from 'vue';
import { Coin, Clock, TrendCharts, Wallet } from '@element-plus/icons-vue';
import {
    formatCurrency,
    loanPaidProgress,
    loanRemaining,
    loanTotalCapital,
    loanTotalInterest,
    loanTotalPaid,
} from '@/utils/loans';

const props = defineProps({
    loan: { type: Object, required: true },
});

const totalCapital = computed(() => loanTotalCapital(props.loan));
const totalInterest = computed(() => loanTotalInterest(props.loan));
const totalPaid = computed(() => loanTotalPaid(props.loan));
const remaining = computed(() => loanRemaining(props.loan));
const progress = computed(() => loanPaidProgress(props.loan));
const isSettled = computed(() => remaining.value <= 0);

const cards = computed(() => [
    {
        key: 'amount',
        label: 'Monto del préstamo',
        value: formatCurrency(props.loan.amount),
        hint: props.loan.type === 'Otorgado' ? 'Capital prestado' : 'Capital recibido',
        icon: Wallet,
        tone: 'primary',
    },
    {
        key: 'paid',
        label: 'Total abonado',
        value: formatCurrency(totalPaid.value),
        hint: `Capital ${formatCurrency(totalCapital.value)}`,
        icon: Coin,
        tone: 'info',
    },
    {
        key: 'interest',
        label: 'Interés pagado',
        value: formatCurrency(totalInterest.value),
        hint: `${(props.loan.payments?.length ?? 0)} abono(s) registrado(s)`,
        icon: TrendCharts,
        tone: 'accent',
    },
    {
        key: 'remaining',
        label: 'Saldo pendiente',
        value: formatCurrency(remaining.value),
        hint: isSettled.value ? 'Préstamo liquidado' : `${progress.value}% del capital liquidado`,
        icon: Clock,
        tone: isSettled.value ? 'success' : 'warning',
    },
]);
</script>

<template>
    <section class="loan-summary">
        <div class="stat-grid loan-stat-grid">
            <article
                v-for="card in cards"
                :key="card.key"
                class="stat-card"
                :class="`stat-card--${card.tone}`"
            >
                <div class="stat-card__head">
                    <span class="stat-card__icon">
                        <el-icon :size="16"><component :is="card.icon" /></el-icon>
                    </span>
                </div>
                <p class="stat-card__label">{{ card.label }}</p>
                <p class="stat-card__value">{{ card.value }}</p>
                <p class="stat-card__hint">{{ card.hint }}</p>
            </article>
        </div>

        <div class="loan-progress">
            <div class="loan-progress__head">
                <span class="loan-progress__label">Liquidación del capital</span>
                <span class="loan-progress__value" :class="{ 'is-complete': isSettled }">{{ progress }}%</span>
            </div>

            <div class="loan-progress__track">
                <span class="loan-progress__bar" :style="{ width: `${progress}%` }"></span>
            </div>

            <div class="loan-progress__foot">
                <span>Abonado a capital <strong>{{ formatCurrency(totalCapital) }}</strong></span>
                <span>Pendiente <strong>{{ formatCurrency(remaining) }}</strong></span>
            </div>
        </div>
    </section>
</template>
