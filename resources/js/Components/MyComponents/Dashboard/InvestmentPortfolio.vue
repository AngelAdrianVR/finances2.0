<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Plus, Delete, Edit, Check, Close } from '@element-plus/icons-vue';

const emit = defineEmits(['changed']);

const investments = ref([]);
const loading = ref(true);

// Formulario de nueva inversión
const form = ref({
    name: '',
    amount: 0,
    annual_rate: 0,
});

// Edición inline
const editingId = ref(null);
const editForm = ref({
    name: '',
    amount: 0,
    annual_rate: 0,
});

const totalInvested = computed(() =>
    investments.value.reduce((sum, inv) => sum + Number(inv.amount || 0), 0)
);

const weightedRate = computed(() => {
    if (totalInvested.value === 0) return 0;
    const weighted = investments.value.reduce(
        (sum, inv) => sum + Number(inv.amount || 0) * Number(inv.annual_rate || 0),
        0
    );
    return weighted / totalInvested.value;
});

function formatMoney(val) {
    return Number(val || 0).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

async function fetchInvestments() {
    try {
        loading.value = true;
        const response = await axios.get(route('investments.index'));
        if (response.status === 200) {
            investments.value = response.data.investments || [];
        }
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
}

async function addInvestment() {
    if (!form.value.amount || form.value.amount <= 0) {
        ElMessage.warning('Ingresa un monto mayor a 0.');
        return;
    }
    if (form.value.annual_rate === null || form.value.annual_rate === undefined || form.value.annual_rate < 0) {
        ElMessage.warning('Ingresa una tasa anual válida.');
        return;
    }
    try {
        const response = await axios.post(route('investments.store'), {
            name: form.value.name || null,
            amount: form.value.amount,
            annual_rate: form.value.annual_rate,
        });
        if (response.status === 200 || response.status === 201) {
            ElMessage.success(response.data.message || 'Inversión registrada.');
            form.value = { name: '', amount: 0, annual_rate: 0 };
            await fetchInvestments();
            emit('changed');
        }
    } catch (error) {
        console.error(error);
        ElMessage.error(error.response?.data?.message || 'Error al registrar la inversión.');
    }
}

function startEdit(inv) {
    editingId.value = inv.id;
    editForm.value = {
        name: inv.name || '',
        amount: inv.amount,
        annual_rate: inv.annual_rate,
    };
}

function cancelEdit() {
    editingId.value = null;
    editForm.value = { name: '', amount: 0, annual_rate: 0 };
}

async function saveEdit(inv) {
    try {
        const response = await axios.put(route('investments.update', inv.id), {
            name: editForm.value.name || null,
            amount: editForm.value.amount,
            annual_rate: editForm.value.annual_rate,
        });
        if (response.status === 200) {
            ElMessage.success(response.data.message || 'Inversión actualizada.');
            editingId.value = null;
            await fetchInvestments();
            emit('changed');
        }
    } catch (error) {
        console.error(error);
        ElMessage.error(error.response?.data?.message || 'Error al actualizar la inversión.');
    }
}

async function removeInvestment(inv) {
    try {
        await ElMessageBox.confirm(
            '¿Estás seguro de eliminar esta inversión?',
            'Confirmar eliminación',
            { confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar', type: 'warning' }
        );
        const response = await axios.delete(route('investments.destroy', inv.id));
        if (response.status === 200) {
            ElMessage.success(response.data.message || 'Inversión eliminada.');
            await fetchInvestments();
            emit('changed');
        }
    } catch (error) {
        if (error !== 'cancel') console.error(error);
    }
}

onMounted(fetchInvestments);
</script>

<template>
    <div class="dashboard-card">
        <h2 class="dashboard-card-title">Portafolio de Inversiones</h2>

        <!-- Formulario para agregar -->
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-4 space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <el-input
                    v-model="form.name"
                    placeholder="Nombre (opcional), ej. CETES, Nu, GBM"
                    clearable
                />
                <div class="grid grid-cols-2 gap-2">
                    <el-input-number
                        v-model="form.amount"
                        :min="0.01"
                        :step="100"
                        :precision="2"
                        controls-position="right"
                        placeholder="Monto"
                        class="!w-full"
                    />
                    <el-input-number
                        v-model="form.annual_rate"
                        :min="0"
                        :max="100"
                        :step="0.5"
                        :precision="2"
                        controls-position="right"
                        placeholder="Tasa %"
                        class="!w-full"
                    />
                </div>
            </div>
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Agrega cada inversión con su monto y tasa anual (%).
                </p>
                <el-button type="primary" :icon="Plus" @click="addInvestment">
                    Agregar
                </el-button>
            </div>
        </div>

        <!-- Resumen -->
        <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total invertido</p>
                <p class="font-mono text-lg font-bold text-[#296A6B] dark:text-primary-400">
                    ${{ formatMoney(totalInvested) }}
                </p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tasa anual ponderada</p>
                <p class="font-mono text-lg font-bold text-[#296A6B] dark:text-primary-400">
                    {{ weightedRate.toFixed(2) }}%
                </p>
            </div>
        </div>

        <!-- Lista / tabla -->
        <div v-if="loading" class="space-y-2">
            <el-skeleton animated :count="2">
                <template #template>
                    <el-skeleton-item variant="rect" style="width: 100%; height: 48px; border-radius: 8px;" />
                </template>
            </el-skeleton>
        </div>

        <div v-else-if="investments.length === 0" class="empty-state py-6">
            <p class="empty-state-title">Aún no tienes inversiones</p>
            <p class="empty-state-text text-center">
                Registra tu primera inversión para ver tu proyección a largo plazo.
            </p>
        </div>

        <div v-else class="space-y-2">
            <div
                v-for="inv in investments"
                :key="inv.id"
                class="border border-grayD9 dark:border-gray-600 rounded-lg p-3 flex items-center justify-between gap-3 hover:shadow-sm transition"
            >
                <!-- Modo edición -->
                <template v-if="editingId === inv.id">
                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-[1fr_110px_110px_auto] gap-2 items-center">
                        <el-input v-model="editForm.name" placeholder="Nombre" size="small" />
                        <el-input-number
                            v-model="editForm.amount"
                            :min="0.01"
                            :precision="2"
                            size="small"
                            controls-position="right"
                            class="!w-full"
                        />
                        <el-input-number
                            v-model="editForm.annual_rate"
                            :min="0"
                            :max="100"
                            :precision="2"
                            size="small"
                            controls-position="right"
                            class="!w-full"
                        />
                        <div class="flex gap-1">
                            <el-button size="small" type="success" :icon="Check" circle @click="saveEdit(inv)" />
                            <el-button size="small" :icon="Close" circle @click="cancelEdit" />
                        </div>
                    </div>
                </template>

                <!-- Modo lectura -->
                <template v-else>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-sm text-gray-800 dark:text-gray-200 truncate">
                            {{ inv.name || 'Inversión' }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ Number(inv.annual_rate).toFixed(2) }}% anual
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <p class="font-mono font-semibold text-gray-800 dark:text-gray-200">
                            ${{ formatMoney(inv.amount) }}
                        </p>
                        <el-button size="small" :icon="Edit" circle plain @click="startEdit(inv)" />
                        <el-button size="small" :icon="Delete" circle plain type="danger" @click="removeInvestment(inv)" />
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>