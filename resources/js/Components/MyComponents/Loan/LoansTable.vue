<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Delete, MoreFilled, Plus, Tickets, Upload, Download } from '@element-plus/icons-vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import PaginationWithNoMeta from '@/Components/MyComponents/PaginationWithNoMeta.vue';
import { formatCalendarDate } from '@/utils/date';
import {
    formatCurrency,
    isLoanOverdue,
    loanFolio,
    loanInterestLabel,
    loanPersonName,
    loanRemaining,
    loanStatusLabel,
} from '@/utils/loans';

const props = defineProps({
    loans: { type: Object, required: true },
    type: { type: String, required: true },
    summary: { type: Object, default: () => ({}) },
});

const tableRef = ref(null);
const selectedRows = ref([]);
const deleting = ref(false);
const perPageOptions = [10, 20, 50, 100];

const rows = computed(() => props.loans?.data ?? []);
const isGiven = computed(() => props.type === 'Otorgado');
const currentPerPage = computed(() => props.loans?.per_page ?? 50);

const selectedTotal = computed(() =>
    selectedRows.value.reduce((sum, row) => sum + (Number(row.amount) || 0), 0)
);

const tableMaxHeight = ref(500);
const viewportWidth = ref(window.innerWidth);

function updateTableHeight() {
    tableMaxHeight.value = Math.max(240, window.innerHeight - 560);
    viewportWidth.value = window.innerWidth;
}

onMounted(() => {
    updateTableHeight();
    window.addEventListener('resize', updateTableHeight);
});

onUnmounted(() => window.removeEventListener('resize', updateTableHeight));

const showAmountColumn = computed(() => viewportWidth.value >= 640);
const showInterestColumn = computed(() => viewportWidth.value >= 1280);
const showDueColumn = computed(() => viewportWidth.value >= 1100);
const showStatusColumn = computed(() => viewportWidth.value >= 900);

function handleSelectionChange(selection) {
    selectedRows.value = selection;
}

function clearSelection() {
    tableRef.value?.clearSelection();
    selectedRows.value = [];
}

function openLoan(row) {
    router.get(route('loans.show', row.id));
}

function handleCommand(command, row) {
    const [action] = command.split('-');

    if (action === 'edit') {
        router.get(route('loans.edit', row.id));

        return;
    }

    openLoan(row);
}

function currentParams(extra = {}) {
    const params = Object.fromEntries(new URLSearchParams(window.location.search));

    return { ...params, ...extra };
}

