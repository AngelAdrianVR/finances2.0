<script setup>
import { computed, ref } from 'vue';
import { useAnimatedNumber } from '@/composables/useAnimatedNumber';

const options = [
    {
        key: 'received',
        label: 'Recibidos',
        borrower: 'Banco del Bajío',
        pending: 3250,
        paid: 9750,
        total: 13000,
        rate: 75,
        next: '15 nov',
        installment: 1450,
    },
    {
        key: 'given',
        label: 'Otorgados',
        borrower: 'Cristina Flores',
        pending: 11250,
        paid: 13750,
        total: 25000,
        rate: 55,
        next: '20 nov',
        installment: 2100,
    },
];

const activeKey = ref('received');
const active = computed(() => options.find((option) => option.key === activeKey.value));

const pending = useAnimatedNumber(computed(() => active.value.pending), { duration: 700 });
const paid = useAnimatedNumber(computed(() => active.value.paid), { duration: 700 });
const total = useAnimatedNumber(computed(() => active.value.total), { duration: 700 });
const rate = useAnimatedNumber(computed(() => active.value.rate), { duration: 850 });

const currency = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    minimumFractionDigits: 2,
});
</script>

<template>
    <div class="lp-viz lp-loansviz" aria-hidden="true">
        <div class="lp-viz__head">
            <span class="lp-viz__label">Préstamos</span>
            <div class="lp-loansviz__switch">
                <span
                    v-for="option in options"
                    :key="option.key"
                    class="lp-loansviz__switch-item"
                    :class="{ 'is-active': option.key === activeKey }"
                    @click="activeKey = option.key"
                >
                    {{ option.label }}
                </span>
            </div>
        </div>

        <div class="lp-loansviz__main">
            <span class="lp-loansviz__caption">Saldo pendiente</span>
            <strong class="lp-viz__amount">{{ currency.format(pending) }}</strong>
            <span class="lp-loansviz__borrower">{{ active.borrower }}</span>
        </div>

        <div class="lp-loansviz__track">
            <span class="lp-loansviz__fill" :style="{ width: `${rate}%` }"></span>
        </div>

        <div class="lp-loansviz__legend">
            <span><i class="lp-loansviz__dot lp-loansviz__dot--done"></i>{{ Math.round(rate) }}% pagado</span>
            <span><i class="lp-loansviz__dot lp-loansviz__dot--pending"></i>{{ Math.round(100 - rate) }}% pendiente</span>
        </div>

        <div class="lp-loansviz__rows">
            <div class="lp-loansviz__row">
                <span>Total pagado</span>
                <strong>{{ currency.format(paid) }}</strong>
            </div>
            <div class="lp-loansviz__row">
                <span>Total del préstamo</span>
                <strong>{{ currency.format(total) }}</strong>
            </div>
        </div>

        <div class="lp-loansviz__foot">
            <span class="lp-loansviz__badge">Próximo abono · {{ active.next }}</span>
            <span class="lp-loansviz__installment">{{ currency.format(active.installment) }}</span>
        </div>
    </div>
</template>