<script setup>
import { computed, ref } from 'vue';
import { Coin, CreditCard, Top, Wallet } from '@element-plus/icons-vue';
import { useAnimatedNumber } from '@/composables/useAnimatedNumber';

const CHART_WIDTH = 320;
const CHART_HEIGHT = 96;
const BASELINE = 88;
const PLOT_HEIGHT = 70;
const BAR_WIDTH = 9;
const GROUP_STEP = 38;
const GROUP_OFFSET = 8;

const monthly = [
    { month: 'Ene', income: 24800, expense: 14200 },
    { month: 'Feb', income: 27600, expense: 16100 },
    { month: 'Mar', income: 22400, expense: 13800 },
    { month: 'Abr', income: 31200, expense: 18500 },
    { month: 'May', income: 26800, expense: 15100 },
    { month: 'Jun', income: 33400, expense: 19200 },
    { month: 'Jul', income: 29100, expense: 16800 },
    { month: 'Ago', income: 32450, expense: 14180 },
];

const MAX_VALUE = 36000;
const AVERAGE_INCOME = Math.round(
    monthly.reduce((total, item) => total + item.income, 0) / monthly.length,
);

const averageY = BASELINE - (AVERAGE_INCOME / MAX_VALUE) * PLOT_HEIGHT;

const bars = computed(() =>
    monthly.map((item, index) => {
        const x = GROUP_OFFSET + index * GROUP_STEP;
        const incomeHeight = (item.income / MAX_VALUE) * PLOT_HEIGHT;
        const expenseHeight = (item.expense / MAX_VALUE) * PLOT_HEIGHT;

        return {
            ...item,
            index,
            x,
            center: x + (BAR_WIDTH * 2 + 3) / 2,
            incomeHeight,
            expenseHeight,
            incomeY: BASELINE - incomeHeight,
            expenseY: BASELINE - expenseHeight,
        };
    }),
);

const hovered = ref(null);
const tooltipLeft = computed(() => `${((hovered.value === null ? 0 : bars.value[hovered.value].center) / CHART_WIDTH) * 100}%`);

const currency = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    minimumFractionDigits: 2,
});

const compactCurrency = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    maximumFractionDigits: 0,
});

const balance = useAnimatedNumber(48320.5, { duration: 1600 });
const incomes = useAnimatedNumber(32450, { duration: 1500, delay: 120 });
const outcomes = useAnimatedNumber(14180, { duration: 1500, delay: 220 });

const balanceText = computed(() => currency.format(balance.value));
const incomesText = computed(() => currency.format(incomes.value));
const outcomesText = computed(() => currency.format(outcomes.value));

const movements = [
    { icon: Coin, name: 'Sueldo quincenal', amount: '+$15,000.00', tone: 'in' },
    { icon: Wallet, name: 'Alimentos y bebidas', amount: '-$1,250.00', tone: 'out' },
    { icon: CreditCard, name: 'Transporte', amount: '-$520.00', tone: 'out' },
];
</script>

