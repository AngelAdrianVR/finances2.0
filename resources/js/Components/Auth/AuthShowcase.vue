<script setup>
import { computed } from 'vue';

const VIEW_WIDTH = 320;
const VIEW_HEIGHT = 118;
const PAD_X = 5;
const PAD_TOP = 12;
const PAD_BOTTOM = 14;
const MAX_VALUE = 100;

const months = ['Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct'];
const incomeSeries = [42, 52, 45, 63, 58, 74, 69, 88];
const expenseSeries = [28, 33, 26, 37, 31, 40, 34, 45];

function toPoints(series) {
    const stepX = (VIEW_WIDTH - PAD_X * 2) / (series.length - 1);
    const usableHeight = VIEW_HEIGHT - PAD_TOP - PAD_BOTTOM;

    return series.map((value, index) => [
        Number((PAD_X + stepX * index).toFixed(2)),
        Number((PAD_TOP + (1 - value / MAX_VALUE) * usableHeight).toFixed(2)),
    ]);
}

function toSmoothPath(points) {
    return points.reduce((path, point, index) => {
        if (index === 0) {
            return `M ${point[0]} ${point[1]}`;
        }

        const previous = points[index - 1];
        const controlX = (previous[0] + point[0]) / 2;

        return `${path} C ${controlX} ${previous[1]}, ${controlX} ${point[1]}, ${point[0]} ${point[1]}`;
    }, '');
}

function toAreaPath(points) {
    const baseline = VIEW_HEIGHT - PAD_BOTTOM;
    const last = points[points.length - 1];

    return `${toSmoothPath(points)} L ${last[0]} ${baseline} L ${points[0][0]} ${baseline} Z`;
}

const incomePoints = computed(() => toPoints(incomeSeries));
const expensePoints = computed(() => toPoints(expenseSeries));

const incomePath = computed(() => toSmoothPath(incomePoints.value));
const expensePath = computed(() => toSmoothPath(expensePoints.value));
const incomeAreaPath = computed(() => toAreaPath(incomePoints.value));
const lastIncomePoint = computed(() => incomePoints.value[incomePoints.value.length - 1]);
</script>

<template>
    <div class="auth-showcase__inner">
        <img class="auth-showcase__logo" src="/images/white_logo.png" alt="Finanzas" />

        <div>
            <span class="auth-badge"><span class="auth-badge__dot"></span>Finanzas personales</span>
            <h1 class="auth-showcase__title mt-5">Tus finanzas, claras y bajo control</h1>
            <p class="auth-showcase__subtitle mt-3">
                Registra ingresos, gastos, préstamos e inversiones en un solo lugar y entiende
                hacia dónde va tu dinero cada mes.
            </p>
        </div>

        <div class="auth-glass">
            <div class="auth-glass__head">
                <div>
                    <span class="auth-label">Balance disponible</span>
                    <span class="auth-amount auth-amount--xl">$48,320.50</span>
                </div>
                <span class="auth-trend">+12.4%</span>
            </div>

            <svg
                class="auth-chart"
                :viewBox="`0 0 ${VIEW_WIDTH} ${VIEW_HEIGHT}`"
                role="img"
                aria-label="Gráfica de ingresos y gastos de los últimos ocho meses"
            >
                <defs>
                    <linearGradient id="authIncomeArea" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.28" />
                        <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0" />
                    </linearGradient>
                </defs>

                <path :d="incomeAreaPath" fill="url(#authIncomeArea)" />
                <path class="auth-chart__line auth-chart__line--expense" :d="expensePath" />
                <path class="auth-chart__line auth-chart__line--income" :d="incomePath" />
                <circle
                    class="auth-chart__dot"
                    :cx="lastIncomePoint[0]"
                    :cy="lastIncomePoint[1]"
                    r="2.8"
                />
            </svg>

            <div class="auth-chart__months">
                <span v-for="month in months" :key="month">{{ month }}</span>
            </div>

            <div class="auth-legend">
                <span class="auth-legend__item">
                    <span class="auth-legend__swatch auth-legend__swatch--income"></span>
                    Ingresos
                </span>
                <span class="auth-legend__item">
                    <span class="auth-legend__swatch auth-legend__swatch--expense"></span>
                    Gastos
                </span>
            </div>
        </div>

        <div class="auth-stats">
            <div class="auth-stat">
                <span class="auth-stat__label">
                    <span class="auth-legend__swatch" style="background: #7BE2A4"></span>
                    Ingresos de octubre
                </span>
                <span class="auth-amount auth-stat__value auth-stat__value--income">+$32,450.00</span>
            </div>
            <div class="auth-stat">
                <span class="auth-stat__label">
                    <span class="auth-legend__swatch" style="background: #F0A17C"></span>
                    Gastos de octubre
                </span>
                <span class="auth-amount auth-stat__value auth-stat__value--expense">-$14,180.00</span>
            </div>
        </div>
    </div>
</template>
