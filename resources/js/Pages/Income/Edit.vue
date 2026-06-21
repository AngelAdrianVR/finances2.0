<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { InfoFilled } from '@element-plus/icons-vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Back from '@/Components/MyComponents/Back.vue';

const props = defineProps({
    income: Object,
});

const form = useForm({
    amount: props.income.amount,
    category: props.income.category,
    concept: props.income.concept,
    created_at: props.income.created_at,
    payment_method: props.income.payment_method,
    is_recurring_income: false,
    periodicity: null,
    description: props.income.description,
});

const shortcuts = [
    { text: 'Hoy', value: new Date() },
    { text: 'Ayer', value: () => { const d = new Date(); d.setTime(d.getTime() - 86400000); return d; } },
    { text: 'Hace una semana', value: () => { const d = new Date(); d.setTime(d.getTime() - 86400000 * 7); return d; } },
];

const categories = ['Ventas', 'Intereses', 'Nómina', 'Prestación de servicios', 'Comision', 'Renta', 'Otro'];
const paymentMethods = ['Efectivo', 'Transferencia', 'Depósito', 'Cheque'];
const periodicities = ['Todos los días', 'Semanal', 'Mensual', 'Anual'];

watch(() => form.is_recurring_income, (val) => {
    if (!val) form.periodicity = null;
});

function update() {
    form.put(route('incomes.update', props.income.id), {
        onSuccess: () => {
            ElMessage.success('Ingreso editado correctamente.');
        },
    });
}
</script>

<template>
    <AppLayout title="Editar ingreso">
        <main class="py-6 px-4 md:px-8 max-w-2xl mx-auto">
            <Back />

            <div class="form-card mt-3">
                <h1 class="form-title">Editar ingreso</h1>

                <el-form :model="form" label-position="top" @submit.prevent="update">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <el-form-item label="Concepto" required>
                            <el-input v-model="form.concept" maxlength="50" placeholder="Ej. Venta de ropa" show-word-limit />
                            <InputError :message="form.errors.concept" />
                        </el-form-item>

                        <el-form-item label="Fecha" required>
                            <el-date-picker v-model="form.created_at" type="date" placeholder="Selecciona la fecha"
                                :shortcuts="shortcuts" class="!w-full" />
                            <InputError :message="form.errors.created_at" />
                        </el-form-item>

                        <el-form-item label="Monto" required>
                            <el-input v-model="form.amount" placeholder="Ej. 500"
                                :formatter="(v) => `${v}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                :parser="(v) => v.replace(/\$\s?|(,*)/g, '')"
                            >
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

                    <div class="flex items-center gap-2 my-4">
                        <el-checkbox v-model="form.is_recurring_income" label="Ingreso recurrente" border size="small" />
                        <el-tooltip content="Selecciona esta opcion para registrar el ingreso automaticamente" placement="right">
                            <el-icon color="#296A6B" :size="16"><InfoFilled /></el-icon>
                        </el-tooltip>
                    </div>

                    <el-form-item v-if="form.is_recurring_income" label="Recurrencia del ingreso" required>
                        <el-select v-model="form.periodicity" filterable placeholder="Seleccione" class="!w-full">
                            <el-option v-for="p in periodicities" :key="p" :label="p" :value="p" />
                        </el-select>
                        <InputError :message="form.errors.periodicity" />
                    </el-form-item>

                    <el-form-item label="Descripcion">
                        <el-input v-model="form.description" maxlength="255" placeholder="Descripcion del ingreso (opcional)"
                            show-word-limit type="textarea" :rows="3" />
                        <InputError :message="form.errors.description" />
                    </el-form-item>

                    <div class="flex justify-end mt-6">
                        <el-button type="primary" :loading="form.processing" @click="update">
                            Guardar cambios
                        </el-button>
                    </div>
                </el-form>
            </div>
        </main>
    </AppLayout>
</template>
