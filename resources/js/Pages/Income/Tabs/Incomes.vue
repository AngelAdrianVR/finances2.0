<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { Edit, Delete, MoreFilled, Refresh, CircleClose, ArrowDown, Tickets } from '@element-plus/icons-vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import PaginationWithNoMeta from '@/Components/MyComponents/PaginationWithNoMeta.vue';
import IncomeDetailDialog from '@/Components/MyComponents/Income/IncomeDetailDialog.vue';
import { formatCalendarDate } from '@/utils/date';
import {
    INCOME_CATEGORY_VALUES,
    INCOME_PAYMENT_METHOD_VALUES,
} from '@/config/finance';
import axios from 'axios';

const props = defineProps({
    incomes: Object,
    filters: { type: Object, default: () => ({ q: '', category: null, payment_method: null }) },
    filtered: { type: Object, default: () => ({ count: 0, total: 0 }) },
});

const tableRef = ref(null);
const selectedRows = ref([]);
const disableMassiveActions = ref(true);

const showDetail = ref(false);
const itemToShow = ref(null);
const showMassiveEdit = ref(false);

const form = useForm({
    category: null,
    payment_method: null,
    selections: [],
});

const categories = INCOME_CATEGORY_VALUES;
const paymentMethods = INCOME_PAYMENT_METHOD_VALUES;
const perPageOptions = [10, 20, 50, 100];

// Filtros aplicados en servidor (query params)
const filters = ref({
    category: props.filters?.category ?? null,
    payment_method: props.filters?.payment_method ?? null,
});

const rows = computed(() => props.incomes?.data ?? []);

const hasActiveFilters = computed(() => Boolean(filters.value.category || filters.value.payment_method));

const totalShown = computed(() => Number(props.filtered?.total ?? 0));
const filteredCount = computed(() => Number(props.filtered?.count ?? 0));

const selectedTotal = computed(() =>
    selectedRows.value.reduce((sum, row) => sum + (Number(row.amount) || 0), 0)
);

const currentPerPage = computed(() => props.incomes?.per_page || 50);