function changePerPage(size) {
    router.get(route('loans.index'), currentParams({ per_page: size, page: 1 }), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
}

function statusTone(row) {
    if (row.status === 'Pagado') return 'tone-success';

    return isLoanOverdue(row) ? 'tone-danger' : 'tone-warning';
}

function dueTone(row) {
    return isLoanOverdue(row) ? 'is-overdue' : '';
}

async function deleteSelections() {
    try {
        await ElMessageBox.confirm(
            '¿Estás seguro que deseas eliminar los préstamos seleccionados? Esta acción no se puede deshacer.',
            'Confirmar eliminación',
            { confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar', type: 'warning' }
        );

        deleting.value = true;

        router.post(
            route('loans.massive-delete'),
            { loans: selectedRows.value.map((row) => ({ id: row.id })) },
            {
                preserveScroll: true,
                onSuccess: () => {
                    clearSelection();
                    ElMessage.success('Préstamos eliminados correctamente.');
                },
                onError: () => ElMessage.error('No se pudieron eliminar los préstamos.'),
                onFinish: () => {
                    deleting.value = false;
                },
            }
        );
    } catch (error) {
        if (error !== 'cancel') {
            ElMessage.error('Algo salió mal.');
            console.error(error);
        }
    }
}
</script>

<template>
    <section class="data-card income-table-section">
        <!-- Toolbar -->
        <header class="data-card__toolbar">
            <div class="data-card__filters">
                <span class="loan-table-meta">
                    <strong>{{ loans?.total ?? rows.length }}</strong> registro(s)
                </span>
            </div>

            <div class="data-card__meta">
                <span class="meta-item" :class="isGiven ? 'meta-item--success' : 'meta-item--expense'">
                    {{ isGiven ? 'Por cobrar' : 'Por pagar' }}
                    <strong>{{ formatCurrency(summary.remaining) }}</strong>
                </span>
                <el-select
                    :model-value="currentPerPage"
                    size="small"
                    class="per-page-select"
                    @change="changePerPage"
                >
                    <el-option v-for="size in perPageOptions" :key="size" :label="`${size} / pág.`" :value="size" />
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
            @row-click="openLoan"
        >
            <el-table-column type="selection" width="46" />

            <el-table-column :label="isGiven ? 'Beneficiario' : 'Prestamista'" min-width="140" show-overflow-tooltip>
                <template #default="{ row }">
                    <div class="concept-cell">
                        <span class="concept-cell__icon loan-icon" :class="{ 'loan-icon--received': !isGiven }">
                            <el-icon :size="13">
                                <Upload v-if="isGiven" />
                                <Download v-else />
                            </el-icon>
                        </span>
                        <div class="concept-cell__text">
                            <span class="concept-cell__name">{{ loanPersonName(row) }}</span>
                            <span class="concept-cell__hint">
                                Folio {{ loanFolio(row) }}
                                <template v-if="!showStatusColumn"> · {{ loanStatusLabel(row) }}</template>
                            </span>
                        </div>
                    </div>
                </template>
            </el-table-column>

            <el-table-column v-if="showAmountColumn" label="Monto" width="125" align="right">
                <template #default="{ row }">
                    <span class="money-cell">{{ formatCurrency(row.amount) }}</span>
                </template>
            </el-table-column>

            <el-table-column v-if="showInterestColumn" label="Interés" width="160">
                <template #default="{ row }">
                    <span class="muted-cell">{{ loanInterestLabel(row) }}</span>
                </template>
            </el-table-column>

            <el-table-column label="Pendiente" width="125" align="right">
                <template #default="{ row }">
                    <span
                        class="money-cell"
                        :class="loanRemaining(row) <= 0 ? 'is-settled' : 'is-pending'"
                    >
                        {{ formatCurrency(loanRemaining(row)) }}
                    </span>
                </template>
            </el-table-column>

            <el-table-column v-if="showDueColumn" label="Vence" width="115">
                <template #default="{ row }">
                    <span class="date-cell" :class="dueTone(row)">
                        {{ row.expired_date ? formatCalendarDate(row.expired_date) : '—' }}
                    </span>
                </template>
            </el-table-column>

            <el-table-column v-if="showStatusColumn" label="Estado" width="105">
                <template #default="{ row }">
                    <span class="category-tag" :class="statusTone(row)">{{ loanStatusLabel(row) }}</span>
                </template>
            </el-table-column>

            <el-table-column align="right" width="54" fixed="right">
                <template #default="{ row }">
                    <el-dropdown trigger="click" @command="(command) => handleCommand(command, row)">
                        <button type="button" class="row-actions-btn" @click.stop>
                            <el-icon :size="16"><MoreFilled /></el-icon>
                        </button>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item :command="`show-${row.id}`">Ver detalles</el-dropdown-item>
                                <el-dropdown-item :command="`edit-${row.id}`">Editar</el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </template>
            </el-table-column>

            <template #empty>
                <div class="empty-state py-10">
                    <p class="empty-state-title">
                        {{ isGiven ? 'No hay préstamos otorgados' : 'No hay préstamos recibidos' }}
                    </p>
                    <p class="empty-state-text">
                        {{ isGiven
                            ? 'Registra el primer préstamo que otorgues para darle seguimiento.'
                            : 'Registra el primer préstamo que recibas para darle seguimiento.' }}
                    </p>
                    <el-button type="primary" :icon="Plus" class="mt-4" @click="router.get(route('loans.create'))">
                        Nuevo préstamo
                    </el-button>
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
                        <span class="bulk-bar__amount bulk-bar__amount--neutral">{{ formatCurrency(selectedTotal) }}</span>
                    </span>

                    <div class="bulk-bar__actions">
                        <button
                            type="button"
                            class="bulk-bar__action bulk-bar__action--danger"
                            :disabled="deleting"
                            @click="deleteSelections"
                        >
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
                    Selecciona filas para eliminar en bloque
                </span>
            </div>

            <PaginationWithNoMeta :pagination="loans" />
        </footer>
    </section>
</template>
