<script setup>
import { ref, computed, watch } from 'vue';
import { Top, Bottom } from '@element-plus/icons-vue';
import axios from 'axios';

const props = defineProps({
    periodicity: String,
    period: { type: [String, Date, null], default: null },
});

const loading = ref(true);
const currentIncome = ref(0);
const prevIncome = ref(0);
const currentOutcome = ref(0);
const prevOutcome = ref(0);

const incomePercentage = computed(() => {
    if (prevIncome.value === 0 && currentIncome.value > 0) return 100;
    const pct = ((currentIncome.value - prevIncome.value) / prevIncome.value) * 100;
    return pct ? Math.abs(pct).toFixed(1) : 0;
});

const outcomePercentage = computed(() => {
    if (prevOutcome.value === 0 && currentOutcome.value > 0) return 100;
    const pct = ((currentOutcome.value - prevOutcome.value) / prevOutcome.value) * 100;
    return pct ? Math.abs(pct).toFixed(1) : 0;
});

const incomeUp = computed(() => currentIncome.value >= prevIncome.value);
const outcomeDown = computed(() => currentOutcome.value <= prevOutcome.value);

function formatMoney(val) {
    return val?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

async function fetchDataComparison() {
    try {
        loading.value = true;
        const response = await axios.post(route('dashboard.fetch-data-comparison'), {
            periodicity: props.periodicity,
            period: props.period,
        });
        if (response.status === 200) {
            currentIncome.value = response.data.current_income;
            prevIncome.value = response.data.prev_income;
            currentOutcome.value = response.data.current_outcome;
            prevOutcome.value = response.data.prev_outcome;
        }
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
}

watch(() => [props.periodicity, props.period], () => fetchDataComparison());

fetchDataComparison();
</script>

<template>
    <!-- Skeleton while loading -->
    <div v-if="loading" class="total-balance-card p-6">
        <el-skeleton animated :count="1">
            <template #template>
                <el-skeleton-item variant="h3" style="width: 50%; height: 20px; margin-bottom: 20px; background: rgba(255,255,255,0.3);" />
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div style="background: rgba(255,255,255,0.15); border-radius: 8px; padding: 14px 18px;">
                        <el-skeleton-item variant="text" style="width: 40%; height: 13px; margin-bottom: 8px; background: rgba(255,255,255,0.3);" />
                        <el-skeleton-item variant="text" style="width: 60%; height: 24px; background: rgba(255,255,255,0.3);" />
                    </div>
                    <div style="background: rgba(255,255,255,0.15); border-radius: 8px; padding: 14px 18px;">
                        <el-skeleton-item variant="text" style="width: 40%; height: 13px; margin-bottom: 8px; background: rgba(255,255,255,0.3);" />
                        <el-skeleton-item variant="text" style="width: 60%; height: 24px; background: rgba(255,255,255,0.3);" />
                    </div>
                </div>
            </template>
        </el-skeleton>
    </div>

    <!-- Loaded content -->
    <div v-else class="total-balance-card p-6">
        <h2 class="text-lg font-semibold mb-5 text-white/90">
            Balance total {{ periodicity }} (actual vs anterior)
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Income -->
            <div class="metric-box">
                <p class="metric-label">Ingresaste</p>
                <p class="metric-value">$ {{ formatMoney(currentIncome) }}</p>
                <div class="flex items-center space-x-2 mt-2">
                    <span
                        class="percentage-badge"
                        :class="incomeUp ? 'positive' : 'negative'"
                    >
                        <el-icon :size="12">
                            <Top v-if="incomeUp" />
                            <Bottom v-else />
                        </el-icon>
                        {{ incomePercentage }}%
                    </span>
                    <span class="text-xs text-white/60">
                        vs anterior (${{ formatMoney(prevIncome) }})
                    </span>
                </div>
            </div>

            <!-- Expense -->
            <div class="metric-box">
                <p class="metric-label">Gastaste</p>
                <p class="metric-value">$ {{ formatMoney(currentOutcome) }}</p>
                <div class="flex items-center space-x-2 mt-2">
                    <span
                        class="percentage-badge"
                        :class="outcomeDown ? 'positive' : 'negative'"
                    >
                        <el-icon :size="12">
                            <Bottom v-if="outcomeDown" />
                            <Top v-else />
                        </el-icon>
                        {{ outcomePercentage }}%
                    </span>
                    <span class="text-xs text-white/60">
                        vs anterior (${{ formatMoney(prevOutcome) }})
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
