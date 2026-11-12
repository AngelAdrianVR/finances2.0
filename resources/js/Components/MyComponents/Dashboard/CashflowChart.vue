<script setup>
import { computed, ref } from 'vue';
import { TrendCharts, Histogram } from '@element-plus/icons-vue';
import { useChartTheme } from '@/composables/useChartTheme';

const props = defineProps({
    trend: { type: Object, default: () => ({ labels: [], income: [], outcome: [], net: [] }) },
    periodicity: { type: String, default: 'Mensual' },
});

const { isDark } = useChartTheme();
const mode = ref('area');

const currency = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
});

const incomeColor = computed(() => (isDark.value ? '#3BB970' : '#2F9E5B'));
const outcomeColor = computed(() => (isDark.value ? '#F05A5A' : '#D64545'));
const netColor = computed(() => (isDark.value ? '#5B9BE8' : '#3B7DD8'));

const hasData = computed(
    () => (props.trend?.income ?? []).some((value) => value > 0) || (props.trend?.outcome ?? []).some((value) => value > 0)
);

const series = computed(() => [
    { name: 'Ingresos', type: mode.value, data: props.trend?.income ?? [] },
    { name: 'Gastos', type: mode.value, data: props.trend?.outcome ?? [] },
]);

const isDense = computed(() => (props.trend?.labels ?? []).length > 12);

const labels = computed(() => props.trend?.labels ?? []);

const labelStep = computed(() => Math.max(1, Math.ceil(labels.value.length / 8)));

const displayLabels = computed(() =>
    labels.value.map((label, index) => (index % labelStep.value === 0 ? label : ''))
);

const options = computed(() => {
    const gridColor = isDark.value ? 'rgba(255, 255, 255, 0.08)' : '#EDF1F1';
    const labelColor = isDark.value ? '#9AA8A8' : '#728282';

    return {
        chart: {
            type: mode.value,
            height: 300,
            toolbar: { show: false },
            zoom: { enabled: false },
            fontFamily: 'Inter, sans-serif',
            background: 'transparent',
            animations: { enabled: true, speed: 450, animateGradually: { enabled: false } },
        },
        colors: [incomeColor.value, outcomeColor.value],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: mode.value === 'area' ? 2.5 : 0 },
        fill:
            mode.value === 'area'
                ? {
                      type: 'gradient',
                      gradient: {
                          shadeIntensity: 1,
                          opacityFrom: 0.35,
                          opacityTo: 0.02,
                          stops: [0, 90, 100],
                      },
                  }
                : { opacity: 1 },
        plotOptions: {
            bar: {
                columnWidth: '55%',
                borderRadius: 4,
                borderRadiusApplication: 'end',
            },
        },
        markers: { size: 0, hover: { size: 5 } },
        xaxis: {
            categories: displayLabels.value,
            labels: {
                style: { colors: labelColor, fontSize: '11px' },
                rotate: isDense.value ? -45 : 0,
                rotateAlways: isDense.value,
                hideOverlappingLabels: true,
            },
            axisBorder: { show: false },
            axisTicks: { show: false },
            tooltip: { enabled: false },
        },
        yaxis: {
            labels: {
                style: { colors: labelColor, fontSize: '11px' },
                formatter: (value) => compactNumber(value),
            },
        },
        grid: {
            borderColor: gridColor,
            strokeDashArray: 4,
            padding: { left: 4, right: 8, top: 0 },
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            fontSize: '12px',
            fontWeight: 600,
            labels: { colors: labelColor },
            markers: { radius: 3, width: 10, height: 10 },
            itemMargin: { horizontal: 10 },
        },
        tooltip: {
            theme: isDark.value ? 'dark' : 'light',
            shared: true,
            intersect: false,
            x: {
                formatter: (value, options) => labels.value[options?.dataPointIndex] ?? value,
            },
            y: { formatter: (value) => currency.format(value) },
        },
        states: {
            hover: { filter: { type: 'lighten', value: 0.05 } },
            active: { filter: { type: 'none' } },
        },
    };
});

function compactNumber(value) {
    const number = Number(value ?? 0);
    if (Math.abs(number) >= 1_000_000) return `$${(number / 1_000_000).toFixed(1)}M`;
    if (Math.abs(number) >= 1_000) return `$${(number / 1_000).toFixed(1)}k`;
    return `$${number}`;
}
</script>

<template>
    <div class="dashboard-card dashboard-card--flush">
        <div class="dashboard-card__head">
            <div>
                <h2 class="dashboard-card__title">Flujo de efectivo</h2>
                <p class="dashboard-card__subtitle">
                    Ingresos y gastos · {{ periodicity }}
                </p>
            </div>

            <div class="dashboard-mode-switch">
                <button
                    type="button"
                    :class="{ 'is-active': mode === 'area' }"
                    title="Ver como área"
                    @click="mode = 'area'"
                >
                    <el-icon :size="14"><TrendCharts /></el-icon>
                </button>
                <button
                    type="button"
                    :class="{ 'is-active': mode === 'bar' }"
                    title="Ver como barras"
                    @click="mode = 'bar'"
                >
                    <el-icon :size="14"><Histogram /></el-icon>
                </button>
            </div>
        </div>

        <apexchart
            v-if="hasData"
            :type="mode"
            height="300"
            :options="options"
            :series="series"
        />

        <div v-else class="empty-state py-10">
            <p class="empty-state-title">Sin movimientos en este periodo</p>
            <p class="empty-state-text">Registra un ingreso o gasto para ver el flujo de efectivo.</p>
        </div>
    </div>
</template>
