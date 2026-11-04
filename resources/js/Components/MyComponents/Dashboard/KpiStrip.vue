<script setup>
import { computed } from 'vue';
import { Wallet, TrendCharts, Calendar, Tickets } from '@element-plus/icons-vue';

const props = defineProps({
    overview: { type: Object, default: null },
});

const currency = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    minimumFractionDigits: 2,
});

const totals = computed(() => props.overview?.totals ?? {});
const kpis = computed(() => props.overview?.kpis ?? {});

const tiles = computed(() => [
    {
        key: 'net',
        label: 'Ahorro neto',
        value: currency.format(Number(kpis.value.net ?? 0)),
        hint: `${kpis.value.savings_rate ?? 0}% de tus ingresos`,
        delta: Number(totals.value.net_change ?? 0),
        icon: Wallet,
        tone: Number(kpis.value.net ?? 0) >= 0 ? 'success' : 'danger',
    },
    {
        key: 'daily',
        label: 'Gasto diario promedio',
        value: currency.format(Number(kpis.value.daily_average_outcome ?? 0)),
        hint: `en ${kpis.value.elapsed_days ?? 0} día(s) con datos`,
        icon: TrendCharts,
        tone: 'warning',
    },
    {
        key: 'projected',
        label: 'Proyección al cierre',
        value: currency.format(Number(kpis.value.projected_outcome ?? 0)),
        hint: `faltan ${kpis.value.remaining_days ?? 0} día(s)`,
        icon: Calendar,
        tone: 'info',
    },
    {
        key: 'movements',
        label: 'Movimientos',
        value: String(Number(totals.value.income_count ?? 0) + Number(totals.value.outcome_count ?? 0)),
        hint: `${totals.value.income_count ?? 0} ingresos · ${totals.value.outcome_count ?? 0} gastos`,
        icon: Tickets,
        tone: 'neutral',
    },
]);
</script>

<template>
    <div class="kpi-strip">
        <article
            v-for="tile in tiles"
            :key="tile.key"
            class="kpi-tile"
            :class="`kpi-tile--${tile.tone}`"
        >
            <div class="kpi-tile__icon">
                <el-icon :size="17"><component :is="tile.icon" /></el-icon>
            </div>

            <div class="kpi-tile__body">
                <div class="kpi-tile__head">
                    <p class="kpi-tile__label">{{ tile.label }}</p>
                    <span
                        v-if="tile.delta !== undefined"
                        class="kpi-tile__delta"
                        :class="tile.delta >= 0 ? 'is-up' : 'is-down'"
                    >
                        {{ tile.delta >= 0 ? '▲' : '▼' }} {{ Math.abs(tile.delta) }}%
                    </span>
                </div>
                <p class="kpi-tile__value">{{ tile.value }}</p>
                <p class="kpi-tile__hint">{{ tile.hint }}</p>
            </div>
        </article>
    </div>
</template>
