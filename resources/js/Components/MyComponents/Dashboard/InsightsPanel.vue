<script setup>
import { computed, ref } from 'vue';
import {
    Coin,
    Histogram,
    Opportunity,
    TrendCharts,
    Trophy,
    Wallet,
    Warning,
} from '@element-plus/icons-vue';

const props = defineProps({
    overview: { type: Object, default: null },
});

const currency = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
});

const scopeOptions = [
    { label: 'Ingresos', value: 'income' },
    { label: 'Gastos', value: 'expense' },
    { label: 'Balance', value: 'balance' },
];

const scope = ref(Number(props.overview?.totals?.income ?? 0) > 0 ? 'income' : 'expense');

const totals = computed(() => props.overview?.totals ?? {});
const kpis = computed(() => props.overview?.kpis ?? {});

function buildMovementInsights({ categories, concepts, total, previous, change, kind }) {
    const topCategory = categories[0] ?? null;
    const topConcept = concepts[0] ?? null;
    const isIncome = kind === 'income';
    const noun = isIncome ? 'ingreso' : 'gasto';
    const plural = isIncome ? 'ingresos' : 'gastos';

    if (!topCategory) {
        return [
            {
                icon: isIncome ? Wallet : Trophy,
                tone: 'info',
                title: `Sin ${plural} en el periodo`,
                text: isIncome
                    ? 'Registra un ingreso para ver tu fuente principal, tu tendencia y tu concentración.'
                    : 'Registra un gasto para ver tu categoría principal, tu tendencia y tus conceptos.',
            },
        ];
    }

    const items = [
        {
            icon: Trophy,
            tone: isIncome ? 'success' : 'warning',
            title: `Mayor ${noun}: ${topCategory.key}`,
            text: `${currency.format(Number(topCategory.total))} · ${topCategory.percentage}% de tus ${plural}.`,
        },
    ];

    const hasPrevious = Number(previous) > 0;
    const isUp = Number(change) >= 0;
    const isGood = isIncome ? isUp : !isUp;

    items.push({
        icon: TrendCharts,
        tone: hasPrevious ? (isGood ? 'success' : 'danger') : 'info',
        title: hasPrevious
            ? `Tus ${plural} ${isUp ? 'crecieron' : 'bajaron'} ${Math.abs(Number(change))}%`
            : `Sin ${plural} en el periodo anterior`,
        text: hasPrevious
            ? `${currency.format(Number(total))} frente a ${currency.format(Number(previous))} del periodo anterior.`
            : `Este periodo sumas ${currency.format(Number(total))}.`,
    });

    if (topConcept) {
        items.push({
            icon: Coin,
            tone: 'info',
            title: `${isIncome ? 'Ingreso' : 'Gasto'} más frecuente: ${topConcept.concept}`,
            text: `${topConcept.count} movimiento(s) por ${currency.format(Number(topConcept.total))}.`,
        });
    }

    if (categories.length > 1) {
        const share = Number(topCategory.percentage);
        const isConcentrated = share >= 70;

        items.push({
            icon: isConcentrated ? Warning : Opportunity,
            tone: isConcentrated ? 'warning' : 'success',
            title: isIncome
                ? isConcentrated
                    ? 'Ingresos concentrados'
                    : 'Ingresos diversificados'
                : isConcentrated
                  ? 'Gasto concentrado'
                  : 'Gasto distribuido',
            text: isConcentrated
                ? `${topCategory.key} representa ${share}% del total; ${
                      isIncome ? 'considera sumar otras fuentes' : 'revisa si puedes repartirlo mejor'
                  }.`
                : `${topCategory.key} es tu mayor categoría con ${share}% del total.`,
        });
    } else {
        items.push({
            icon: Warning,
            tone: 'warning',
            title: isIncome ? 'Un solo origen de ingresos' : 'Todo tu gasto en una categoría',
            text: isIncome
                ? `${topCategory.key} concentra el 100% de tus ingresos; considera sumar otras fuentes.`
                : `${topCategory.key} concentra el 100% de tus gastos del periodo.`,
        });
    }

    return items;
}

