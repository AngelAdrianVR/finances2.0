<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { Edit, Delete, MoreFilled, Refresh, CircleClose, ArrowDown } from '@element-plus/icons-vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { ElMessage, ElMessageBox } from 'element-plus';
import PaginationWithNoMeta from '@/Components/MyComponents/PaginationWithNoMeta.vue';
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

const categories = ['Ventas', 'Intereses', 'Nomina', 'Prestacion de servicios', 'Comision', 'Renta', 'Otro'];
const paymentMethods = ['Efectivo', 'Transferencia', 'Deposito', 'Cheque', 'Pago con tarjeta'];
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
    if (!date) return '';
    return format(new Date(date), 'dd MMM yyyy', { locale: es });
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
            <div class="selection-actions">
                <template v-if="selectedRows.length">
                    <span class="selection-chip">{{ selectedRows.length }} seleccionados</span>
                    <el-button size="small" :icon="Edit" @click="openMassiveEdit">Editar</el-button>
                    <el-button size="small" type="danger" plain :icon="Delete" @click="deleteSelections">Eliminar</el-button>
                </template>
                <span v-else class="selection-hint">Selecciona filas para acciones masivas</span>
            </div>

            <PaginationWithNoMeta :pagination="incomes" />
        </footer>
    </section>

    <!-- Detail dialog -->
    <el-dialog v-model="showDetail" title="Detalles del ingreso" width="500px" destroy-on-close>
        <template v-if="itemToShow">
            <div class="space-y-2">
                <div class="detail-row"><span class="detail-label">Concepto</span><span class="detail-value">{{ itemToShow.concept }}</span></div>
                <div class="detail-row"><span class="detail-label">Fecha</span><span class="detail-value">{{ formatDate(itemToShow.created_at) }}</span></div>
                <div class="detail-row"><span class="detail-label">Monto</span><span class="detail-value font-mono font-semibold" style="color: #2F9E5B;">${{ formatMoney(itemToShow.amount) }}</span></div>
                <div class="detail-row"><span class="detail-label">Categoría</span><span class="detail-value">{{ itemToShow.category }}</span></div>
                <div class="detail-row"><span class="detail-label">Método de pago</span><span class="detail-value">{{ itemToShow.payment_method }}</span></div>
                <div class="detail-row"><span class="detail-label">Descripción</span><span class="detail-value">{{ itemToShow.description ?? '-' }}</span></div>
            </div>
            <div v-if="itemToShow.automatically_created" class="mt-3">
                <span class="recurring-badge">
                    <el-icon :size="12"><Refresh /></el-icon>
                    Ingreso recurrente
                </span>
            </div>
        </template>
        <template #footer>
            <el-button v-if="itemToShow" @click="router.get(route('incomes.edit', itemToShow?.id))">Editar</el-button>
            <el-button @click="showDetail = false">Cerrar</el-button>
        </template>
    </el-dialog>

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
