<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Plus, Delete, Edit, Check, Close, Coin, TrendCharts } from '@element-plus/icons-vue';

const emit = defineEmits(['changed']);

const investments = ref([]);
const loading = ref(true);

const form = ref({
    name: '',
    amount: null,
    annual_rate: 0,
});

const editingId = ref(null);
const editForm = ref({
    name: '',
    amount: 0,
    annual_rate: 0,
});

const totalInvested = computed(() =>
    investments.value.reduce((sum, investment) => sum + Number(investment.amount || 0), 0)
);

const weightedRate = computed(() => {
    if (totalInvested.value === 0) return 0;
    const weighted = investments.value.reduce(
        (sum, investment) =>
            sum + Number(investment.amount || 0) * Number(investment.annual_rate || 0),
        0
    );
    return weighted / totalInvested.value;
});

const expectedYearlyReturn = computed(() => (totalInvested.value * weightedRate.value) / 100);

function formatMoney(value) {
    return Number(value || 0).toLocaleString('en-US', {
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
            form.value = { name: '', amount: null, annual_rate: 0 };
            await fetchInvestments();
            emit('changed');
        }
    } catch (error) {
        console.error(error);
        ElMessage.error(error.response?.data?.message || 'Error al registrar la inversión.');
    }
}

function startEdit(investment) {
    editingId.value = investment.id;
    editForm.value = {
        name: investment.name || '',
        amount: investment.amount,
        annual_rate: investment.annual_rate,
    };
}

function cancelEdit() {
    editingId.value = null;
    editForm.value = { name: '', amount: 0, annual_rate: 0 };
}

async function saveEdit(investment) {
    try {
        const response = await axios.put(route('investments.update', investment.id), {
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

async function removeInvestment(investment) {
    try {
        await ElMessageBox.confirm(
            '¿Estás seguro de eliminar esta inversión?',
            'Confirmar eliminación',
            { confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar', type: 'warning' }
        );
        const response = await axios.delete(route('investments.destroy', investment.id));
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
        <div class="dashboard-card__head">
            <div>
                <h2 class="dashboard-card__title">Portafolio de inversiones</h2>
                <p class="dashboard-card__subtitle">
                    Registra cada instrumento con su monto y tasa anual para conocer tu capital total,
                    la tasa promedio ponderada y el rendimiento que puedes esperar cada año.
                </p>
            </div>
            <span class="dashboard-chip">
                {{ investments.length }} {{ investments.length === 1 ? 'instrumento' : 'instrumentos' }}
            </span>
        </div>

        <el-form label-position="top" class="investment-form" @submit.prevent="addInvestment">
            <div class="investment-form__grid">
                <el-form-item label="Nombre del instrumento">
                    <el-input
                        v-model="form.name"
                        placeholder="Ej. CETES, Nu, GBM"
                        clearable
                    />
                </el-form-item>

                <el-form-item label="Monto invertido (MXN)">
                    <el-input-number
                        v-model="form.amount"
                        :min="0.01"
                        :step="100"
                        :precision="2"
                        controls-position="right"
                        class="!w-full"
                    />
                </el-form-item>

                <el-form-item label="Tasa anual (%)">
                    <el-input-number
                        v-model="form.annual_rate"
                        :min="0"
                        :max="100"
                        :step="0.5"
                        :precision="2"
                        controls-position="right"
                        class="!w-full"
                    />
                </el-form-item>

                <div class="investment-form__action">
                    <el-button type="primary" :icon="Plus" native-type="submit" class="!w-full">
                        Agregar
                    </el-button>
                </div>
            </div>
        </el-form>

        <div class="investment-summary">
            <div class="investment-summary__item">
                <span class="investment-summary__icon investment-summary__icon--primary">
                    <el-icon :size="18"><Coin /></el-icon>
                </span>
                <div class="min-w-0">
                    <p class="investment-summary__label">Total invertido</p>
                    <p class="investment-summary__value">${{ formatMoney(totalInvested) }}</p>
                </div>
            </div>

            <div class="investment-summary__item">
                <span class="investment-summary__icon investment-summary__icon--info">
                    <el-icon :size="18"><TrendCharts /></el-icon>
                </span>
                <div class="min-w-0">
                    <p class="investment-summary__label">Tasa anual ponderada</p>
                    <p class="investment-summary__value">{{ weightedRate.toFixed(2) }}%</p>
                </div>
            </div>

            <div class="investment-summary__item">
                <span class="investment-summary__icon investment-summary__icon--success">
                    <el-icon :size="18"><Coin /></el-icon>
                </span>
                <div class="min-w-0">
                    <p class="investment-summary__label">Rendimiento anual estimado</p>
                    <p class="investment-summary__value investment-summary__value--income">
                        ${{ formatMoney(expectedYearlyReturn) }}
                    </p>
                </div>
            </div>
        </div>

        <div v-if="loading" class="space-y-2">
            <el-skeleton animated :count="2">
                <template #template>
                    <el-skeleton-item variant="rect" style="width: 100%; height: 56px; border-radius: 8px;" />
                </template>
            </el-skeleton>
        </div>

        <div v-else-if="investments.length === 0" class="empty-state py-6">
            <el-icon :size="32" class="empty-state-icon"><Coin /></el-icon>
            <p class="empty-state-title">Aún no tienes inversiones</p>
            <p class="empty-state-text">
                Registra tu primera inversión para ver tu capital y la proyección de rendimientos.
            </p>
        </div>

        <ul v-else class="investment-list">
            <li v-for="investment in investments" :key="investment.id" class="investment-item">
                <template v-if="editingId === investment.id">
                    <div class="investment-item__edit">
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
                            <el-button size="small" type="success" :icon="Check" circle @click="saveEdit(investment)" />
                            <el-button size="small" :icon="Close" circle @click="cancelEdit" />
                        </div>
                    </div>
                </template>

                <template v-else>
                    <div class="investment-item__main">
                        <span class="investment-item__icon">
                            <el-icon :size="17"><Coin /></el-icon>
                        </span>
                        <div class="min-w-0">
                            <p class="investment-item__name">{{ investment.name || 'Inversión' }}</p>
                            <span class="investment-item__chip">
                                {{ Number(investment.annual_rate).toFixed(2) }}% anual
                            </span>
                        </div>
                    </div>

                    <div class="investment-item__side">
                        <p class="investment-item__amount">${{ formatMoney(investment.amount) }}</p>
                        <div class="flex gap-1">
                            <el-button size="small" :icon="Edit" circle plain @click="startEdit(investment)" />
                            <el-button
                                size="small"
                                :icon="Delete"
                                circle
                                plain
                                type="danger"
                                @click="removeInvestment(investment)"
                            />
                        </div>
                    </div>
                </template>
            </li>
        </ul>
    </div>
</template>
