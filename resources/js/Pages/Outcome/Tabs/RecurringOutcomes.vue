<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Edit, Delete, MoreFilled, Refresh, VideoPause, VideoPlay } from '@element-plus/icons-vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import PaginationWithNoMeta from '@/Components/MyComponents/PaginationWithNoMeta.vue';
import RecurringDetailDialog from '@/Components/MyComponents/Recurring/RecurringDetailDialog.vue';
import { formatCalendarDate } from '@/utils/date';
import axios from 'axios';

const props = defineProps({ recurring_outcomes: Object });

const tableRef = ref(null);
const selectedRows = ref([]);
const disableMassiveActions = ref(true);
const showDetail = ref(false);
const itemToShow = ref(null);

function formatDate(date) { return formatCalendarDate(date, 'dd MMMM yyyy'); }
function formatMoney(val) { return val?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','); }

function handleSelectionChange(val) { selectedRows.value = val; disableMassiveActions.value = !val.length; }
function handleRowClick(row) { itemToShow.value = row; showDetail.value = true; }
function handleCommand(command) { const [action, id] = command.split('-'); if (action === 'show') showDetail.value = true; else if (action === 'edit') router.get(route('recurring-outcomes.edit', id)); else if (action === 'status') toggleStatus(id); }

async function toggleStatus(rowId) {
    try {
        const response = await axios.get(route('recurring-outcomes.toggle-status', rowId));
        if (response.status === 200) {
            const idx = props.recurring_outcomes.data.findIndex(i => i.id == rowId);
            if (idx !== -1) props.recurring_outcomes.data[idx].is_active = response.data.is_active;
            ElMessage.success('Estatus cambiado.');
        }
    } catch (error) { ElMessage.error('Error al cambiar estatus.'); console.error(error); }
}

function onDetailToggled({ id, isActive }) {
    const index = props.recurring_outcomes.data.findIndex((item) => item.id === id);

    if (index !== -1) {
        props.recurring_outcomes.data[index].is_active = isActive;
    }
}

async function deleteSelections() {
    try {
        await ElMessageBox.confirm('Estas seguro que deseas eliminar los gastos recurrentes seleccionados?', 'Confirmar eliminacion', { confirmButtonText: 'Si, eliminar', cancelButtonText: 'Cancelar', type: 'warning' });
        const response = await axios.post(route('recurring-outcomes.massive-delete'), { recurring_outcomes: selectedRows.value });
        if (response.status === 200) {
            ElMessage.success('Eliminados correctamente.');
            const ids = new Set(selectedRows.value.map(r => r.id));
            props.recurring_outcomes.data = props.recurring_outcomes.data.filter(d => !ids.has(d.id));
            tableRef.value?.clearSelection(); selectedRows.value = []; disableMassiveActions.value = true;
        }
    } catch (err) { if (err !== 'cancel') { ElMessage.error('Algo salio mal.'); console.error(err); } }
}

function tableRowClassName({ row }) { return row.is_active ? 'cursor-pointer' : 'cursor-pointer inactive-row'; }
</script>

<template>
    <section class="data-card income-table-section">
        <header class="data-card__toolbar">
            <div class="data-card__filters">
                <span class="meta-item">
                    <strong>{{ recurring_outcomes?.data?.length ?? 0 }}</strong> gasto(s) recurrente(s)
                </span>
            </div>

            <div class="data-card__meta">
                <PaginationWithNoMeta :pagination="recurring_outcomes" />
                <el-tooltip content="Eliminar seleccionados" placement="top">
                    <el-button :disabled="disableMassiveActions" :icon="Delete" circle size="small" type="danger" @click="deleteSelections" />
                </el-tooltip>
            </div>
        </header>

        <el-table ref="tableRef" :data="recurring_outcomes.data" max-height="600" class="data-table" style="width: 100%" @selection-change="handleSelectionChange" @row-click="handleRowClick" :row-class-name="tableRowClassName">
            <el-table-column type="selection" width="46" />
            <el-table-column prop="concept" label="Concepto" min-width="180" show-overflow-tooltip />
            <el-table-column label="Monto" width="140" align="right">
                <template #default="{ row }"><span class="amount-cell amount-cell--expense">-${{ formatMoney(row.amount) }}</span></template>
            </el-table-column>
            <el-table-column prop="periodicity" label="Recurrencia" width="140" />
            <el-table-column label="Categoría" width="170">
                <template #default="{ row }">
                    <span class="category-tag tone-neutral">{{ row.category ?? 'Sin categoría' }}</span>
                </template>
            </el-table-column>
            <el-table-column label="Método" width="150">
                <template #default="{ row }"><span class="muted-cell">{{ row.payment_method ?? '—' }}</span></template>
            </el-table-column>
            <el-table-column label="Creado" width="140">
                <template #default="{ row }"><span class="date-cell">{{ formatDate(row.created_at) }}</span></template>
            </el-table-column>
            <el-table-column align="right" width="64" fixed="right">
                <template #default="{ row }">
                    <el-dropdown trigger="click" @command="handleCommand">
                        <button type="button" class="row-actions-btn" @click.stop><el-icon :size="16"><MoreFilled /></el-icon></button>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item :command="'show-' + row.id" @click="itemToShow = row">Ver detalles</el-dropdown-item>
                                <el-dropdown-item :command="'edit-' + row.id">Editar</el-dropdown-item>
                                <el-dropdown-item :command="'status-' + row.id" divided>
                                    <el-icon :size="14" class="mr-1"><VideoPause v-if="row.is_active" /><VideoPlay v-else /></el-icon>
                                    {{ row.is_active ? 'Deshabilitar' : 'Habilitar' }}
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </template>
            </el-table-column>
        </el-table>
    </section>

    <RecurringDetailDialog
        v-model="showDetail"
        :record="itemToShow"
        type="outcome"
        @toggled="onDetailToggled"
    />
</template>