function requestFilters() {
    const params = { per_page: currentPerPage.value, page: 1 };

    if (props.filters?.q) params.q = props.filters.q;
    if (filters.value.category) params.category = filters.value.category;
    if (filters.value.payment_method) params.payment_method = filters.value.payment_method;

    router.get(route('incomes.index'), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function onFilterChange() {
    requestFilters();
}

function clearFilters() {
    filters.value = { category: null, payment_method: null };
    requestFilters();
}

const tableMaxHeight = ref(500);
const viewportWidth = ref(window.innerWidth);
function updateTableHeight() {
    tableMaxHeight.value = Math.max(240, window.innerHeight - 470);
    viewportWidth.value = window.innerWidth;
}
onMounted(() => {
    updateTableHeight();
    window.addEventListener('resize', updateTableHeight);
});
onUnmounted(() => window.removeEventListener('resize', updateTableHeight));

const showPaymentColumn = computed(() => viewportWidth.value >= 1200);
const showDateColumn = computed(() => viewportWidth.value >= 1024);

function changePerPage(size) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', size);
    url.searchParams.delete('page');
    router.get(url.pathname + url.search, {}, { preserveScroll: true });
}

function formatDate(date) {
    return formatCalendarDate(date);
}

function formatMoney(val) {
    return Number(val || 0).toLocaleString('es-MX', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

const categoryTones = {
    Ventas: 'tone-primary',
    Intereses: 'tone-info',
    Nomina: 'tone-success',
    'Prestacion de servicios': 'tone-accent',
    Comision: 'tone-warning',
    Renta: 'tone-neutral',
    Otro: 'tone-neutral',
};

function categoryTone(category) {
    return categoryTones[category] ?? 'tone-neutral';
}

function handleSelectionChange(val) {
    selectedRows.value = val;
    disableMassiveActions.value = !val.length;
}

function clearSelection() {
    tableRef.value?.clearSelection();
    selectedRows.value = [];
    disableMassiveActions.value = true;
}

function handleRowClick(row) {
    itemToShow.value = row;
    showDetail.value = true;
}

function handleCommand(command) {
    const [action, id] = command.split('-');
    if (action === 'show') {
        showDetail.value = true;
    } else if (action === 'edit') {
        router.get(route('incomes.edit', id));
    }
}

function openMassiveEdit() {
    form.selections = selectedRows.value;
    showMassiveEdit.value = true;
}

function massiveUpdate() {
    form.post(route('incomes.massive-update'), {
        onSuccess: () => {
            showMassiveEdit.value = false;
            selectedRows.value.forEach((inc) => {
                inc.category = form.category;
                inc.payment_method = form.payment_method;
            });
            tableRef.value?.clearSelection();
            selectedRows.value = [];
            disableMassiveActions.value = true;
            form.reset();
            ElMessage.success('Ingresos actualizados.');
        },
        onError: (err) => console.error(err),
    });
}

async function deleteSelections() {
    try {
        await ElMessageBox.confirm(
            '¿Estás seguro que deseas eliminar los ingresos seleccionados?',
            'Confirmar eliminación',
            { confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar', type: 'warning' }
        );

        const response = await axios.post(route('incomes.massive-delete'), {
            ids: selectedRows.value.map((r) => r.id),
        });

        if (response.status === 200) {
            ElMessage.success('Ingresos eliminados correctamente.');
            const deletedIds = new Set(selectedRows.value.map((r) => r.id));
            props.incomes.data = props.incomes.data.filter((d) => !deletedIds.has(d.id));
            tableRef.value?.clearSelection();
            selectedRows.value = [];
            disableMassiveActions.value = true;
        }
    } catch (err) {
        if (err !== 'cancel') {
            ElMessage.error('Algo salió mal.');
            console.error(err);
        }
    }
}
</script>

<template>
    <section class="data-card income-table-section">
        <!-- Toolbar -->
        <header class="data-card__toolbar">
            <div class="data-card__filters">
                <el-select
                    v-model="filters.category"
                    clearable
                    filterable
                    placeholder="Categoría"
                    class="filter-select"
                    @change="onFilterChange"
                >
                    <el-option v-for="cat in categories" :key="cat" :label="cat" :value="cat" />
                </el-select>
                <el-select
                    v-model="filters.payment_method"
                    clearable
                    filterable
                    placeholder="Método de pago"
                    class="filter-select"
                    @change="onFilterChange"
                >
                    <el-option v-for="pm in paymentMethods" :key="pm" :label="pm" :value="pm" />
                </el-select>
                <button v-if="hasActiveFilters" type="button" class="filter-clear" @click="clearFilters">
                    <el-icon :size="13"><CircleClose /></el-icon>
                    Limpiar filtros
                </button>
            </div>

            <div class="data-card__meta">
                <span class="meta-item">
                    <strong>{{ filteredCount }}</strong> registro(s)
                </span>
                <span class="meta-item meta-item--success">
                    Total <strong>${{ formatMoney(totalShown) }}</strong>
                </span>
                <el-select :model-value="currentPerPage" size="small" class="per-page-select" @change="changePerPage">
                    <el-option v-for="n in perPageOptions" :key="n" :label="`${n} / pág.`" :value="n" />
                </el-select>
            </div>
        </header>

        <!-- Table -->
        <el-table
            ref="tableRef"
            :data="rows"
            :max-height="tableMaxHeight"
            class="data-table"
            style="width: 100%"
            @selection-change="handleSelectionChange"
            @row-click="handleRowClick"
        >
            <el-table-column type="selection" width="46" />

            <el-table-column label="Concepto" min-width="170" show-overflow-tooltip>
                <template #default="{ row }">
                    <div class="concept-cell">
                        <span class="concept-cell__icon">
                            <el-icon :size="13"><ArrowDown /></el-icon>
                        </span>
                        <div class="concept-cell__text">
                            <span class="concept-cell__name">{{ row.concept }}</span>
                            <span v-if="row.description" class="concept-cell__hint">{{ row.description }}</span>
                        </div>
                    </div>
                </template>
            </el-table-column>

            <el-table-column label="Categoría" width="145">
                <template #default="{ row }">
                    <span class="category-tag" :class="categoryTone(row.category)">
                        {{ row.category ?? 'Sin categoría' }}
                    </span>
                </template>
            </el-table-column>

            <el-table-column v-if="showPaymentColumn" label="Método de pago" width="150">
                <template #default="{ row }">
                    <span class="muted-cell">{{ row.payment_method ?? '—' }}</span>
                </template>
            </el-table-column>

            <el-table-column v-if="showDateColumn" label="Fecha" width="115">
                <template #default="{ row }">
                    <span class="date-cell">{{ formatDate(row.created_at) }}</span>
                </template>
            </el-table-column>

            <el-table-column label="Monto" width="130" align="right">
                <template #default="{ row }">
                    <span class="amount-cell">
                        <el-tooltip v-if="row.automatically_created" content="Creado automáticamente" placement="top">
                            <el-icon :size="13" class="recurring-icon"><Refresh /></el-icon>
                        </el-tooltip>
                        +${{ formatMoney(row.amount) }}
                    </span>
                </template>
            </el-table-column>

            <el-table-column align="right" width="54" fixed="right">
                <template #default="{ row }">
                    <el-dropdown trigger="click" @command="handleCommand">
                        <button type="button" class="row-actions-btn" @click.stop>
                            <el-icon :size="16"><MoreFilled /></el-icon>
                        </button>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item :command="'show-' + row.id" @click="itemToShow = row">
                                    Ver detalles
                                </el-dropdown-item>
                                <el-dropdown-item :command="'edit-' + row.id">
                                    Editar
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </template>
            </el-table-column>

            <template #empty>
                <div class="empty-state py-10">
                    <p class="empty-state-title">No hay ingresos registrados</p>
                    <p class="empty-state-text">
                        {{ hasActiveFilters ? 'Ajusta los filtros para ver más resultados.' : 'Registra tu primer ingreso para empezar.' }}
                    </p>
                </div>
            </template>
        </el-table>

        <!-- Footer -->
        <footer class="data-card__footer">
            <div class="bulk-bar" :class="{ 'is-active': selectedRows.length }">
                <template v-if="selectedRows.length">
                    <span class="bulk-bar__summary">
                        <span class="bulk-bar__count">{{ selectedRows.length }}</span>
                        {{ selectedRows.length === 1 ? 'seleccionado' : 'seleccionados' }}
                        <span class="bulk-bar__amount">${{ formatMoney(selectedTotal) }}</span>
                    </span>

                    <div class="bulk-bar__actions">
                        <button type="button" class="bulk-bar__action" @click="openMassiveEdit">
                            <el-icon :size="14"><Edit /></el-icon>
                            Editar
                        </button>
                        <button type="button" class="bulk-bar__action bulk-bar__action--danger" @click="deleteSelections">
                            <el-icon :size="14"><Delete /></el-icon>
                            Eliminar
                        </button>
                        <button type="button" class="bulk-bar__clear" @click="clearSelection">
                            Limpiar
                        </button>
                    </div>
                </template>

                <span v-else class="bulk-bar__hint">
                    <el-icon :size="13"><Tickets /></el-icon>
                    Selecciona filas para editar o eliminar en bloque
                </span>
            </div>

            <PaginationWithNoMeta :pagination="incomes" />
        </footer>
    </section>

    <!-- Detail dialog -->
    <IncomeDetailDialog v-model="showDetail" :income="itemToShow" />

    <!-- Massive edit dialog -->
    <el-dialog v-model="showMassiveEdit" title="Editar ingresos masivamente" width="480px" destroy-on-close>
        <el-form :model="form" label-position="top">
            <el-form-item label="Categoría">
                <el-select v-model="form.category" filterable placeholder="Selecciona" class="!w-full">
                    <el-option v-for="cat in categories" :key="cat" :label="cat" :value="cat" />
                </el-select>
            </el-form-item>
            <el-form-item label="Método de pago">
                <el-select v-model="form.payment_method" filterable placeholder="Selecciona" class="!w-full">
                    <el-option v-for="pm in paymentMethods" :key="pm" :label="pm" :value="pm" />
                </el-select>
            </el-form-item>
        </el-form>
        <div class="massive-edit-hint">
            Todos los ingresos seleccionados <strong>({{ selectedRows.length }})</strong> se actualizarán con los valores establecidos.
        </div>
        <template #footer>
            <el-button @click="showMassiveEdit = false">Cancelar</el-button>
            <el-button type="primary" :disabled="!form.category || !form.payment_method || form.processing" :loading="form.processing" @click="massiveUpdate">
                Guardar cambios
            </el-button>
        </template>
    </el-dialog>
</template>
