<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { InfoFilled } from '@element-plus/icons-vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Back from '@/Components/MyComponents/Back.vue';

const showGuide = ref(false);

const form = useForm({
    type: 'Otorgado', beneficiary_name: null, payment_periodicity: null, lender_name: null,
    profitability: null, profitability_mode: 'Porcentaje', profitability_type: 'Amortizable',
    profitability_period: null, expired_date: null, amount: null, status: 'En curso',
    loan_date: null, description: null, no_interest: false, automatic: true,
});

const shortcuts = [
    { text: 'Hoy', value: new Date() },
    { text: 'Ayer', value: () => { const d = new Date(); d.setTime(d.getTime() - 86400000); return d; } },
    { text: 'Hace una semana', value: () => { const d = new Date(); d.setTime(d.getTime() - 86400000 * 7); return d; } },
];

const loanTypes = [{ label: 'Otorgado', description: 'Preste dinero' }, { label: 'Recibido', description: 'Me prestaron' }];
const profitabilityTypes = ['Amortizable'];
const periodicities = ['A un pago', 'Todos los dias', 'Semanal', 'Quincenal', 'Mensual', 'Anual'];

watch(() => form.no_interest, (val) => { if (val) { form.profitability = null; form.profitability_type = 'Amortizable'; form.profitability_period = null; } });

function store() {
    form.post(route('loans.store'), { onSuccess: () => ElMessage.success('Prestamo registrado correctamente.') });
}
</script>

