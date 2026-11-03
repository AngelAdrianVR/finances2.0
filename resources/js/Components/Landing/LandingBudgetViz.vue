<script setup>
import { computed, ref } from 'vue';

const budgets = [
    { label: 'Alimentos y bebidas', spent: 5240, limit: 8000, color: '#296A6B' },
    { label: 'Transporte', spent: 2180, limit: 3500, color: '#5B9C9D' },
    { label: 'Salud y bienestar', spent: 3410, limit: 4000, color: '#2F9E5B' },
    { label: 'Entretenimiento', spent: 3350, limit: 3000, color: '#E8946B' },
];

const totalSpent = budgets.reduce((sum, item) => sum + item.spent, 0);
const totalLimit = budgets.reduce((sum, item) => sum + item.limit, 0);
const usedPercent = Math.round((totalSpent / totalLimit) * 100);

const rows = computed(() =>
    budgets.map((item) => {
        const ratio = item.spent / item.limit;
        const over = item.spent > item.limit;

        return {
            ...item,
            over,
            width: Math.min(ratio, 1) * 100,
            remaining: Math.abs(item.limit - item.spent),
        };
    }),
);

const activeIndex = ref(0);
const active = computed(() => rows.value[activeIndex.value]);

const currency = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    maximumFractionDigits: 0,
});
</script>

<template>
    <div class="lp-viz lp-budgetviz" aria-hidden="true">
        <div class="lp-viz__head">
            <span class="lp-viz__label">Presupuesto del mes</span>
            <span class="lp-viz__pill">{{ usedPercent }}% usado</span>
        </div>

        <div class="lp-budgetviz__summary">
            <div>
                <span class="lp-budgetviz__summary-label">Gastado</span>
                <strong class="lp-budgetviz__summary-value">{{ currency.format(totalSpent) }}</strong>
            </div>
            <div>
                <span class="lp-budgetviz__summary-label">Presupuesto</span>
                <strong class="lp-budgetviz__summary-total">{{ currency.format(totalLimit) }}</strong>
            </div>
        </div>

        <ul class="lp-budgetviz__list">
            <li
                v-for="(row, index) in rows"
                :key="row.label"
                class="lp-budgetviz__row"
                :class="{ 'is-active': index === activeIndex, 'is-over': row.over }"
                @mouseenter="activeIndex = index"
            >
                <div class="lp-budgetviz__row-head">
                    <span class="lp-budgetviz__row-name">{{ row.label }}</span>
                    <span class="lp-budgetviz__row-amount">{{ currency.format(row.spent) }}</span>
                </div>
                <div class="lp-budgetviz__track">
                    <span
                        class="lp-budgetviz__fill"
                        :style="{ width: `${row.width}%`, background: row.over ? '#D64545' : row.color }"
                    ></span>
                </div>
            </li>
        </ul>

        <div class="lp-budgetviz__foot" :class="{ 'is-over': active.over }">
            <span>{{ active.over ? 'Excedido' : 'Disponible en ' + active.label.split(' ')[0] }}</span>
            <strong>{{ currency.format(active.remaining) }}</strong>
        </div>
    </div>
</template>