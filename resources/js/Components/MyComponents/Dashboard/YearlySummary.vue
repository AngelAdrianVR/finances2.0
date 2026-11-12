<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    periodicity: String,
    period: { type: [String, Date, null], default: null },
});

const loading = ref(true);
const data = ref(null);

// Extract the year from the period when periodicity is 'Anual', otherwise use current year
const selectedYear = computed(() => {
    if (props.periodicity === 'Anual' && props.period) {
        return new Date(props.period).getFullYear();
    }
    return new Date().getFullYear();
});

const avgMonthlyIncome = computed(() => data.value?.avg_monthly_income ?? 0);
const avgMonthlyOutcome = computed(() => data.value?.avg_monthly_outcome ?? 0);
const yearNet = computed(() => data.value?.year_net ?? 0);
const yearTotalIncome = computed(() => data.value?.year_total_income ?? 0);
const yearTotalOutcome = computed(() => data.value?.year_total_outcome ?? 0);
const monthsElapsed = computed(() => data.value?.months_elapsed ?? 1);
const currentMonthIncome = computed(() => data.value?.current_month_income ?? 0);
const currentMonthOutcome = computed(() => data.value?.current_month_outcome ?? 0);

// Savings rate = (income - outcome) / income * 100 for current year
const savingsRate = computed(() => {
    if (yearTotalIncome.value === 0) return 0;
    return ((yearTotalIncome.value - yearTotalOutcome.value) / yearTotalIncome.value * 100).toFixed(1);
});

// Current month vs yearly average comparison
const incomeVsAverage = computed(() => {
    if (avgMonthlyIncome.value === 0) return 0;
    return ((currentMonthIncome.value - avgMonthlyIncome.value) / avgMonthlyIncome.value * 100).toFixed(1);
});

const outcomeVsAverage = computed(() => {
    if (avgMonthlyOutcome.value === 0) return 0;
    return ((currentMonthOutcome.value - avgMonthlyOutcome.value) / avgMonthlyOutcome.value * 100).toFixed(1);
});

function formatMoney(value) {
    return Number(value || 0).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

async function fetchData() {
    try {
        loading.value = true;
        const response = await axios.post(route('dashboard.fetch-yearly-averages'), {
            year: selectedYear.value,
        });
        if (response.status === 200) {
            data.value = response.data;
        }
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
}

watch(selectedYear, () => fetchData());

fetchData();
</script>

<template>
    <!-- Skeleton while loading -->
    <div v-if="loading" class="dashboard-card">
        <el-skeleton animated :count="1">
            <template #template>
                <div style="padding: 24px;">
                    <div class="flex items-center gap-2 mb-5">
                        <el-skeleton-item variant="circle" style="width: 18px; height: 18px;" />
                        <el-skeleton-item variant="text" style="width: 50%; height: 18px;" />
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div style="background: #F7F9F9; border-radius: 8px; padding: 12px;">
                            <el-skeleton-item variant="text" style="width: 70%; height: 12px; margin-bottom: 8px;" />
                            <el-skeleton-item variant="text" style="width: 80%; height: 18px;" />
                        </div>
                        <div style="background: #F7F9F9; border-radius: 8px; padding: 12px;">
                            <el-skeleton-item variant="text" style="width: 70%; height: 12px; margin-bottom: 8px;" />
                            <el-skeleton-item variant="text" style="width: 80%; height: 18px;" />
                        </div>
                    </div>
                    <el-skeleton-item variant="text" style="width: 100%; height: 60px; margin-bottom: 16px;" />
                </div>
            </template>
        </el-skeleton>
    </div>

    <!-- Loaded content -->
    <div v-else class="dashboard-card">
        <div class="dashboard-card__head">
            <div>
                <h2 class="dashboard-card__title">Resumen del año {{ selectedYear }}</h2>
                <p class="dashboard-card__subtitle">Promedios, balance y comparativa del mes en curso</p>
            </div>
            <span class="dashboard-chip">{{ monthsElapsed }} meses</span>
        </div>

        <div class="summary-metrics">
            <div class="summary-metric">
                <p class="summary-metric__label">Promedio mensual de ingresos</p>
                <p class="summary-metric__value summary-metric__value--income">
                    ${{ formatMoney(avgMonthlyIncome) }}
                </p>
            </div>
            <div class="summary-metric">
                <p class="summary-metric__label">Promedio mensual de gastos</p>
                <p class="summary-metric__value summary-metric__value--expense">
                    ${{ formatMoney(avgMonthlyOutcome) }}
                </p>
            </div>
            <div class="summary-metric">
                <p class="summary-metric__label">Balance neto del año</p>
                <p
                    class="summary-metric__value"
                    :class="yearNet >= 0 ? 'summary-metric__value--income' : 'summary-metric__value--expense'"
                >
                    {{ yearNet >= 0 ? '+' : '' }}${{ formatMoney(yearNet) }}
                </p>
            </div>
            <div class="summary-metric">
                <p class="summary-metric__label">Tasa de ahorro</p>
                <p
                    class="summary-metric__value"
                    :class="parseFloat(savingsRate) >= 0 ? 'summary-metric__value--income' : 'summary-metric__value--expense'"
                >
                    {{ savingsRate }}%
                </p>
            </div>
        </div>

        <div class="summary-columns">
            <section class="summary-block">
                <p class="summary-block__title">Este mes vs promedio mensual</p>
                <div class="summary-compare">
                    <div class="summary-compare__item">
                        <div class="summary-compare__head">
                            <span class="summary-compare__label">Ingresos</span>
                            <span
                                class="summary-compare__badge"
                                :class="parseFloat(incomeVsAverage) >= 0 ? 'is-up' : 'is-down'"
                            >
                                {{ parseFloat(incomeVsAverage) >= 0 ? '+' : '' }}{{ incomeVsAverage }}%
                            </span>
                        </div>
                        <p class="summary-compare__value">${{ formatMoney(currentMonthIncome) }}</p>
                        <p class="summary-compare__hint">Promedio ${{ formatMoney(avgMonthlyIncome) }}</p>
                    </div>

                    <div class="summary-compare__item">
                        <div class="summary-compare__head">
                            <span class="summary-compare__label">Gastos</span>
                            <span
                                class="summary-compare__badge"
                                :class="parseFloat(outcomeVsAverage) <= 0 ? 'is-up' : 'is-down'"
                            >
                                {{ parseFloat(outcomeVsAverage) >= 0 ? '+' : '' }}{{ outcomeVsAverage }}%
                            </span>
                        </div>
                        <p class="summary-compare__value">${{ formatMoney(currentMonthOutcome) }}</p>
                        <p class="summary-compare__hint">Promedio ${{ formatMoney(avgMonthlyOutcome) }}</p>
                    </div>
                </div>
            </section>

            <section class="summary-block">
                <p class="summary-block__title">Acumulado del año</p>
                <div class="summary-compare">
                    <div class="summary-compare__item">
                        <span class="summary-compare__label">Total de ingresos</span>
                        <p class="summary-compare__value summary-compare__value--income">
                            ${{ formatMoney(yearTotalIncome) }}
                        </p>
                        <p class="summary-compare__hint">{{ monthsElapsed }} meses transcurridos</p>
                    </div>

                    <div class="summary-compare__item">
                        <span class="summary-compare__label">Total de gastos</span>
                        <p class="summary-compare__value summary-compare__value--expense">
                            ${{ formatMoney(yearTotalOutcome) }}
                        </p>
                        <p class="summary-compare__hint">{{ monthsElapsed }} meses transcurridos</p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