const incomeInsights = computed(() =>
    buildMovementInsights({
        categories: props.overview?.categories?.income ?? [],
        concepts: props.overview?.top_concepts?.income ?? [],
        total: totals.value.income ?? 0,
        previous: totals.value.prev_income ?? 0,
        change: totals.value.income_change ?? 0,
        kind: 'income',
    })
);

const expenseInsights = computed(() =>
    buildMovementInsights({
        categories: props.overview?.categories?.outcome ?? [],
        concepts: props.overview?.top_concepts?.outcome ?? [],
        total: totals.value.outcome ?? 0,
        previous: totals.value.prev_outcome ?? 0,
        change: totals.value.outcome_change ?? 0,
        kind: 'expense',
    })
);

const balanceInsights = computed(() => {
    const savingsRate = Number(kpis.value.savings_rate ?? 0);
    const outcomeRatio = Number(kpis.value.outcome_per_income_ratio ?? 0);
    const projectedOutcome = Number(kpis.value.projected_outcome ?? 0);
    const remainingDays = Number(kpis.value.remaining_days ?? 0);

    const items = [
        {
            icon: savingsRate >= 20 ? Opportunity : Warning,
            tone: savingsRate >= 20 ? 'success' : savingsRate >= 0 ? 'info' : 'danger',
            title:
                savingsRate >= 20
                    ? 'Buen ritmo de ahorro'
                    : savingsRate >= 0
                      ? 'Ahorro ajustado'
                      : 'Gastos por encima de ingresos',
            text: `Tasa de ahorro del ${savingsRate}% sobre tus ingresos del periodo.`,
        },
    ];

    if (Number(totals.value.income ?? 0) > 0) {
        items.push({
            icon: Histogram,
            tone: outcomeRatio <= 70 ? 'success' : outcomeRatio <= 100 ? 'warning' : 'danger',
            title: `Destinas ${outcomeRatio}% de tus ingresos a gastos`,
            text:
                outcomeRatio <= 70
                    ? 'Mantienes un margen sano para ahorrar o invertir.'
                    : outcomeRatio <= 100
                      ? 'Tu margen es justo; cuidado con gastos imprevistos.'
                      : 'Estás gastando más de lo que ingresas.',
        });
    }

    items.push({
        icon: Coin,
        tone: 'info',
        title: 'Proyección de gasto al cierre',
        text:
            remainingDays > 0
                ? `${currency.format(projectedOutcome)} estimados; faltan ${remainingDays} día(s) del periodo.`
                : `${currency.format(projectedOutcome)} al cierre del periodo.`,
    });

    return items;
});

const visibleInsights = computed(() => {
    if (scope.value === 'income') return incomeInsights.value;
    if (scope.value === 'expense') return expenseInsights.value;
    return balanceInsights.value;
});
</script>

<template>
    <div class="dashboard-card dashboard-card--flush">
        <div class="dashboard-card__head">
            <div>
                <h2 class="dashboard-card__title">Insights del periodo</h2>
                <p class="dashboard-card__subtitle">
                    Qué categoría pesa más, cómo cambiaste frente al periodo anterior y qué tan sano es tu ahorro.
                </p>
            </div>
        </div>

        <el-segmented
            v-model="scope"
            :options="scopeOptions"
            size="small"
            class="insight-switch"
        />

        <ul class="insight-list">
            <li
                v-for="(insight, index) in visibleInsights"
                :key="`${scope}-${index}`"
                class="insight-item"
                :class="`insight-item--${insight.tone}`"
            >
                <span class="insight-item__icon">
                    <el-icon :size="15"><component :is="insight.icon" /></el-icon>
                </span>
                <div>
                    <p class="insight-item__title">{{ insight.title }}</p>
                    <p class="insight-item__text">{{ insight.text }}</p>
                </div>
            </li>
        </ul>
    </div>
</template>
