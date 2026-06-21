<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Basic from '@/Components/MyComponents/Dashboard/Chart/Colum/Basic.vue';

const props = defineProps({
    outcomes: { type: Array, default: () => [] },
    incomes: { type: Array, default: () => [] },
});

const categories = computed(() => {
    const allDates = [...(props.outcomes || []), ...(props.incomes || [])].map(item =>
        new Date(item.created_at).toLocaleString('default', { month: 'short' })
    );
    return [...new Set(allDates)];
});

const sumByMonth = (data) => {
    const result = {};
    data?.forEach(item => {
        const month = new Date(item.created_at).toLocaleString('default', { month: 'short' });
        result[month] = (result[month] || 0) + item.amount;
    });
    return categories.value.map(month => result[month] || 0);
};

const series = computed(() => [
    { name: 'Ingresos', data: sumByMonth(props.incomes) },
    { name: 'Gastos', data: sumByMonth(props.outcomes) },
]);

const options = computed(() => ({
    chart: { type: 'bar', height: 350, toolbar: { show: false } },
    plotOptions: { bar: { horizontal: false, columnWidth: '30%', borderRadius: 5, borderRadiusApplication: 'end' } },
    dataLabels: { enabled: false },
    stroke: { show: true, width: 2, colors: ['transparent'] },
    xaxis: { categories: categories.value },
    yaxis: { title: { text: '$ MXN' } },
    fill: { opacity: 1 },
    tooltip: { y: { formatter: (val) => '$ ' + val + ' Pesos' } },
    colors: ['#296A6B', '#D64545'],
    legend: { position: 'top', horizontalAlign: 'right', fontSize: '13px', fontFamily: 'Inter, sans-serif', markers: { radius: 4 } },
    grid: { borderColor: '#EDF1F1', strokeDashArray: 4 },
}));

const darkOptions = computed(() => ({
    ...options.value,
    grid: { borderColor: '#3F4949', strokeDashArray: 4 },
    tooltip: { ...options.value.tooltip, theme: 'dark' },
}));

const activeOptions = computed(() => {
    return document.documentElement.classList.contains('dark') ? darkOptions.value : options.value;
});

const hasData = computed(() => (props.incomes?.length > 0 || props.outcomes?.length > 0));
</script>

<template>
    <div class="dashboard-card">
        <h2 class="dashboard-card-title">Estadisticas</h2>
        <Basic v-if="hasData" :chartOptions="activeOptions" :series="series" />

        <div v-else class="empty-state py-8">
            <p class="empty-state-title">No hay informacion para mostrar</p>
            <p class="empty-state-text">Agrega un ingreso o gasto para visualizar la grafica</p>
            <div class="flex gap-3 mt-4">
                <el-button type="primary" @click="router.get(route('incomes.create'))">+ Ingreso</el-button>
                <el-button @click="router.get(route('outcomes.create'))">+ Gasto</el-button>
            </div>
        </div>
    </div>
</template>
