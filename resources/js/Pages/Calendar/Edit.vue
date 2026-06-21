<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Back from '@/Components/MyComponents/Back.vue';

const props = defineProps({ calendar: Object });

const form = useForm({
    type: props.calendar.type, title: props.calendar.title, date: props.calendar.date,
    amount: props.calendar.amount, category: props.calendar.category, description: props.calendar.description,
    periodicity: props.calendar.periodicity, payment_method: props.calendar.payment_method,
});

const remainType = ref(props.calendar.type);
const options = ['Ingreso recurrente', 'Gasto fijo'];
const paymentMethods = ['Transferencia', 'Deposito', 'Cheque', 'Efectivo'];
const periodicities = ['Todos los dias', 'Semanal', 'Mensual', 'Anual'];

const categories = [
    { label: 'Nomina', description: '(Salarios o sueldos fijos)' },
    { label: 'Renta de propiedad', description: '(Ingresos por alquiler)' },
    { label: 'Regalias', description: '(Derechos de autor, musica, libros, etc.)' },
    { label: 'Dividendos', description: '(Ganancias por acciones o inversiones)' },
    { label: 'Pension', description: '(Ingresos por jubilacion o retiro)' },
    { label: 'Contratos de servicios', description: '(Mantenimiento o soporte tecnico)' },
    { label: 'Servicios', description: '(Agua, luz, internet, etc.)' },
    { label: 'Alimentos y bebidas', description: '(Compras de despensa y comida)' },
    { label: 'Transporte', description: '(Gasolina, transporte publico, etc.)' },
    { label: 'Salud y bienestar', description: '(Medicinas, consultas, etc.)' },
    { label: 'Educacion', description: '(Cursos, colegiaturas, etc.)' },
    { label: 'Entretenimiento', description: '(Streaming, cine, etc.)' },
    { label: 'Otro', description: '' },
];

function changeType() { form.type = remainType.value; }

function update() {
    form.put(route('calendars.update', props.calendar.id), { onSuccess: () => ElMessage.success('Recordatorio editado.') });
}
</script>

<template>
    <AppLayout title="Editar recordatorio">
        <main class="py-6 px-4 md:px-8 max-w-2xl mx-auto">
            <Back />
            <div class="form-card mt-3">
                <h1 class="form-title">Editar recordatorio</h1>
                <div class="flex items-center justify-between mb-6">
                    <el-collapse accordion class="flex-1 mr-4">
                        <el-collapse-item :title="remainType" :name="1">
                            <template #title><span class="text-[#296A6B] font-bold text-base">{{ remainType }}</span></template>
                            <p v-if="remainType === 'Ingreso recurrente'" class="text-sm text-gray-600 dark:text-gray-400">Los ingresos recurrentes se registran automaticamente en el sistema.</p>
                            <p v-else class="text-sm text-gray-600 dark:text-gray-400">Los gastos fijos se registran automaticamente en el sistema.</p>
                        </el-collapse-item>
                    </el-collapse>
                    <el-segmented v-model="remainType" :options="options" block @change="changeType" />
                </div>

                <el-form :model="form" label-position="top" @submit.prevent="update">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <el-form-item :label="remainType === 'Ingreso recurrente' ? 'Nombre del ingreso' : 'Nombre del gasto'" required class="md:col-span-2">
                            <el-input v-model="form.title" maxlength="80" show-word-limit />
                            <InputError :message="form.errors.title" />
                        </el-form-item>
                        <el-form-item label="Fecha de inicio" required>
                            <el-date-picker v-model="form.date" type="date" class="!w-full" />
                            <InputError :message="form.errors.date" />
                        </el-form-item>
                        <el-form-item label="Monto" required>
                            <el-input v-model="form.amount" :formatter="(v) => `${v}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')" :parser="(v) => v.replace(/\$\s?|(,*)/g, '')"><template #prepend>$</template></el-input>
                            <InputError :message="form.errors.amount" />
                        </el-form-item>
                        <el-form-item label="Categoria">
                            <el-select v-model="form.category" filterable class="!w-full"><el-option v-for="cat in categories" :key="cat.label" :label="cat.label" :value="cat.label"><span>{{ cat.label }}</span><span class="text-gray-400 text-xs ml-2">{{ cat.description }}</span></el-option></el-select>
                            <InputError :message="form.errors.category" />
                        </el-form-item>
                        <el-form-item label="Metodo de pago">
                            <el-select v-model="form.payment_method" filterable class="!w-full"><el-option v-for="pm in paymentMethods" :key="pm" :label="pm" :value="pm" /></el-select>
                            <InputError :message="form.errors.payment_method" />
                        </el-form-item>
                        <el-form-item label="Frecuencia" required>
                            <el-select v-model="form.periodicity" filterable class="!w-full"><el-option v-for="p in periodicities" :key="p" :label="p" :value="p" /></el-select>
                            <InputError :message="form.errors.periodicity" />
                        </el-form-item>
                    </div>
                    <el-form-item label="Descripcion">
                        <el-input v-model="form.description" maxlength="255" type="textarea" :rows="3" show-word-limit />
                        <InputError :message="form.errors.description" />
                    </el-form-item>
                    <div class="flex justify-end mt-6">
                        <el-button type="primary" :loading="form.processing" @click="update">Guardar cambios</el-button>
                    </div>
                </el-form>
            </div>
        </main>
    </AppLayout>
</template>
