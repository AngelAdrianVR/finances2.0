<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Edit, Delete, MoreFilled, Refresh } from '@element-plus/icons-vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { ElMessage, ElMessageBox } from 'element-plus';
import { useForm } from '@inertiajs/vue3';
import PaginationWithNoMeta from '@/Components/MyComponents/PaginationWithNoMeta.vue';
import axios from 'axios';

const props = defineProps({
    incomes: Object,
});

// Table ref
const tableRef = ref(null);
const selectedRows = ref([]);
const disableMassiveActions = ref(true);

// Detail modal
const showDetail = ref(false);
const itemToShow = ref(null);

// Massive edit modal
const showMassiveEdit = ref(false);

const form = useForm({
    category: null,
    payment_method: null,
    selections: [],
});

const categories = ['Ventas', 'Intereses', 'Nomina', 'Prestacion de servicios', 'Comision', 'Renta', 'Otro'];
const paymentMethods = ['Efectivo', 'Transferencia', 'Deposito', 'Cheque'];

function formatDate(date) {
    if (!date) return '';
    return format(new Date(date), 'dd MMMM yyyy', { locale: es });
}

function formatMoney(val) {
    return val?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
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
            'Estas seguro que deseas eliminar los ingresos seleccionados?',
            'Confirmar eliminacion',
            { confirmButtonText: 'Si, eliminar', cancelButtonText: 'Cancelar', type: 'warning' }
        );

        const response = await axios.post(route('incomes.massive-delete'), {
            incomes: selectedRows.value,
        });

        if (response.status === 200) {
            ElMessage.success('Ingresos eliminados correctamente.');
            const deletedIds = new Set(selectedRows.value.map(r => r.id));
            props.incomes.data = props.incomes.data.filter(d => !deletedIds.has(d.id));
            tableRef.value?.clearSelection();
            selectedRows.value = [];
            disableMassiveActions.value = true;
        }
    } catch (err) {
        if (err !== 'cancel') {
            ElMessage.error('Algo salio mal.');
            console.error(err);
        }
    }
}

function tableRowClassName({ rowIndex }) {
    return 'cursor-pointer';
}
</script>

<template>
    <div class="income-table-section">
        <!-- Toolbar -->
        <div class="table-toolbar">
            <PaginationWithNoMeta :pagination="incomes" class="py-2" />

            <div class="flex items-center gap-2">
                <el-tooltip content="Editar seleccionados" placement="top">
                    <el-button
                        :disabled="disableMassiveActions"
                        :icon="Edit"
                        circle
                        size="small"
                        @click="openMassiveEdit"
                    />
                </el-tooltip>
                <el-tooltip content="Eliminar seleccionados" placement="top">
                    <el-button
                        :disabled="disableMassiveActions"
                        :icon="Delete"
                        circle
                        size="small"
                        type="danger"
                        @click="deleteSelections"
                    />
                </el-tooltip>
            </div>
        </div>

        <!-- Table -->
        <el-table
            ref="tableRef"
            :data="incomes.data"
            max-height="600"
            style="width: 100%"
            @selection-change="handleSelectionChange"
            @row-click="handleRowClick"
            :row-class-name="tableRowClassName"
        >
            <el-table-column type="selection" width="40" />
            <el-table-column prop="concept" label="Concepto" min-width="200" show-overflow-tooltip />
            <el-table-column label="Fecha" width="180">
                <template #default="{ row }">
                    <span class="text-sm text-gray-600 dark:text-gray-400 dark:text-gray-400">{{ formatDate(row.created_at) }}</span>
                </template>
            </el-table-column>
            <el-table-column label="Monto" width="150" align="right">
                <template #default="{ row }">
                    <span class="amount-cell">
                        <el-tooltip v-if="row.automatically_created" content="Creado automaticamente" placement="top">
                            <el-icon :size="14" color="#2F9E5B"><Refresh /></el-icon>
                        </el-tooltip>
                        ${{ formatMoney(row.amount) }}
                    </span>
                </template>
            </el-table-column>
            <el-table-column prop="category" label="Categoria" width="150" />
            <el-table-column prop="payment_method" label="Metodo de pago" width="150">
                <template #default="{ row }">
                    {{ row.payment_method ?? '-' }}
                </template>
            </el-table-column>
            <el-table-column align="right" width="60" fixed="right">
                <template #default="{ row }">
                    <el-dropdown trigger="click" @command="handleCommand">
                        <button class="row-actions-btn" @click.stop>
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
        </el-table>
    </div>

    <!-- Detail dialog -->
    <el-dialog v-model="showDetail" title="Detalles del ingreso" width="500px" destroy-on-close>
        <template v-if="itemToShow">
            <div class="space-y-2">
                <div class="detail-row"><span class="detail-label">Concepto</span><span class="detail-value">{{ itemToShow.concept }}</span></div>
                <div class="detail-row"><span class="detail-label">Fecha</span><span class="detail-value">{{ formatDate(itemToShow.created_at) }}</span></div>
                <div class="detail-row"><span class="detail-label">Monto</span><span class="detail-value font-mono font-semibold" style="color: #2F9E5B;">${{ formatMoney(itemToShow.amount) }}</span></div>
                <div class="detail-row"><span class="detail-label">Categoria</span><span class="detail-value">{{ itemToShow.category }}</span></div>
                <div class="detail-row"><span class="detail-label">Metodo de pago</span><span class="detail-value">{{ itemToShow.payment_method }}</span></div>
                <div class="detail-row"><span class="detail-label">Descripcion</span><span class="detail-value">{{ itemToShow.description ?? '-' }}</span></div>
            </div>
            <div v-if="itemToShow.automatically_created" class="mt-3">
                <span class="recurring-badge">
                    <el-icon :size="12"><Refresh /></el-icon>
                    Ingreso recurrente
                </span>
            </div>
        </template>
        <template #footer>
            <el-button @click="router.get(route('incomes.edit', itemToShow?.id))" v-if="itemToShow">Editar</el-button>
            <el-button @click="showDetail = false">Cerrar</el-button>
        </template>
    </el-dialog>

    <!-- Massive edit dialog -->
    <el-dialog v-model="showMassiveEdit" title="Editar ingresos masivamente" width="480px" destroy-on-close>
        <el-form :model="form" label-position="top">
            <el-form-item label="Categoria">
                <el-select v-model="form.category" filterable placeholder="Selecciona" class="!w-full">
                    <el-option v-for="cat in categories" :key="cat" :label="cat" :value="cat" />
                </el-select>
            </el-form-item>
            <el-form-item label="Metodo de pago">
                <el-select v-model="form.payment_method" filterable placeholder="Selecciona" class="!w-full">
                    <el-option v-for="pm in paymentMethods" :key="pm" :label="pm" :value="pm" />
                </el-select>
            </el-form-item>
        </el-form>
        <div class="massive-edit-hint">
            Todos los ingresos seleccionados <strong>({{ selectedRows.length }})</strong> se actualizaran con los valores establecidos.
        </div>
        <template #footer>
            <el-button @click="showMassiveEdit = false">Cancelar</el-button>
            <el-button type="primary" :disabled="!form.category || !form.payment_method || form.processing" :loading="form.processing" @click="massiveUpdate">
                Guardar cambios
            </el-button>
        </template>
    </el-dialog>
</template>
