<script setup>
import { ref, computed, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { Plus } from "@element-plus/icons-vue";
import { format } from "date-fns";
import { es } from "date-fns/locale";

const props = defineProps({
    outcomes: { type: Array, default: () => [] },
});

const categoryDefs = [
    { key: "Transporte", label: "Transporte", bgClass: "bg-[#C9E7F3]", color: "#145A85" },
    { key: "Compras", label: "Compras", bgClass: "bg-[#F3D4C9]", color: "#E36C12" },
    { key: "Salud y bienestar", label: "Salud y bienestar", bgClass: "bg-[#D4F3C9]", color: "#2E8A14" },
    { key: "Entretenimiento", label: "Entretenimiento", bgClass: "bg-[#F1C9F3]", color: "#57145A" },
    { key: "Alimentos y bebidas", label: "Alimentos y bebidas", bgClass: "bg-[#F3EAC9]", color: "#B19A18" },
    { key: "Servicios", label: "Servicios", bgClass: "bg-[#C9F3F0]", color: "#147D85" },
    { key: "Educaci\u00f3n y desarrollo personal", label: "Educaci\u00f3n", bgClass: "bg-[#F3C9DF]", color: "#851457" },
    { key: "Otro", label: "Otros", bgClass: "bg-[#F3C9C9]", color: "#851414" },
];

const categoryTotals = ref({});
watch(() => props.outcomes, (val) => {
    const totals = {};
    val?.forEach((o) => {
        const cat = categoryDefs.find(d => d.key === o.category) ? o.category : "Otro";
        totals[cat] = (totals[cat] || 0) + o.amount;
    });
    categoryTotals.value = totals;
}, { immediate: true });

const totalOutcomes = computed(() =>
    Object.values(categoryTotals.value).reduce((s, v) => s + v, 0)
);

const showDetail = ref(false);
const selectedCategory = ref("");

function openDetail(cat) {
    selectedCategory.value = cat;
    showDetail.value = true;
}

const filteredOutcomes = computed(() =>
    (props.outcomes || []).filter(o => {
        const found = categoryDefs.find(d => d.key === o.category);
        return (found ? found.key : "Otro") === selectedCategory.value;
    })
);

function formatDate(date) {
    if (!date) return "";
    return format(new Date(date), "dd MMM yyyy", { locale: es });
}

function formatMoney(val) {
    return val?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function getCatTotal(key) {
    return categoryTotals.value[key] || 0;
}

const catIcons = {
    "Transporte": "\uD83D\uDE97",
    "Compras": "\uD83D\uDECD",
    "Salud y bienestar": "\u2764\uFE0F",
    "Entretenimiento": "\uD83C\uDFAE",
    "Alimentos y bebidas": "\uD83C\uDF54",
    "Servicios": "\uD83C\uDFE0",
    "Educaci\u00f3n y desarrollo personal": "\uD83D\uDCDA",
    "Otro": "\uD83D\uDCCC",
};
</script>

<template>
    <div class="dashboard-card">
        <div class="flex items-center justify-between mb-5">
            <h2 class="dashboard-card-title !mb-0 !pb-0 !border-0">
                Gastos
                <span class="font-mono text-[#D64545] ml-2">${{ formatMoney(totalOutcomes) }}</span>
            </h2>
            <el-button :icon="Plus" size="small" circle @click="router.get(route('outcomes.create'))" />
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
                    <p class="category-amount" style="color: #D64545;">
                        ${{ formatMoney(getCatTotal(cat.key)) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <el-dialog
        v-model="showDetail"
        :title="`Gastos de ` + selectedCategory"
        width="95%"
        class="category-dialog"
        :close-on-click-modal="true"
        destroy-on-close
    >
        <div v-if="filteredOutcomes.length" class="max-h-80 overflow-auto">
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, i) in filteredOutcomes" :key="i">
                        <td class="max-w-[200px] truncate">{{ item.concept }}</td>
                        <td class="amount-expense monetary-amount">$ {{ formatMoney(item.amount) }}</td>
                        <td class="text-gray-500 dark:text-gray-400 text-sm">{{ formatDate(item.created_at) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else class="empty-state">
            <p class="empty-state-title">No hay registros</p>
            <p class="empty-state-text">No hay gastos en esta categor\u00eda para el per\u00edodo seleccionado.</p>
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