<template>
    <AppLayout title="Registrar prestamo">
        <main class="py-6 px-4 md:px-8 max-w-2xl mx-auto">
            <Back />
            <div class="form-card mt-3">
                <h1 class="form-title">Registrar prestamo</h1>
                <el-form :model="form" label-position="top" @submit.prevent="store">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <el-form-item label="Tipo de prestamo" required>
                            <el-select v-model="form.type" filterable placeholder="Seleccione" class="!w-full">
                                <el-option v-for="lt in loanTypes" :key="lt.label" :value="lt.label">
                                    <span>{{ lt.label }}</span><span class="text-gray-400 text-xs ml-2">{{ lt.description }}</span>
                                </el-option>
                            </el-select>
                            <InputError :message="form.errors.type" />
                        </el-form-item>
                        <el-form-item v-if="form.type === 'Otorgado'" label="Nombre del beneficiario" required>
                            <el-input v-model="form.beneficiary_name" maxlength="50" placeholder="A quien otorgaste el prestamo" show-word-limit />
                            <InputError :message="form.errors.beneficiary_name" />
                        </el-form-item>
                        <el-form-item v-else label="Prestamista" required>
                            <el-input v-model="form.lender_name" maxlength="50" placeholder="Quien te presto" show-word-limit />
                            <InputError :message="form.errors.lender_name" />
                        </el-form-item>
                        <el-form-item label="Monto del prestamo" required>
                            <el-input v-model="form.amount" placeholder="Ej. 500" :formatter="(v) => `${v}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')" :parser="(v) => v.replace(/\$\s?|(,*)/g, '')"><template #prepend>$</template></el-input>
                            <InputError :message="form.errors.amount" />
                        </el-form-item>
                        <el-form-item label="Fecha del prestamo" required>
                            <el-date-picker v-model="form.loan_date" type="date" placeholder="Selecciona la fecha" :shortcuts="shortcuts" class="!w-full" />
                            <InputError :message="form.errors.loan_date" />
                        </el-form-item>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 my-4">
                        <el-checkbox v-model="form.no_interest" label="Prestamo sin intereses" border size="small" />
                        <el-checkbox v-model="form.automatic" label="Gestion automatica" border size="small">
                            <el-tooltip placement="top" :show-after="300"><template #content><div class="max-w-xs text-xs">El sistema registrara automaticamente los ingresos y gastos por intereses en cada abono, y actualizara el monto total de tu dinero disponible.</div></template><el-icon :size="14" class="ml-1" color="#296A6B"><InfoFilled /></el-icon></el-tooltip>
                        </el-checkbox>
                    </div>

                    <div v-if="!form.no_interest" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <el-form-item label="Interes" required>
                            <el-input v-model="form.profitability" :formatter="(v) => `${v}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')" :parser="(v) => v.replace(/\$\s?|(,*)/g, '')" placeholder="Ingresa el interes">
                                <template #append><el-select v-model="form.profitability_mode" style="width: 130px"><el-option label="% Porcentaje" value="Porcentaje" /><el-option label="Cantidad" value="Cantidad" /></el-select></template>
                            </el-input>
                        </el-form-item>
                        <el-form-item label="Tipo de interes" required>
                            <div class="flex items-center gap-2"><el-select v-model="form.profitability_type" class="!w-full" disabled><el-option v-for="pt in profitabilityTypes" :key="pt" :label="pt" :value="pt" /></el-select><el-button size="small" @click="showGuide = true">Guia</el-button></div>
                            <InputError :message="form.errors.profitability_type" />
                        </el-form-item>
                        <el-form-item label="Periodo de interes">
                            <el-select v-model="form.profitability_period" filterable placeholder="Seleccione" class="!w-full"><el-option v-for="p in periodicities" :key="p" :label="p" :value="p" /></el-select>
                            <InputError :message="form.errors.profitability_period" />
                        </el-form-item>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <el-form-item label="Frecuencia de pago">
                            <el-select v-model="form.payment_periodicity" filterable placeholder="Seleccione" class="!w-full"><el-option v-for="p in periodicities" :key="p" :label="p" :value="p" /></el-select>
                            <InputError :message="form.errors.payment_periodicity" />
                        </el-form-item>
                        <el-form-item label="Fecha de vencimiento">
                            <el-date-picker v-model="form.expired_date" type="date" placeholder="Selecciona la fecha" class="!w-full" />
                            <InputError :message="form.errors.expired_date" />
                        </el-form-item>
                        <el-form-item label="Estado del prestamo">
                            <el-select v-model="form.status" class="!w-full"><el-option label="En curso" value="En curso" /><el-option label="Pagado" value="Pagado" /></el-select>
                            <InputError :message="form.errors.status" />
                        </el-form-item>
                    </div>

                    <el-form-item label="Descripcion">
                        <el-input v-model="form.description" maxlength="255" placeholder="Descripcion del prestamo (opcional)" show-word-limit type="textarea" :rows="3" />
                        <InputError :message="form.errors.description" />
                    </el-form-item>

                    <div class="flex justify-end mt-6">
                        <el-button type="warning" :loading="form.processing" @click="store">Crear prestamo</el-button>
                    </div>
                </el-form>
            </div>
        </main>

        <el-dialog v-model="showGuide" title="Guia de Tipos de Interes" width="560px">
            <div class="space-y-4 text-sm">
                <div>
                    <p class="font-semibold">• Interes simple:</p>
                    <p class="ml-6 text-gray-600">Solo pagas una vez una cantidad fija de intereses. Ej: Pediste $100 y acordaron $10 extra despues de 1 ano. Total: $110.</p>
                </div>
                <div>
                    <p class="font-semibold">• Interes compuesto:</p>
                    <p class="ml-6 text-gray-600">Los intereses se calculan sobre lo que ya debias. Ano 1: $100 + $10 = $110. Ano 2: $110 + $11 = $121.</p>
                </div>
                <div>
                    <p class="font-semibold">• Interes fijo:</p>
                    <p class="ml-6 text-gray-600">Se acuerda pagar la misma cantidad de intereses siempre. Ej: $1,000 y cada mes pagas $100 fijo.</p>
                </div>
                <div>
                    <p class="font-semibold">• Interes amortizable:</p>
                    <p class="ml-6 text-gray-600">Los pagos cubren poco a poco la deuda y los intereses. Al principio pagas mas intereses y menos capital; al final, mas capital y menos intereses.</p>
                </div>
            </div>
            <template #footer><el-button @click="showGuide = false">Cerrar</el-button></template>
        </el-dialog>
    </AppLayout>
</template>
