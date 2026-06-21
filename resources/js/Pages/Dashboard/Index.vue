<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import TotalBalance from '@/Components/MyComponents/Dashboard/TotalBalance.vue';
import Outcomes from '@/Components/MyComponents/Dashboard/Outcomes.vue';
import Incomes from '@/Components/MyComponents/Dashboard/Incomes.vue';
import Statistics from '@/Components/MyComponents/Dashboard/Statistics.vue';
import LoanStatus from '@/Components/MyComponents/Dashboard/LoanStatus.vue';
import YearlySummary from '@/Components/MyComponents/Dashboard/YearlySummary.vue';
import axios from 'axios';

const loading = ref(false);
const period = ref(null);
const periodicity = ref('Por día');
const outcomes = ref(null);
const incomes = ref(null);
const loans = ref(null);

const options = ['Por día', 'Semanal', 'Mensual', 'Anual'];

function changeType() {
    period.value = null;
}

async function fetchDataForPeriod() {
    try {
        loading.value = true;
        const response = await axios.post(route('dashboard.fetch-data-for-period'), {
            periodicity: periodicity.value,
            period: period.value,
        });
        if (response.status === 200) {
            outcomes.value = response.data.outcomes;
            incomes.value = response.data.incomes;
            loans.value = response.data.loans;
        }
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
}

// Trigger fetch when periodicity or period changes
watch(period, () => {
    if (period.value) fetchDataForPeriod();
});
watch(periodicity, () => {
    period.value = null;
});

fetchDataForPeriod();
</script>

<template>
    <AppLayout title="Panel de inicio">
        <main class="py-6 px-4 md:px-8 max-w-7xl mx-auto dark:text-gray-200">
            <!-- Periodicity selector -->
            <div class="dashboard-periodicity w-full max-w-md mx-auto mb-8">
                <el-segmented
                    v-model="periodicity"
                    :options="options"
                    block
                    @change="changeType"
                />

                <div class="mt-3 flex justify-center">
                    <el-date-picker
                        v-if="periodicity === 'Por día'"
                        v-model="period"
                        type="date"
                        placeholder="Selecciona un día"
                        @change="fetchDataForPeriod"
                    />
                    <el-date-picker
                        v-if="periodicity === 'Semanal'"
                        v-model="period"
                        type="week"
                        format="[Week] ww"
                        placeholder="Selecciona una semana"
                        @change="fetchDataForPeriod"
                    />
                    <el-date-picker
                        v-if="periodicity === 'Mensual'"
                        v-model="period"
                        type="month"
                        placeholder="Selecciona un mes"
                        @change="fetchDataForPeriod"
                    />
                    <el-date-picker
                        v-if="periodicity === 'Anual'"
                        v-model="period"
                        type="year"
                        placeholder="Selecciona un año"
                        @change="fetchDataForPeriod"
                    />
                </div>
            </div>

            <!-- Loading state — skeleton cards while data loads -->
            <article v-if="loading" class="lg:flex lg:space-x-6 space-y-6 lg:space-y-0">
                <section class="lg:w-[70%] space-y-6">
                    <el-skeleton animated class="total-balance-card !rounded-xl !p-6">
                        <template #template>
                            <el-skeleton-item variant="h3" style="width: 50%; height: 20px; margin-bottom: 20px;" />
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

                    <el-skeleton animated class="dashboard-card !rounded-xl">
                        <template #template>
                            <div style="padding: 24px;">
                                <el-skeleton-item variant="h3" style="width: 30%; height: 22px; margin-bottom: 20px;" />
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                    <div v-for="i in 4" :key="i" style="border: 1px solid #EDF1F1; border-radius: 8px; padding: 12px; display: flex; align-items: center; gap: 14px;">
                                        <el-skeleton-item variant="circle" style="width: 44px; height: 44px;" />
                                        <div style="flex: 1;">
                                            <el-skeleton-item variant="text" style="width: 60%; height: 12px; margin-bottom: 8px;" />
                                            <el-skeleton-item variant="text" style="width: 80%; height: 16px;" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </el-skeleton>

                    <el-skeleton animated class="dashboard-card !rounded-xl">
                        <template #template>
                            <div style="padding: 24px;">
                                <el-skeleton-item variant="h3" style="width: 30%; height: 22px; margin-bottom: 20px;" />
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                    <div v-for="i in 4" :key="i" style="border: 1px solid #EDF1F1; border-radius: 8px; padding: 12px; display: flex; align-items: center; gap: 14px;">
                                        <el-skeleton-item variant="circle" style="width: 44px; height: 44px;" />
                                        <div style="flex: 1;">
                                            <el-skeleton-item variant="text" style="width: 60%; height: 12px; margin-bottom: 8px;" />
                                            <el-skeleton-item variant="text" style="width: 80%; height: 16px;" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </el-skeleton>
                </section>

                <section class="lg:w-[30%] space-y-6">
                    <el-skeleton animated class="dashboard-card !rounded-xl">
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

                    <el-skeleton animated class="dashboard-card !rounded-xl">
                        <template #template>
                            <div style="padding: 24px;">
                                <el-skeleton-item variant="h3" style="width: 60%; height: 22px; margin-bottom: 20px;" />
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <el-skeleton-item variant="text" style="width: 80%; height: 12px; margin-bottom: 8px;" />
                                        <el-skeleton-item variant="text" style="width: 60%; height: 16px; margin-bottom: 8px;" />
                                        <el-skeleton-item variant="text" style="width: 40%; height: 12px;" />
                                    </div>
                                    <div>
                                        <el-skeleton-item variant="text" style="width: 80%; height: 12px; margin-bottom: 8px;" />
                                        <el-skeleton-item variant="text" style="width: 60%; height: 16px; margin-bottom: 8px;" />
                                        <el-skeleton-item variant="text" style="width: 40%; height: 12px;" />
                                    </div>
                                </div>
                            </div>
                        </template>
                    </el-skeleton>
                </section>
            </article>

            <!-- Content (loaded) -->
            <article v-else class="lg:flex lg:space-x-6 space-y-6 lg:space-y-0">
                <!-- Left column (70%) -->
                <section class="lg:w-[70%] space-y-6">
                    <TotalBalance :periodicity="periodicity" />
                    <Outcomes :outcomes="outcomes" />
                    <Incomes :incomes="incomes" />
                    <Statistics :outcomes="outcomes" :incomes="incomes" />
                </section>

                <!-- Right column (30%) -->
                <section class="lg:w-[30%] space-y-6">
                    <YearlySummary />
                    <LoanStatus :loans="loans" />
                </section>
            </article>
        </main>
    </AppLayout>
</template>