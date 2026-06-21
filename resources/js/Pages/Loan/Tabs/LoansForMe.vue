<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Delete, MoreFilled } from '@element-plus/icons-vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { ElMessage, ElMessageBox } from 'element-plus';
import PaginationWithNoMeta from '@/Components/MyComponents/PaginationWithNoMeta.vue';
import axios from 'axios';

const props = defineProps({ loans: Object });
const tableRef = ref(null);
const selectedRows = ref([]);
const disableMassiveActions = ref(true);
const deleting = ref(false);

function getRemaining(loan) { return loan.payments[loan.payments.length - 1]?.remaining ?? loan.amount; }
function formatDate(date) { return date ? format(new Date(date), 'dd MMMM yyyy', { locale: es }) : ''; }
function formatMoney(val) { return val?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','); }
function handleSelectionChange(val) { selectedRows.value = val; disableMassiveActions.value = !val.length; }
function handleCommand(command) { const [action, id] = command.split('-'); router.get(route('loans.' + action, id)); }
function tableRowClassName() { return 'cursor-pointer'; }

async function deleteSelections() {
    try {
        await ElMessageBox.confirm('Estas seguro que deseas eliminar los prestamos seleccionados?', 'Confirmar eliminacion', { confirmButtonText: 'Si, eliminar', cancelButtonText: 'Cancelar', type: 'warning' });
        deleting.value = true;
        const response = await axios.post(route('loans.massive-delete'), { loans: selectedRows.value });
        if (response.status === 200) {
            ElMessage.success('Eliminados correctamente.');
            const ids = new Set(selectedRows.value.map(r => r.id));
            props.loans.data = props.loans.data.filter(d => !ids.has(d.id));
            tableRef.value?.clearSelection(); selectedRows.value = []; disableMassiveActions.value = true;
        }
    } catch (err) { if (err !== 'cancel') { ElMessage.error('Algo salio mal.'); console.error(err); } }
    finally { deleting.value = false; }
}
</script>

<template>
    <div class="income-table-section">
        <div class="table-toolbar">
            <PaginationWithNoMeta :pagination="loans" class="py-2" />
            <el-tooltip content="Eliminar seleccionados" placement="top">
                <el-button :disabled="disableMassiveActions" :icon="Delete" :loading="deleting" circle size="small" type="danger" @click="deleteSelections" />
            </el-tooltip>
        </div>
        <el-table ref="tableRef" :data="loans.data" max-height="600" style="width:100%" @selection-change="handleSelectionChange" :row-class-name="tableRowClassName">
            <el-table-column type="selection" width="40" />
            <el-table-column prop="lender_name" label="Prestamista" min-width="140" show-overflow-tooltip />
            <el-table-column label="Fecha" width="140"><template #default="{row}"><span class="text-sm text-gray-600 dark:text-gray-400">{{ formatDate(row.loan_date) }}</span></template></el-table-column>
            <el-table-column label="Monto" width="120" align="right"><template #default="{row}"><span class="amount-cell" style="color:#D64545;">${{ formatMoney(row.amount) }}</span></template></el-table-column>
            <el-table-column prop="profitability_type" label="Interes" width="120"><template #default="{row}">{{ row.profitability_type ?? '-' }}</template></el-table-column>
            <el-table-column label="Pendiente" width="120" align="right"><template #default="{row}"><span class="amount-cell" :style="getRemaining(row) === 0 ? 'color:#2F9E5B;' : 'color:#D99A2B;'">${{ formatMoney(getRemaining(row)) }}</span></template></el-table-column>
            <el-table-column label="Vence" width="120"><template #default="{row}"><span class="text-sm text-gray-600 dark:text-gray-400">{{ formatDate(row.expired_date) ?? '-' }}</span></template></el-table-column>
            <el-table-column align="right" width="60" fixed="right">
                <template #default="{row}">
                    <el-dropdown trigger="click" @command="handleCommand">
                        <button class="row-actions-btn" @click.stop><el-icon :size="16"><MoreFilled /></el-icon></button>
                        <template #dropdown><el-dropdown-menu><el-dropdown-item :command="'show-'+row.id">Ver</el-dropdown-item><el-dropdown-item :command="'edit-'+row.id">Editar</el-dropdown-item></el-dropdown-menu></template>
                    </el-dropdown>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>
