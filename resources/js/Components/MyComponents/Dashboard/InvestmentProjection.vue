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
            <h2 class="text-lg font-semibold text-gray-800 dark:text-[#EDF1F1]">Proyección de Inversión</h2>
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
                <div class="bg-gray-50 dark:bg-[#252C2C] rounded-lg p-3">
                    <p class="text-xs text-gray-500 dark:text-[#9AA8A8] mb-1">Total invertido</p>
                    <p class="font-mono text-base font-bold text-[#296A6B] dark:text-[#7FD3D4]">
                        ${{ formatMoney(totalInvested) }}
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-[#252C2C] rounded-lg p-3">
                    <p class="text-xs text-gray-500 dark:text-[#9AA8A8] mb-1">Tasa ponderada</p>
                    <p class="font-mono text-base font-bold text-[#296A6B] dark:text-[#7FD3D4]">
                        {{ weightedRate.toFixed(2) }}%
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-[#252C2C] rounded-lg p-3">
                    <p class="text-xs text-gray-500 dark:text-[#9AA8A8] mb-1">Aportación mensual</p>
                    <p class="font-mono text-base font-bold text-green-600 dark:text-[#6EE7A8]">
                        ${{ formatMoney(monthlyContribution) }}
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-[#252C2C] rounded-lg p-3">
                    <p class="text-xs text-gray-500 dark:text-[#9AA8A8] mb-1">Gastos mensuales</p>
                    <p class="font-mono text-base font-bold text-red-500 dark:text-[#FF9D9D]">
                        ${{ formatMoney(monthlyExpenses) }}
                    </p>
                </div>
            </div>

            <!-- Punto de equilibrio -->
            <div
                class="rounded-lg p-4"
                :class="data?.break_even
                    ? 'bg-[#EAF7EF] dark:bg-[#1A3A2A] border border-green-200 dark:border-[#2F7A4F]'
                    : 'bg-gray-50 dark:bg-[#252C2C] border border-grayD9 dark:border-[#3F4949]'"
            >
                <p class="text-xs font-medium text-gray-600 dark:text-[#DCE3E3] mb-1">
                    🎯 Independencia Financiera
                </p>
                <template v-if="data?.break_even">
                    <p class="text-lg font-bold text-[#247A47] dark:text-[#6EE7A8]">
                        {{ breakEvenLabel() }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-[#DCE3E3] mt-1">
                        Tus rendimientos mensuales (${{ formatMoney(data.break_even.monthly_income) }})
                        cubrirán tus gastos (${{ formatMoney(data.break_even.monthly_expenses) }}).
                    </p>
                    <p class="text-xs text-gray-500 dark:text-[#9AA8A8] mt-1">
                        Capital necesario: <span class="font-mono font-semibold">${{ formatMoney(data.break_even.capital) }}</span>
                    </p>
                </template>
                <template v-else>
                    <p class="text-sm text-gray-600 dark:text-[#DCE3E3]">
                        {{
                            monthlyContribution > 0 && weightedRate > 0
                                ? 'Con la aportación y tasa actuales, el punto de equilibrio no se alcanza en 40 años.'
                                : 'Registra inversiones para calcular tu punto de independencia financiera.'
                        }}
                    </p>
                </template>
            </div>

            <!-- Proyección anual -->
            <div v-if="data?.yearly_projection?.length" class="border-t border-gray-200 dark:border-[#3F4949] pt-4">
                <p class="text-xs text-gray-500 dark:text-[#9AA8A8] mb-3">Proyección anual (capital y rendimientos)</p>

                <div class="max-h-72 overflow-y-auto rounded-lg border border-grayD9 dark:border-[#3F4949]">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 bg-gray-100 dark:bg-[#252C2C] text-gray-600 dark:text-[#DCE3E3] text-xs uppercase">
                            <tr>
                                <th class="text-left px-3 py-2 font-medium">Año</th>
                                <th class="text-right px-3 py-2 font-medium">Capital</th>
                                <th class="text-right px-3 py-2 font-medium">Rend. anual</th>
                                <th class="text-right px-3 py-2 font-medium">Rend. mensual</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-[#2B3232]">
                            <tr
                                v-for="row in data.yearly_projection"
                                :key="row.year"
                                class="hover:bg-gray-50 dark:hover:bg-[#2F3838]"
                                :class="data.break_even && row.year === data.break_even.year ? 'bg-[#EAF7EF] dark:bg-[#1A3A2A]' : ''"
                            >
                                <td class="px-3 py-2 text-gray-700 dark:text-[#DCE3E3] font-medium">{{ row.year }}</td>
                                <td class="px-3 py-2 text-right font-mono text-gray-800 dark:text-[#EDF1F1]">
                                    ${{ formatMoney(row.capital) }}
                                </td>
                                <td class="px-3 py-2 text-right font-mono text-green-600 dark:text-[#6EE7A8]">
                                    ${{ formatMoney(row.yearly_return) }}
                                </td>
                                <td class="px-3 py-2 text-right font-mono text-[#296A6B] dark:text-[#7FD3D4]">
                                    ${{ formatMoney(row.monthly_return) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p v-if="data.break_even" class="text-xs text-gray-500 dark:text-[#9AA8A8] mt-2">
                    ✓ La fila resaltada marca el año de tu punto de equilibrio.
                </p>
            </div>
        </div>
    </div>
</template>