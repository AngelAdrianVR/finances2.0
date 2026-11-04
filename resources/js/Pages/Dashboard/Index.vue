<script setup>
import { computed, ref } from 'vue';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import PeriodToolbar from '@/Components/MyComponents/Dashboard/PeriodToolbar.vue';
import BalanceHero from '@/Components/MyComponents/Dashboard/BalanceHero.vue';
import KpiStrip from '@/Components/MyComponents/Dashboard/KpiStrip.vue';
import CashflowChart from '@/Components/MyComponents/Dashboard/CashflowChart.vue';
import CategoryDonut from '@/Components/MyComponents/Dashboard/CategoryDonut.vue';
import InsightsPanel from '@/Components/MyComponents/Dashboard/InsightsPanel.vue';
import Incomes from '@/Components/MyComponents/Dashboard/Incomes.vue';
import Outcomes from '@/Components/MyComponents/Dashboard/Outcomes.vue';
import LoanStatus from '@/Components/MyComponents/Dashboard/LoanStatus.vue';
import YearlySummary from '@/Components/MyComponents/Dashboard/YearlySummary.vue';
import InvestmentPortfolio from '@/Components/MyComponents/Dashboard/InvestmentPortfolio.vue';
import InvestmentProjection from '@/Components/MyComponents/Dashboard/InvestmentProjection.vue';

const props = defineProps({
    userName: { type: String, default: '' },
});

const periodicity = ref('Mensual');
const period = ref(new Date());
const loading = ref(true);
const overview = ref(null);
const investmentRefreshKey = ref(0);

const firstName = computed(() => (props.userName ?? '').split(' ')[0] || 'Bienvenido');

async function fetchOverview() {
    try {
        loading.value = true;
        const { data } = await axios.post(route('dashboard.fetch-overview'), {
            periodicity: periodicity.value,
            period: period.value,
        });
        overview.value = data;
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
}

function onInvestmentsChanged() {
    investmentRefreshKey.value++;
}

fetchOverview();
</script>

<template>
    <AppLayout title="Panel de inicio">
        <main class="dashboard-shell">
            <header class="dashboard-heading">
                <div class="dashboard-heading__text">
                    <p class="dashboard-heading__eyebrow">Resumen financiero</p>
                    <h1 class="dashboard-heading__title">Hola, {{ firstName }} 👋</h1>
                    <p class="dashboard-heading__subtitle">
                        {{ overview?.range?.label ?? 'Cargando tu periodo…' }}
                    </p>
                </div>

                <PeriodToolbar
                    v-model:periodicity="periodicity"
                    v-model:period="period"
                    class="dashboard-heading__toolbar"
                    @change="fetchOverview"
                />
            </header>

            <BalanceHero :overview="overview" />

            <template v-if="overview">
                <KpiStrip :overview="overview" />

                <div class="dashboard-grid">
                    <CashflowChart
                        class="dashboard-span-2"
                        :trend="overview.trend"
                        :periodicity="periodicity"
                    />
                    <CategoryDonut
                        :categories="overview.categories.outcome"
                        :total="overview.totals.outcome"
                    />

                    <InsightsPanel :overview="overview" />
                    <LoanStatus :loans="overview.loans" />
                    <InvestmentProjection :refresh-key="investmentRefreshKey" />
                </div>

                <div class="dashboard-grid dashboard-grid--half">
                    <Incomes :incomes="overview.incomes" />
                    <Outcomes :outcomes="overview.outcomes" />
                </div>

                <InvestmentPortfolio @changed="onInvestmentsChanged" />

                <YearlySummary :periodicity="periodicity" :period="period" />
            </template>

            <div v-else-if="loading" class="dashboard-grid">
                <div class="dashboard-card dashboard-span-2">
                    <el-skeleton animated :rows="6" />
                </div>
                <div class="dashboard-card">
                    <el-skeleton animated :rows="6" />
                </div>
            </div>
        </main>
    </AppLayout>
</template>
