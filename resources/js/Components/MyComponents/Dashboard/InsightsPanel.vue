<script setup>
import { computed } from 'vue';
import { Opportunity, Warning, Trophy, TrendCharts } from '@element-plus/icons-vue';

const props = defineProps({
    overview: { type: Object, default: null },
});

const currency = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
});

const totals = computed(() => props.overview?.totals ?? {});
const kpis = computed(() => props.overview?.kpis ?? {});
const topCategory = computed(() => props.overview?.categories?.outcome?.[0] ?? null);
const topConcept = computed(() => props.overview?.top_concepts?.outcome?.[0] ?? null);

const insights = computed(() => {
    const items = [];

    if (topCategory.value) {
        items.push({
            icon: Trophy,
            tone: 'warning',
            title: `Mayor gasto: ${topCategory.value.key}`,
            text: `${currency.format(Number(topCategory.value.total))} · ${topCategory.value.percentage}% de tus gastos.`,
        });
    }

    const outcomeChange = Number(totals.value.outcome_change ?? 0);
    items.push({
        icon: TrendCharts,
        tone: outcomeChange <= 0 ? 'success' : 'danger',
        title: outcomeChange <= 0 ? 'Gastaste menos que antes' : 'Tus gastos aumentaron',
        text: `${Math.abs(outcomeChange)}% ${outcomeChange <= 0 ? 'menos' : 'más'} que el periodo anterior (${currency.format(Number(totals.value.prev_outcome ?? 0))}).`,
    });

    if (topConcept.value) {
        items.push({
            icon: Opportunity,
            tone: 'info',
            title: `Concepto recurrente: ${topConcept.value.concept}`,
            text: `${topConcept.value.count} movimiento(s) por ${currency.format(Number(topConcept.value.total))}.`,
        });
    }

    const savingsRate = Number(kpis.value.savings_rate ?? 0);
    items.push({
        icon: savingsRate >= 20 ? Opportunity : Warning,
        tone: savingsRate >= 20 ? 'success' : savingsRate >= 0 ? 'info' : 'danger',
        title:
            savingsRate >= 20
                ? 'Buen ritmo de ahorro'
                : savingsRate >= 0
                  ? 'Ahorro ajustado'
                  : 'Gastos por encima de ingresos',
        text: `Tasa de ahorro del ${savingsRate}%. Proyección de gasto al cierre: ${currency.format(Number(kpis.value.projected_outcome ?? 0))}.`,
    });

    return items;
});
</script>

<template>
    <div class="dashboard-card dashboard-card--flush">
        <div class="dashboard-card__head">
            <div>
                <h2 class="dashboard-card__title">Insights del periodo</h2>
                <p class="dashboard-card__subtitle">Lo que dicen tus números</p>
            </div>
        </div>

        <ul class="insight-list">
            <li v-for="(insight, index) in insights" :key="index" class="insight-item" :class="`insight-item--${insight.tone}`">
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
