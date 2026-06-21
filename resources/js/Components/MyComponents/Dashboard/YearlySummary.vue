<script setup>
import { ref, computed, onMounted } from 'vue';
import { TrendCharts, Coin, Wallet } from '@element-plus/icons-vue';
import axios from 'axios';

const loading = ref(true);
const data = ref(null);

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

function formatMoney(val) {
    return val?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

async function fetchData() {
    try {
        loading.value = true;
        const response = await axios.post(route('dashboard.fetch-yearly-averages'));
        if (response.status === 200) {
            data.value = response.data;
        }
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
}

onMounted(() => fetchData());
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
        <div class="flex items-center gap-2 mb-5">
            <el-icon :size="18" color="#296A6B"><TrendCharts /></el-icon>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Resumen del año {{ new Date().getFullYear() }}</h2>
        </div>

        <div class="space-y-4">
            <!-- Monthly averages row -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Promedio mensual de ingresos</p>
                    <p class="font-mono text-lg font-bold text-[#2F9E5B]">
                        ${{ formatMoney(avgMonthlyIncome) }}
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Promedio mensual de gastos</p>
                    <p class="font-mono text-lg font-bold text-[#D64545]">
                        ${{ formatMoney(avgMonthlyOutcome) }}
                    </p>
                </div>
            </div>

            <!-- Net balance for the year -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Balance neto del año</p>
                        <p
                            class="font-mono text-lg font-bold"
                            :class="yearNet >= 0 ? 'text-[#2F9E5B]' : 'text-[#D64545]'"
                        >
                            {{ yearNet >= 0 ? '+' : '' }}${{ formatMoney(yearNet) }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tasa de ahorro</p>
                        <p
                            class="font-mono font-bold"
                            :class="parseFloat(savingsRate) >= 0 ? 'text-[#2F9E5B]' : 'text-[#D64545]'"
                        >
                            {{ savingsRate }}%
                        </p>
                    </div>
                </div>
            </div>

            <!-- Current month vs average -->
            <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Este mes vs promedio mensual</p>
                <div class="grid grid-cols-2 gap-4">
                    <!-- Income comparison -->
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-400 mb-0.5">Ingresos</p>
                        <p class="font-mono text-sm font-semibold text-gray-800 dark:text-gray-200">
                            ${{ formatMoney(currentMonthIncome) }}
                        </p>
                        <div class="flex items-center gap-1 mt-1">
                            <span
                                class="text-xs px-2 py-0.5 rounded-full font-medium"
                                :class="parseFloat(incomeVsAverage) >= 0
                                    ? 'bg-[#EAF7EF] text-[#247A47]'
                                    : 'bg-[#FCEAEA] text-[#B23636]'"
                            >
                                {{ parseFloat(incomeVsAverage) >= 0 ? '+' : '' }}{{ incomeVsAverage }}%
                            </span>
                            <span class="text-xs text-gray-400">vs promedio</span>
                        </div>
                    </div>
                    <!-- Outcome comparison -->
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-400 mb-0.5">Gastos</p>
                        <p class="font-mono text-sm font-semibold text-gray-800 dark:text-gray-200">
                            ${{ formatMoney(currentMonthOutcome) }}
                        </p>
                        <div class="flex items-center gap-1 mt-1">
                            <span
                                class="text-xs px-2 py-0.5 rounded-full font-medium"
                                :class="parseFloat(outcomeVsAverage) <= 0
                                    ? 'bg-[#EAF7EF] text-[#247A47]'
                                    : 'bg-[#FCEAEA] text-[#B23636]'"
                            >
                                {{ parseFloat(outcomeVsAverage) >= 0 ? '+' : '' }}{{ outcomeVsAverage }}%
                            </span>
                            <span class="text-xs text-gray-400">vs promedio</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Totals row -->
            <div class="border-t border-gray-200 dark:border-gray-600 pt-4 grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-400 mb-0.5">Total ingresos del año</p>
                    <p class="font-mono text-sm font-semibold text-gray-800">
                        ${{ formatMoney(yearTotalIncome) }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-400 mb-0.5">Total gastos del año</p>
                    <p class="font-mono text-sm font-semibold text-gray-800 dark:text-gray-200">
                        ${{ formatMoney(yearTotalOutcome) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
