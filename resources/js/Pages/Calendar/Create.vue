<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Back from '@/Components/MyComponents/Back.vue';

const props = defineProps({ type: String });

const form = useForm({ type: props.type, title: null, date: null, amount: null, category: null, description: null, periodicity: null, payment_method: null });

const remaindType = ref(props.type);
const options = ['Ingreso recurrente', 'Gasto fijo'];
const paymentMethods = ['Transferencia', 'Deposito', 'Cheque', 'Efectivo'];
const periodicities = ['Todos los dias', 'Semanal', 'Mensual', 'Anual'];

const incomeCategories = [
    { label: 'Nomina', description: '(Salarios o sueldos fijos)' },
    { label: 'Renta de propiedad', description: '(Ingresos por alquiler de propiedades)' },
    { label: 'Regalias', description: '(Pagos por derechos de autor, musica, libros, etc.)' },
    { label: 'Dividendos', description: '(Ganancias por acciones o inversiones)' },
    { label: 'Pension', description: '(Ingresos por jubilacion o retiro)' },
    { label: 'Contratos de servicios', description: '(Mantenimiento o soporte tecnico)' },
    { label: 'Otro', description: '' },
];

const outcomeCategories = [
    { label: 'Servicios', description: '(Agua, luz, internet, etc.)' },
    { label: 'Alimentos y bebidas', description: '(Compras de despensa y comida)' },
    { label: 'Transporte', description: '(Gasolina, transporte publico, etc.)' },
    { label: 'Salud y bienestar', description: '(Medicinas, consultas, etc.)' },
    { label: 'Educacion', description: '(Cursos, colegiaturas, etc.)' },
    { label: 'Entretenimiento', description: '(Streaming, cine, etc.)' },
    { label: 'Otro', description: '' },
];

const currentCategories = computed(() => remaindType.value === 'Ingreso recurrente' ? incomeCategories : outcomeCategories);

function changeType() { form.type = remaindType.value; form.category = null; }

function store() {
    form.post(route('calendars.store'), { onSuccess: () => ElMessage.success('Recordatorio creado.') });
}
</script>

<template>
    <AppLayout title="Crear recordatorio">
        <main class="py-6 px-4 md:px-8 max-w-2xl mx-auto">
            <Back />
            <div class="form-card mt-3">
                <h1 class="form-title">Crear recordatorio</h1>
                <div class="flex items-center justify-between mb-6">
                    <el-collapse accordion class="flex-1 mr-4">
                        <el-collapse-item :title="remaindType" :name="1">
                            <template #title><span class="text-[#296A6B] font-bold text-base">{{ remaindType }}</span></template>
                            <p v-if="remaindType === 'Ingreso recurrente'" class="text-sm text-gray-600 dark:text-gray-400">Los ingresos recurrentes se registran automaticamente en el sistema. Estos corresponden a entradas periodicas, como sueldos, rentas u otros pagos regulares.</p>
                            <p v-else class="text-sm text-gray-600 dark:text-gray-400">Los gastos fijos se registran automaticamente en el sistema. Estos corresponden a salidas periodicas, pago de servicios, alimentos y otros pagos regulares.</p>
                        </el-collapse-item>
                    </el-collapse>
                    <el-segmented v-model="remaindType" :options="options" block @change="changeType" />
                </div>

                <el-form :model="form" label-position="top" @submit.prevent="store">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <el-form-item :label="remaindType === 'Ingreso recurrente' ? 'Nombre del ingreso' : 'Nombre del gasto'" required class="md:col-span-2">
                            <el-input v-model="form.title" maxlength="80" :placeholder="remaindType === 'Ingreso recurrente' ? 'Ej. Pago de nomina' : 'Ej. Compra de despensa'" show-word-limit />
                            <InputError :message="form.errors.title" />
                        </el-form-item>
                        <el-form-item label="Fecha de inicio" required>
                            <el-date-picker v-model="form.date" type="date" placeholder="Selecciona la fecha de inicio" class="!w-full" />
                            <InputError :message="form.errors.date" />
                        </el-form-item>
                        <el-form-item label="Monto" required>
                            <el-input v-model="form.amount" placeholder="Ej. 2,000" :formatter="(v) => `${v}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')" :parser="(v) => v.replace(/\$\s?|(,*)/g, '')"><template #prepend>$</template></el-input>
                            <InputError :message="form.errors.amount" />
                        </el-form-item>
                        <el-form-item label="Categoria">
                            <el-select v-model="form.category" filterable placeholder="Seleccione" class="!w-full">
                                <el-option v-for="cat in currentCategories" :key="cat.label" :label="cat.label" :value="cat.label"><span>{{ cat.label }}</span><span class="text-gray-400 text-xs ml-2">{{ cat.description }}</span></el-option>
                            </el-select>
                            <InputError :message="form.errors.category" />
                        </el-form-item>
                        <el-form-item label="Metodo de pago">
                            <el-select v-model="form.payment_method" filterable placeholder="Seleccione" class="!w-full"><el-option v-for="pm in paymentMethods" :key="pm" :label="pm" :value="pm" /></el-select>
                            <InputError :message="form.errors.payment_method" />
                        </el-form-item>
                        <el-form-item label="Frecuencia" required>
                            <el-select v-model="form.periodicity" filterable placeholder="Seleccione" class="!w-full"><el-option v-for="p in periodicities" :key="p" :label="p" :value="p" /></el-select>
                            <InputError :message="form.errors.periodicity" />
                        </el-form-item>
                    </div>
                    <el-form-item label="Descripcion">
                        <el-input v-model="form.description" maxlength="255" placeholder="Agrega una breve descripcion (opcional)" show-word-limit type="textarea" :rows="3" />
                        <InputError :message="form.errors.description" />
                    </el-form-item>
                    <div class="flex justify-end mt-6">
                        <el-button type="primary" :loading="form.processing" @click="store">Crear</el-button>
                    </div>
                </el-form>
            </div>
        </main>
    </AppLayout>
</template>
