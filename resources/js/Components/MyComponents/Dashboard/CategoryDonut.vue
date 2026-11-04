<script setup>
import { computed, ref } from 'vue';
import { useChartTheme } from '@/composables/useChartTheme';

const props = defineProps({
    categories: { type: Array, default: () => [] },
    total: { type: Number, default: 0 },
});

const { isDark } = useChartTheme();
const activeIndex = ref(null);

const palette = ['#296A6B', '#E8946B', '#3B7DD8', '#D99A2B', '#9AA8A8', '#7C6BD9', '#2F9E5B'];

const currency = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
});

const visible = computed(() => props.categories.slice(0, 6));
const hasData = computed(() => visible.value.length > 0 && props.total > 0);

const series = computed(() => visible.value.map((item) => Number(item.total)));

const options = computed(() => ({
    chart: {
        type: 'donut',
        height: 250,
        fontFamily: 'Inter, sans-serif',
        background: 'transparent',
        animations: { enabled: true, speed: 450 },
    },
    labels: visible.value.map((item) => item.key),
    colors: palette,
    stroke: { width: 2, colors: [isDark.value ? '#1E2424' : '#FFFFFF'] },
    legend: { show: false },
    dataLabels: { enabled: false },
    plotOptions: {
        pie: {
            expandOnClick: false,
            donut: {
                size: '74%',
                labels: {
                    show: true,
                    name: {
                        show: true,
                        fontSize: '11px',
                        color: isDark.value ? '#9AA8A8' : '#728282',
                        offsetY: 18,
                    },
                    value: {
                        show: true,
                        fontSize: '20px',
                        fontFamily: 'JetBrains Mono, monospace',
                        fontWeight: 700,
                        color: isDark.value ? '#EDF1F1' : '#1A1F1F',
                        offsetY: -8,
                        formatter: (value) => currency.format(Number(value)),
                    },
                    total: {
                        show: true,
                        label: 'Gasto total',
                        fontSize: '11px',
                        color: isDark.value ? '#9AA8A8' : '#728282',
                        formatter: () => currency.format(props.total),
                    },
                },
            },
        },
    },
    tooltip: {
        theme: isDark.value ? 'dark' : 'light',
        y: { formatter: (value) => currency.format(Number(value)) },
    },
    states: {
        hover: { filter: { type: 'lighten', value: 0.08 } },
        active: { filter: { type: 'none' } },
    },
}));

function share(item) {
    return props.total > 0 ? (Number(item.total) / props.total) * 100 : 0;
}
</script>

<template>
    <div class="dashboard-card dashboard-card--flush">
        <div class="dashboard-card__head">
            <div>
                <h2 class="dashboard-card__title">Gastos por categoría</h2>
                <p class="dashboard-card__subtitle">Distribución del periodo</p>
            </div>
            <span class="dashboard-chip dashboard-chip--expense">{{ visible.length }} categorías</span>
        </div>

        <template v-if="hasData">
            <apexchart type="donut" height="250" :options="options" :series="series" />

            <ul class="category-legend">
                <li
                    v-for="(item, index) in visible"
                    :key="item.key"
                    class="category-legend__item"
                    :class="{ 'is-active': activeIndex === index }"
                    @mouseenter="activeIndex = index"
                    @mouseleave="activeIndex = null"
                >
                    <span class="category-legend__dot" :style="{ background: palette[index % palette.length] }" />
                    <div class="category-legend__body">
                        <div class="category-legend__row">
                            <span class="category-legend__name">{{ item.key }}</span>
                            <span class="category-legend__value">{{ currency.format(Number(item.total)) }}</span>
                        </div>
                        <div class="category-legend__track">
                            <span
                                class="category-legend__bar"
                                :style="{
                                    width: `${Math.max(share(item), 2)}%`,
                                    background: palette[index % palette.length],
                                }"
                            />
                        </div>
                    </div>
                    <span class="category-legend__pct">{{ item.percentage }}%</span>
                </li>
            </ul>
        </template>

        <div v-else class="empty-state py-10">
            <p class="empty-state-title">Aún no hay gastos</p>
            <p class="empty-state-text">Cuando registres gastos verás aquí su distribución.</p>
        </div>
    </div>
</template>