<template>
    <div class="lp-dash" aria-hidden="true">
        <div class="lp-dash__bar">
            <div class="lp-dash__dots">
                <span class="lp-dash__dot lp-dash__dot--a"></span>
                <span class="lp-dash__dot lp-dash__dot--b"></span>
                <span class="lp-dash__dot lp-dash__dot--c"></span>
            </div>
            <span class="lp-dash__title">Panel de finanzas</span>
            <span class="lp-dash__live"><i></i> En vivo</span>
        </div>

        <div class="lp-dash__balance">
            <div>
                <span class="lp-dash__label">Saldo disponible</span>
                <span class="lp-dash__amount">{{ balanceText }}</span>
            </div>
            <span class="lp-trend"><Top /> +12.4%</span>
        </div>

        <div class="lp-dash__chart-wrap">
            <svg
                class="lp-dash__chart"
                :viewBox="`0 0 ${CHART_WIDTH} ${CHART_HEIGHT}`"
                preserveAspectRatio="none"
                role="presentation"
            >
                <defs>
                    <linearGradient id="lp-bar-income" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#b6ffe6" />
                        <stop offset="100%" stop-color="#4fbf9e" />
                    </linearGradient>
                    <linearGradient id="lp-bar-expense" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#f4b696" />
                        <stop offset="100%" stop-color="#c9694a" />
                    </linearGradient>
                </defs>

                <g stroke="rgba(255,255,255,0.08)" stroke-dasharray="3 5">
                    <line x1="0" :y1="averageY" x2="320" :y2="averageY" />
                    <line x1="0" y1="88" x2="320" y2="88" />
                </g>

                <text x="316" :y="averageY - 4" text-anchor="end" class="lp-dash__chart-note">
                    promedio
                </text>

                <g v-for="bar in bars" :key="bar.month">
                    <rect
                        class="lp-dash__bar-shape"
                        :class="{ 'is-active': hovered === bar.index }"
                        :x="bar.x"
                        :y="bar.incomeY"
                        :width="BAR_WIDTH"
                        :height="bar.incomeHeight"
                        rx="4"
                        fill="url(#lp-bar-income)"
                        :style="{ '--i': bar.index }"
                    />
                    <rect
                        class="lp-dash__bar-shape lp-dash__bar-shape--expense"
                        :class="{ 'is-active': hovered === bar.index }"
                        :x="bar.x + BAR_WIDTH + 3"
                        :y="bar.expenseY"
                        :width="BAR_WIDTH"
                        :height="bar.expenseHeight"
                        rx="4"
                        fill="url(#lp-bar-expense)"
                        :style="{ '--i': bar.index }"
                    />
                    <rect
                        class="lp-dash__bar-hit"
                        :x="bar.x - 6"
                        y="8"
                        :width="GROUP_STEP"
                        height="82"
                        @mouseenter="hovered = bar.index"
                        @mouseleave="hovered = null"
                    />
                </g>
            </svg>

            <transition name="lp-tip">
                <div v-if="hovered !== null" class="lp-dash__tip" :style="{ left: tooltipLeft }">
                    <span class="lp-dash__tip-month">{{ bars[hovered].month }}</span>
                    <span class="lp-dash__tip-row">
                        <i class="lp-dash__tip-dot lp-dash__tip-dot--in"></i>
                        {{ compactCurrency.format(bars[hovered].income) }}
                    </span>
                    <span class="lp-dash__tip-row">
                        <i class="lp-dash__tip-dot lp-dash__tip-dot--out"></i>
                        {{ compactCurrency.format(bars[hovered].expense) }}
                    </span>
                </div>
            </transition>
        </div>

        <div class="lp-dash__months">
            <span v-for="(item, index) in monthly" :key="item.month" :class="{ 'is-active': hovered === index }">
                {{ item.month }}
            </span>
        </div>

        <div class="lp-dash__tiles">
            <div class="lp-dash__tile">
                <span class="lp-dash__tile-label">Ingresos del mes</span>
                <span class="lp-dash__tile-value lp-dash__tile-value--in">+{{ incomesText }}</span>
            </div>
            <div class="lp-dash__tile">
                <span class="lp-dash__tile-label">Gastos del mes</span>
                <span class="lp-dash__tile-value lp-dash__tile-value--out">-{{ outcomesText }}</span>
            </div>
        </div>

        <div class="lp-dash__rows">
            <div v-for="movement in movements" :key="movement.name" class="lp-dash__row">
                <span class="lp-dash__row-icon">
                    <component :is="movement.icon" />
                </span>
                <span class="lp-dash__row-name">{{ movement.name }}</span>
                <span
                    class="lp-dash__row-amount"
                    :class="{ 'lp-dash__tile-value--in': movement.tone === 'in' }"
                >
                    {{ movement.amount }}
                </span>
            </div>
        </div>
    </div>
</template>