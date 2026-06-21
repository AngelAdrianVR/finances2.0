<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Edit, Delete, MoreFilled, Refresh, VideoPause, VideoPlay } from '@element-plus/icons-vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { ElMessage, ElMessageBox } from 'element-plus';
import PaginationWithNoMeta from '@/Components/MyComponents/PaginationWithNoMeta.vue';
import axios from 'axios';

const props = defineProps({ recurring_outcomes: Object });

const tableRef = ref(null);
const selectedRows = ref([]);
const disableMassiveActions = ref(true);
const showDetail = ref(false);
const itemToShow = ref(null);

function formatDate(date) { return date ? format(new Date(date), 'dd MMMM yyyy', { locale: es }) : ''; }
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
    <div class="income-table-section">
        <div class="table-toolbar">
            <PaginationWithNoMeta :pagination="recurring_outcomes" class="py-2" />
            <el-tooltip content="Eliminar seleccionados" placement="top">
                <el-button :disabled="disableMassiveActions" :icon="Delete" circle size="small" type="danger" @click="deleteSelections" />
            </el-tooltip>
        </div>

        <el-table ref="tableRef" :data="recurring_outcomes.data" max-height="600" style="width: 100%" @selection-change="handleSelectionChange" @row-click="handleRowClick" :row-class-name="tableRowClassName">
            <el-table-column type="selection" width="40" />
            <el-table-column prop="concept" label="Concepto" min-width="180" show-overflow-tooltip />
            <el-table-column label="Monto" width="140" align="right">
                <template #default="{ row }"><span class="amount-cell" style="color: #D64545;">${{ formatMoney(row.amount) }}</span></template>
            </el-table-column>
            <el-table-column prop="periodicity" label="Recurrencia" width="140" />
            <el-table-column prop="category" label="Categoria" width="140" />
            <el-table-column prop="payment_method" label="Metodo" width="130"><template #default="{ row }">{{ row.payment_method ?? '-' }}</template></el-table-column>
            <el-table-column label="Creado" width="170"><template #default="{ row }"><span class="text-sm text-gray-600 dark:text-gray-400">{{ formatDate(row.created_at) }}</span></template></el-table-column>
            <el-table-column align="right" width="60" fixed="right">
                <template #default="{ row }">
                    <el-dropdown trigger="click" @command="handleCommand">
                        <button class="row-actions-btn" @click.stop><el-icon :size="16"><MoreFilled /></el-icon></button>
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
    </div>

    <el-dialog v-model="showDetail" title="Detalles del gasto recurrente" width="500px" destroy-on-close>
        <template v-if="itemToShow">
            <div class="space-y-2">
                <div class="detail-row"><span class="detail-label">Concepto</span><span class="detail-value">{{ itemToShow.concept }}</span></div>
                <div class="detail-row"><span class="detail-label">Fecha de creacion</span><span class="detail-value">{{ formatDate(itemToShow.created_at) }}</span></div>
                <div class="detail-row"><span class="detail-label">Monto</span><span class="detail-value font-mono font-semibold" style="color: #D64545;">${{ formatMoney(itemToShow.amount) }}</span></div>
                <div class="detail-row"><span class="detail-label">Recurrencia</span><span class="detail-value">{{ itemToShow.periodicity }}</span></div>
                <div class="detail-row"><span class="detail-label">Categoria</span><span class="detail-value">{{ itemToShow.category }}</span></div>
                <div class="detail-row"><span class="detail-label">Metodo de pago</span><span class="detail-value">{{ itemToShow.payment_method }}</span></div>
                <div class="detail-row"><span class="detail-label">Descripcion</span><span class="detail-value">{{ itemToShow.description ?? '-' }}</span></div>
                <div class="detail-row"><span class="detail-label">Estado</span>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium" :class="itemToShow.is_active ? 'bg-[#EAF7EF] text-[#247A47]' : 'bg-[#FCEAEA] text-[#B23636]'">{{ itemToShow.is_active ? 'Activo' : 'Inactivo' }}</span>
                </div>
            </div>
        </template>
        <template #footer>
            <el-button @click="router.get(route('recurring-outcomes.edit', itemToShow?.id))" v-if="itemToShow">Editar</el-button>
            <el-button @click="showDetail = false">Cerrar</el-button>
        </template>
    </el-dialog>
</template>
