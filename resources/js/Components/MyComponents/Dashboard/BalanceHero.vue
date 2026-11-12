<script setup>
import { computed } from 'vue';
import { useChartTheme } from '@/composables/useChartTheme';

const props = defineProps({
    overview: { type: Object, default: null },
});

const { isDark } = useChartTheme();

const currency = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    minimumFractionDigits: 2,
});

const totals = computed(() => props.overview?.totals ?? {});
const kpis = computed(() => props.overview?.kpis ?? {});
const range = computed(() => props.overview?.range ?? {});

const cumulativeSeries = computed(() => {
    let running = 0;

    return (props.overview?.trend?.net ?? []).map((value) => {
        running += Number(value);
        return Number(running.toFixed(2));
    });
});

const balance = computed(() => Number(props.overview?.balance ?? 0));
const net = computed(() => Number(totals.value.net ?? 0));
const isPositive = computed(() => net.value >= 0);

const sparklineOptions = computed(() => ({
    chart: {
        type: 'area',
        sparkline: { enabled: true },
        toolbar: { show: false },
        animations: { enabled: true, speed: 500 },
        background: 'transparent',
    },
    stroke: { curve: 'smooth', width: 2.5, colors: [isPositive.value ? '#6EE7B7' : '#FCA5A5'] },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.45,
            opacityTo: 0,
            stops: [0, 100],
            colorStops: isPositive.value
                ? [
                      { offset: 0, color: '#6EE7B7', opacity: 0.45 },
                      { offset: 100, color: '#6EE7B7', opacity: 0 },
                  ]
                : [
                      { offset: 0, color: '#FCA5A5', opacity: 0.4 },
                      { offset: 100, color: '#FCA5A5', opacity: 0 },
                  ],
        },
    },
    colors: [isPositive.value ? '#6EE7B7' : '#FCA5A5'],
    tooltip: {
        theme: 'dark',
        x: { show: false },
        y: { formatter: (value) => currency.format(value) },
    },
    grid: { padding: { top: 6, bottom: 0, left: 0, right: 0 } },
}));

function changeClass(value) {
    return Number(value) >= 0 ? 'is-up' : 'is-down';
}
</script>

<template>
    <section v-if="overview" class="dashboard-hero">
        <div class="dashboard-hero__main">
            <p class="dashboard-hero__eyebrow">
                Saldo disponible
                <span class="dashboard-hero__range">{{ range.label }}</span>
            </p>

            <p class="dashboard-hero__balance">
                {{ currency.format(balance) }}
            </p>

            <div class="dashboard-hero__net">
                <span class="dashboard-hero__badge" :class="isPositive ? 'is-up' : 'is-down'">
                    {{ isPositive ? '+' : '' }}{{ currency.format(net) }}
                </span>
                <span class="dashboard-hero__net-label">
                    {{ isPositive ? 'Ahorro neto del periodo' : 'Déficit del periodo' }}
                </span>
            </div>

            <div class="dashboard-hero__metrics">
                <div class="dashboard-hero__metric">
                    <span>Ingresos</span>
                    <strong class="is-income">{{ currency.format(totals.income ?? 0) }}</strong>
                    <em :class="changeClass(totals.income_change)">
                        {{ Number(totals.income_change) >= 0 ? '▲' : '▼' }}
                        {{ Math.abs(Number(totals.income_change ?? 0)) }}%
                    </em>
                </div>
                <div class="dashboard-hero__metric">
                    <span>Gastos</span>
                    <strong class="is-expense">{{ currency.format(totals.outcome ?? 0) }}</strong>
                    <em :class="Number(totals.outcome_change) <= 0 ? 'is-up' : 'is-down'">
                        {{ Number(totals.outcome_change) >= 0 ? '▲' : '▼' }}
                        {{ Math.abs(Number(totals.outcome_change ?? 0)) }}%
                    </em>
                </div>
                <div class="dashboard-hero__metric">
                    <span>Tasa de ahorro</span>
                    <strong>{{ kpis.savings_rate ?? 0 }}%</strong>
                    <em>de tus ingresos</em>
                </div>
            </div>
        </div>

        <div class="dashboard-hero__chart">
            <div class="dashboard-hero__chart-head">
                <span>Flujo acumulado</span>
                <span class="dashboard-hero__chip">{{ range.periodicity }}</span>
            </div>

            <apexchart
                v-if="cumulativeSeries.length"
                type="area"
                height="150"
                width="100%"
                :options="sparklineOptions"
                :series="[{ name: 'Acumulado', data: cumulativeSeries }]"
            />

            <div class="dashboard-hero__chart-foot">
                <span>
                    {{ totals.income_count + totals.outcome_count }} movimientos registrados
                </span>
                <span>vs periodo anterior</span>
            </div>
        </div>
    </section>

    <section v-else class="dashboard-hero dashboard-hero--skeleton">
        <el-skeleton animated>
            <template #template>
                <el-skeleton-item variant="text" style="width: 30%; height: 14px; margin-bottom: 16px;" />
                <el-skeleton-item variant="text" style="width: 55%; height: 36px; margin-bottom: 20px;" />
                <el-skeleton-item variant="rect" style="width: 100%; height: 90px; border-radius: 10px;" />
            </template>
        </el-skeleton>
    </section>
</template>
