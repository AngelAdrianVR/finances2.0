<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import { InfoFilled } from '@element-plus/icons-vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Back from '@/Components/MyComponents/Back.vue';
import axios from 'axios';

const props = defineProps({ outcome: Object });

const form = useForm({
    amount: props.outcome.amount, category: props.outcome.category, concept: props.outcome.concept,
    created_at: props.outcome.created_at, payment_method: props.outcome.payment_method,
    is_recurring_outcome: false, periodicity: null, description: props.outcome.description,
    split_enabled: props.outcome.split_enabled || false,
    split_with: props.outcome.split_with || [],
});

const shortcuts = [
    { text: 'Hoy', value: new Date() },
    { text: 'Ayer', value: () => { const d = new Date(); d.setTime(d.getTime() - 86400000); return d; } },
    { text: 'Hace una semana', value: () => { const d = new Date(); d.setTime(d.getTime() - 86400000 * 7); return d; } },
];

const categories = ['Transporte', 'Compras', 'Salud y bienestar', 'Entretenimiento', 'Alimentos y bebidas', 'Servicios', 'Educacion y desarrollo personal', 'Otro'];
const paymentMethods = ['Efectivo', 'Transferencia', 'Deposito', 'Cheque'];
const periodicities = ['Todos los dias', 'Semanal', 'Mensual', 'Anual'];

// Linked users for split
const linkedUsers = ref([]);

async function fetchLinkedUsers() {
    try {
        const response = await axios.get(route('outcomes.get-linked-users'));
        linkedUsers.value = response.data.linked_users;
    } catch (error) {
        console.error(error);
    }
}

onMounted(() => fetchLinkedUsers());

const splitAmount = computed(() => {
    if (!form.split_enabled || !form.split_with?.length || !form.amount) return null;
    const totalPeople = form.split_with.length + 1;
    return form.amount / totalPeople;
});

watch(() => form.is_recurring_outcome, (val) => { if (!val) form.periodicity = null; });

function update() {
    form.put(route('outcomes.update', props.outcome.id), {
        onSuccess: () => ElMessage.success('Gasto editado correctamente.'),
    });
}
</script>

<template>
    <AppLayout title="Editar gasto">
        <main class="py-6 px-4 md:px-8 max-w-2xl mx-auto">
            <Back />
            <div class="form-card mt-3">
                <h1 class="form-title">Editar gasto</h1>
                <el-form :model="form" label-position="top" @submit.prevent="update">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <el-form-item label="Concepto" required>
                            <el-input v-model="form.concept" maxlength="50" placeholder="Ej. Compra de supermercado" show-word-limit />
                            <InputError :message="form.errors.concept" />
                        </el-form-item>
                        <el-form-item label="Fecha" required>
                            <el-date-picker v-model="form.created_at" type="date" placeholder="Selecciona la fecha" :shortcuts="shortcuts" class="!w-full" />
                            <InputError :message="form.errors.created_at" />
                        </el-form-item>
                        <el-form-item label="Monto" required>
                            <el-input v-model="form.amount" placeholder="Ej. 500"
                                :formatter="(v) => `${v}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                :parser="(v) => v.replace(/\$\s?|(,*)/g, '')">
                                <template #prepend>$</template>
                            </el-input>
                            <InputError :message="form.errors.amount" />
                        </el-form-item>
                        <el-form-item label="Categoria">
                            <el-select v-model="form.category" filterable placeholder="Seleccione" class="!w-full">
                                <el-option v-for="cat in categories" :key="cat" :label="cat" :value="cat" />
                            </el-select>
                            <InputError :message="form.errors.category" />
                        </el-form-item>
                        <el-form-item label="Metodo de pago">
                            <el-select v-model="form.payment_method" filterable placeholder="Seleccione" class="!w-full">
                                <el-option v-for="pm in paymentMethods" :key="pm" :label="pm" :value="pm" />
                            </el-select>
                            <InputError :message="form.errors.payment_method" />
                        </el-form-item>
                    </div>

                    <!-- Split expense -->
                    <div v-if="linkedUsers.length" class="mt-4 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <el-checkbox v-model="form.split_enabled" label="Dividir gasto en partes iguales" size="small" />
                            <el-tooltip content="El monto se dividira en partes iguales entre ti y los usuarios seleccionados" placement="right">
                                <el-icon :size="14" color="#296A6B"><InfoFilled /></el-icon>
                            </el-tooltip>
                        </div>
                        <div v-if="form.split_enabled">
                            <el-form-item label="Dividir con:">
                                <el-select v-model="form.split_with" filterable multiple placeholder="Selecciona usuarios vinculados" class="!w-full">
                                    <el-option v-for="user in linkedUsers" :key="user.id" :label="user.name" :value="user.id">
                                        <div class="flex items-center gap-2">
                                            <img :src="user.profile_photo_url" class="size-5 rounded-full" />
                                            <span>{{ user.name }}</span>
                                            <span class="text-xs text-gray-400">{{ user.email }}</span>
                                        </div>
                                    </el-option>
                                </el-select>
                            </el-form-item>
                            <div v-if="splitAmount && form.split_with?.length" class="flex items-center gap-2 mt-2 text-sm">
                                <span class="text-gray-500 dark:text-gray-400">
                                    {{ form.split_with.length + 1 }} personas:
                                </span>
                                <span class="font-mono font-semibold text-[#D64545] dark:text-[#F05A5A]">
                                    ${{ splitAmount.toFixed(2) }} c/u
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 my-4">
                        <el-checkbox v-model="form.is_recurring_outcome" label="Gasto recurrente" border size="small" />
                        <el-tooltip content="Selecciona esta opcion para registrar el gasto automaticamente" placement="right">
                            <el-icon color="#296A6B" :size="16"><InfoFilled /></el-icon>
                        </el-tooltip>
                    </div>
                    <el-form-item v-if="form.is_recurring_outcome" label="Recurrencia del gasto" required>
                        <el-select v-model="form.periodicity" filterable placeholder="Seleccione" class="!w-full">
                            <el-option v-for="p in periodicities" :key="p" :label="p" :value="p" />
                        </el-select>
                        <InputError :message="form.errors.periodicity" />
                    </el-form-item>
                    <el-form-item label="Descripcion">
                        <el-input v-model="form.description" maxlength="255" placeholder="Descripcion del gasto (opcional)" show-word-limit type="textarea" :rows="3" />
                        <InputError :message="form.errors.description" />
                    </el-form-item>
                    <div class="flex justify-end mt-6">
                        <el-button type="danger" :loading="form.processing" @click="update">Guardar cambios</el-button>
                    </div>
                </el-form>
            </div>
        </main>
    </AppLayout>
</template>
