<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import {
    Plus,
    Sell,
    TrendCharts,
    Wallet,
    Tools,
    Medal,
    House,
    MoreFilled,
} from '@element-plus/icons-vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

const props = defineProps({
    incomes: { type: Array, default: () => [] },
});

const categoryDefs = [
    { key: 'Ventas', label: 'Ventas', tone: 'emerald', icon: Sell },
    { key: 'Intereses', label: 'Intereses', tone: 'sky', icon: TrendCharts },
    { key: 'Nómina', label: 'Nómina', tone: 'mint', icon: Wallet },
    { key: 'Prestación de servicios', label: 'Servicios', tone: 'jade', icon: Tools },
    { key: 'Comisión', label: 'Comisión', tone: 'cyan', icon: Medal },
    { key: 'Renta', label: 'Renta', tone: 'indigo', icon: House },
    { key: 'Otro', label: 'Otros', tone: 'neutral', icon: MoreFilled },
];

const categoryTotals = ref({});

watch(
    () => props.incomes,
    (value) => {
        const totals = {};
        value?.forEach((income) => {
            const category = categoryDefs.some((definition) => definition.key === income.category)
                ? income.category
                : 'Otro';
            totals[category] = (totals[category] || 0) + Number(income.amount || 0);
        });
        categoryTotals.value = totals;
    },
    { immediate: true }
);

const totalIncomes = computed(() =>
    Object.values(categoryTotals.value).reduce((sum, amount) => sum + amount, 0)
);

const showDetail = ref(false);
const selectedCategory = ref('');

const selectedDefinition = computed(
    () =>
        categoryDefs.find((definition) => definition.key === selectedCategory.value) ??
        categoryDefs[categoryDefs.length - 1]
);

const categoryIncomes = computed(() =>
    (props.incomes || [])
        .filter((income) => {
            const definition = categoryDefs.find((item) => item.key === income.category);
            return (definition ? definition.key : 'Otro') === selectedCategory.value;
        })
        .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
);

const selectedTotal = computed(() => categoryTotals.value[selectedCategory.value] || 0);

const selectedAverage = computed(() =>
    categoryIncomes.value.length > 0 ? selectedTotal.value / categoryIncomes.value.length : 0
);

function openDetail(category) {
    selectedCategory.value = category;
    showDetail.value = true;
}

function categoryTotal(category) {
    return categoryTotals.value[category] || 0;
}

function shareOfTotal(category) {
    if (totalIncomes.value === 0) return 0;
    return (categoryTotal(category) / totalIncomes.value) * 100;
}

function formatDate(date) {
    if (!date) return '';
    return format(new Date(date), 'dd MMM yyyy', { locale: es });
}

function formatMoney(value) {
    return Number(value || 0).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}
</script>

<template>
    <div class="dashboard-card">
        <div class="dashboard-card__head">
            <div>
                <h2 class="dashboard-card__title">Ingresos</h2>
                <p class="dashboard-card__subtitle">Toca una categoría para ver sus movimientos</p>
            </div>
            <div class="category-header__actions">
                <span class="category-total category-total--income">${{ formatMoney(totalIncomes) }}</span>
                <el-tooltip content="Registrar ingreso" placement="top">
                    <el-button :icon="Plus" size="small" circle @click="router.get(route('incomes.create'))" />
                </el-tooltip>
            </div>
        </div>

        <div class="category-grid">
            <button
                v-for="category in categoryDefs"
                :key="category.key"
                type="button"
                class="category-card"
                @click="openDetail(category.key)"
            >
                <span class="category-icon" :class="`category-icon--${category.tone}`">
                    <el-icon :size="18"><component :is="category.icon" /></el-icon>
                </span>
                <span class="category-card__body">
                    <span class="category-label">{{ category.label }}</span>
                    <span class="category-amount category-amount--income">
                        ${{ formatMoney(categoryTotal(category.key)) }}
                    </span>
                    <span class="category-card__track">
                        <span
                            class="category-card__bar category-card__bar--income"
                            :style="{ width: `${Math.max(shareOfTotal(category.key), 2)}%` }"
                        />
                    </span>
                </span>
            </button>
        </div>
    </div>

    <el-dialog
        v-model="showDetail"
        width="92%"
        class="category-dialog"
        align-center
        destroy-on-close
    >
        <template #header>
            <div class="detail-dialog__head">
                <span class="category-icon" :class="`category-icon--${selectedDefinition.tone}`">
                    <el-icon :size="20"><component :is="selectedDefinition.icon" /></el-icon>
                </span>
                <div class="min-w-0">
                    <p class="detail-dialog__eyebrow">Ingresos por categoría</p>
                    <h3 class="detail-dialog__title">{{ selectedDefinition.label }}</h3>
                </div>
            </div>
        </template>

        <div class="detail-stats">
            <div class="detail-stat">
                <p class="detail-stat__label">Total</p>
                <p class="detail-stat__value detail-stat__value--income">${{ formatMoney(selectedTotal) }}</p>
            </div>
            <div class="detail-stat">
                <p class="detail-stat__label">Movimientos</p>
                <p class="detail-stat__value">{{ categoryIncomes.length }}</p>
            </div>
            <div class="detail-stat">
                <p class="detail-stat__label">Promedio</p>
                <p class="detail-stat__value">${{ formatMoney(selectedAverage) }}</p>
            </div>
        </div>

        <div v-if="categoryIncomes.length" class="detail-table-wrap">
            <el-table :data="categoryIncomes" max-height="340" class="category-detail-table">
                <el-table-column prop="concept" label="Concepto" min-width="110" show-overflow-tooltip>
                    <template #default="{ row }">
                        <span class="detail-concept">{{ row.concept }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="Fecha" width="92">
                    <template #default="{ row }">
                        <span class="detail-date">{{ formatDate(row.created_at) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="Monto" width="102" align="right">
                    <template #default="{ row }">
                        <span class="monetary-amount amount-income">+ ${{ formatMoney(row.amount) }}</span>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <div v-else class="empty-state py-6">
            <el-icon :size="30" class="empty-state-icon"><component :is="selectedDefinition.icon" /></el-icon>
            <p class="empty-state-title">Sin movimientos</p>
            <p class="empty-state-text">No hay ingresos en esta categoría para el periodo seleccionado.</p>
        </div>

        <template #footer>
            <el-button @click="showDetail = false">Cerrar</el-button>
        </template>
    </el-dialog>
</template>
