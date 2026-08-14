<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { TrendCharts } from '@element-plus/icons-vue';

const props = defineProps({
    refreshKey: { type: Number, default: 0 },
});

const loading = ref(true);
const data = ref(null);

const totalInvested = ref(0);
const weightedRate = ref(0);
const monthlyContribution = ref(0);
const monthlyExpenses = ref(0);

const monthNames = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre',
];

function formatMoney(val) {
    return Number(val || 0).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

async function fetchProjection() {
    try {
        loading.value = true;
        const response = await axios.post(route('investments.projection'));
        if (response.status === 200) {
            data.value = response.data;
            totalInvested.value = Number(response.data.total_invested || 0);
            weightedRate.value = Number(response.data.weighted_rate || 0);
            monthlyContribution.value = Number(response.data.monthly_contribution || 0);
            monthlyExpenses.value = Number(response.data.monthly_expenses || 0);
        }
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
}

function breakEvenLabel() {
    const be = data.value?.break_even;
    if (!be) return null;
    return `${monthNames[be.month - 1]} ${be.year}`;
}

onMounted(fetchProjection);

watch(() => props.refreshKey, () => {
    if (props.refreshKey > 0) fetchProjection();
});
</script>

<template>
    <div class="dashboard-card">
        <div class="flex items-center gap-2 mb-5">
            <el-icon :size="18" color="#296A6B"><TrendCharts /></el-icon>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Proyección de Inversión</h2>
        </div>

        <!-- Skeleton -->
        <div v-if="loading" class="space-y-3">
            <el-skeleton animated :count="3">
                <template #template>
                    <el-skeleton-item variant="rect" style="width: 100%; height: 56px; border-radius: 8px;" />
                </template>
            </el-skeleton>
        </div>

        <!-- Contenido -->
        <div v-else class="space-y-4">
            <!-- Indicadores clave -->
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total invertido</p>
                    <p class="font-mono text-base font-bold text-[#296A6B] dark:text-primary-400">
                        ${{ formatMoney(totalInvested) }}
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tasa ponderada</p>
                    <p class="font-mono text-base font-bold text-[#296A6B] dark:text-primary-400">
                        {{ weightedRate.toFixed(2) }}%
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Aportación mensual</p>
                    <p class="font-mono text-base font-bold text-green-600 dark:text-green-400">
                        ${{ formatMoney(monthlyContribution) }}
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Gastos mensuales</p>
                    <p class="font-mono text-base font-bold text-red-500 dark:text-red-400">
                        ${{ formatMoney(monthlyExpenses) }}
                    </p>
                </div>
            </div>

            <!-- Punto de equilibrio -->
            <div
                class="rounded-lg p-4"
                :class="data?.break_even
                    ? 'bg-[#EAF7EF] dark:bg-green-900/30 border border-green-200 dark:border-green-700'
                    : 'bg-gray-50 dark:bg-gray-800 border border-grayD9 dark:border-gray-600'"
            >
                <p class="text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">
                    🎯 Independencia Financiera
                </p>
                <template v-if="data?.break_even">
                    <p class="text-lg font-bold text-[#247A47] dark:text-green-400">
                        {{ breakEvenLabel() }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                        Tus rendimientos mensuales (${{ formatMoney(data.break_even.monthly_income) }})
                        cubrirán tus gastos (${{ formatMoney(data.break_even.monthly_expenses) }}).
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Capital necesario: <span class="font-mono font-semibold">${{ formatMoney(data.break_even.capital) }}</span>
                    </p>
                </template>
                <template v-else>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        {{
                            monthlyContribution > 0 && weightedRate > 0
                                ? 'Con la aportación y tasa actuales, el punto de equilibrio no se alcanza en 40 años.'
                                : 'Registra inversiones para calcular tu punto de independencia financiera.'
                        }}
                    </p>
                </template>
            </div>

            <!-- Proyección anual -->
            <div v-if="data?.yearly_projection?.length" class="border-t border-gray-200 dark:border-gray-600 pt-4">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Proyección anual (capital y rendimientos)</p>

                <div class="max-h-72 overflow-y-auto rounded-lg border border-grayD9 dark:border-gray-600">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-xs uppercase">
                            <tr>
                                <th class="text-left px-3 py-2 font-medium">Año</th>
                                <th class="text-right px-3 py-2 font-medium">Capital</th>
                                <th class="text-right px-3 py-2 font-medium">Rend. anual</th>
                                <th class="text-right px-3 py-2 font-medium">Rend. mensual</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr
                                v-for="row in data.yearly_projection"
                                :key="row.year"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700"
                                :class="data.break_even && row.year === data.break_even.year ? 'bg-[#EAF7EF] dark:bg-green-900/30' : ''"
                            >
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 font-medium">{{ row.year }}</td>
                                <td class="px-3 py-2 text-right font-mono text-gray-800 dark:text-gray-200">
                                    ${{ formatMoney(row.capital) }}
                                </td>
                                <td class="px-3 py-2 text-right font-mono text-green-600 dark:text-green-400">
                                    ${{ formatMoney(row.yearly_return) }}
                                </td>
                                <td class="px-3 py-2 text-right font-mono text-[#296A6B] dark:text-primary-400">
                                    ${{ formatMoney(row.monthly_return) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p v-if="data.break_even" class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                    ✓ La fila resaltada marca el año de tu punto de equilibrio.
                </p>
            </div>
        </div>
    </div>
</template>