<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { Plus } from '@element-plus/icons-vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

const props = defineProps({
    incomes: { type: Array, default: () => [] },
});

const categoryDefs = [
    { key: 'Ventas', label: 'Ventas', bgClass: 'bg-green-100', color: '#147D85' },
    { key: 'Intereses', label: 'Intereses', bgClass: 'bg-sky-100', color: '#0EA5E9' },
    { key: 'Nómina', label: 'Nómina', bgClass: 'bg-slate-200', color: '#475569' },
    { key: 'Prestación de servicios', label: 'Servicios', bgClass: 'bg-stone-200', color: '#57534E' },
    { key: 'Comisión', label: 'Comisión', bgClass: 'bg-pink-100', color: '#EC4899' },
    { key: 'Renta', label: 'Renta', bgClass: 'bg-indigo-100', color: '#4F46E5' },
    { key: 'Otro', label: 'Otros', bgClass: 'bg-[#F3C9C9]', color: '#851414' },
];

const categoryTotals = ref({});
watch(() => props.incomes, (val) => {
    const totals = {};
    val?.forEach((inc) => {
        const cat = categoryDefs.find(d => d.key === inc.category) ? inc.category : 'Otro';
        totals[cat] = (totals[cat] || 0) + inc.amount;
    });
    categoryTotals.value = totals;
}, { immediate: true });

const totalIncomes = computed(() =>
    Object.values(categoryTotals.value).reduce((s, v) => s + v, 0)
);

const showDetail = ref(false);
const selectedCategory = ref('');

function openDetail(cat) {
    selectedCategory.value = cat;
    showDetail.value = true;
}

const filteredIncomes = computed(() =>
    (props.incomes || []).filter(inc => {
        const found = categoryDefs.find(d => d.key === inc.category);
        return (found ? found.key : 'Otro') === selectedCategory.value;
    })
);

function formatDate(date) {
    if (!date) return '';
    return format(new Date(date), 'dd MMM yyyy', { locale: es });
}

function formatMoney(val) {
    return val?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function getCatTotal(key) {
    return categoryTotals.value[key] || 0;
}

const catIcons = {
    'Ventas': '💰',
    'Intereses': '📈',
    'Nómina': '💳',
    'Prestación de servicios': '🔧',
    'Comisión': '📋',
    'Renta': '🏢',
    'Otro': '📌',
};
</script>

<template>
    <div class="dashboard-card">
        <div class="flex items-center justify-between mb-5">
            <h2 class="dashboard-card-title !mb-0 !pb-0 !border-0">
                Ingresos
                <span class="font-mono text-[#2F9E5B] ml-2">${{ formatMoney(totalIncomes) }}</span>
            </h2>
            <el-button :icon="Plus" size="small" circle @click="router.get(route('incomes.create'))" />
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            <div
                v-for="cat in categoryDefs"
                :key="cat.key"
                class="category-card"
                @click="openDetail(cat.key)"
            >
                <div class="category-icon" :class="cat.bgClass">
                    <span class="text-lg">{{ catIcons[cat.key] }}</span>
                </div>
                <div class="min-w-0">
                    <p class="category-label truncate">{{ cat.label }}</p>
                    <p class="category-amount" style="color: #2F9E5B;">
                        ${{ formatMoney(getCatTotal(cat.key)) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <el-dialog
        v-model="showDetail"
        :title="'Ingresos de ' + selectedCategory"
        width="95%"
        class="category-dialog"
        :close-on-click-modal="true"
        destroy-on-close
    >
        <div v-if="filteredIncomes.length" class="max-h-80 overflow-auto">
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, i) in filteredIncomes" :key="i">
                        <td class="max-w-[200px] truncate">{{ item.concept }}</td>
                        <td class="amount-income monetary-amount">$ {{ formatMoney(item.amount) }}</td>
                        <td class="text-gray-500 dark:text-gray-400 text-sm">{{ formatDate(item.created_at) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else class="empty-state">
            <p class="empty-state-title">No hay registros</p>
            <p class="empty-state-text">No hay ingresos en esta categoria para el periodo seleccionado.</p>
        </div>
        <template #footer>
            <el-button @click="showDetail = false">Cerrar</el-button>
        </template>
    </el-dialog>
</template>

<style>
@media (min-width: 640px) {
    .category-dialog {
        max-width: 560px !important;
    }
}
</style>
