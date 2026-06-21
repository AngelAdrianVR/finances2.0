<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { InfoFilled } from '@element-plus/icons-vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Back from '@/Components/MyComponents/Back.vue';

const props = defineProps({ loan: Object });

const showGuide = ref(false);
const form = useForm({
    type: props.loan.type, beneficiary_name: props.loan.beneficiary_name, payment_periodicity: props.loan.payment_periodicity,
    lender_name: props.loan.lender_name, profitability: props.loan.profitability, profitability_mode: props.loan.profitability_mode ?? 'Porcentaje',
    profitability_type: props.loan.profitability_type ?? 'Amortizable', profitability_period: props.loan.profitability_period,
    expired_date: props.loan.expired_date, amount: props.loan.amount, status: props.loan.status, loan_date: props.loan.loan_date,
    description: props.loan.description, no_interest: !props.loan.profitability, automatic: props.loan.automatic,
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

function update() { form.put(route('loans.update', props.loan.id), { onSuccess: () => ElMessage.success('Prestamo editado correctamente.') }); }
</script>

<template>
    <AppLayout title="Editar prestamo">
        <main class="py-6 px-4 md:px-8 max-w-2xl mx-auto">
            <Back />
            <div class="form-card mt-3">
                <h1 class="form-title">Editar prestamo</h1>
                <el-form :model="form" label-position="top" @submit.prevent="update">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <el-form-item label="Tipo de prestamo" required>
                            <el-select v-model="form.type" filterable class="!w-full">
                                <el-option v-for="lt in loanTypes" :key="lt.label" :value="lt.label"><span>{{ lt.label }}</span><span class="text-gray-400 text-xs ml-2">{{ lt.description }}</span></el-option>
                            </el-select>
                            <InputError :message="form.errors.type" />
                        </el-form-item>
                        <el-form-item v-if="form.type === 'Otorgado'" label="Nombre del beneficiario" required>
                            <el-input v-model="form.beneficiary_name" maxlength="50" show-word-limit /><InputError :message="form.errors.beneficiary_name" />
                        </el-form-item>
                        <el-form-item v-else label="Prestamista" required>
                            <el-input v-model="form.lender_name" maxlength="50" show-word-limit /><InputError :message="form.errors.lender_name" />
                        </el-form-item>
                        <el-form-item label="Monto del prestamo" required>
                            <el-input v-model="form.amount" :formatter="(v) => `${v}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')" :parser="(v) => v.replace(/\$\s?|(,*)/g, '')"><template #prepend>$</template></el-input>
                            <InputError :message="form.errors.amount" />
                        </el-form-item>
                        <el-form-item label="Fecha del prestamo" required>
                            <el-date-picker v-model="form.loan_date" type="date" :shortcuts="shortcuts" class="!w-full" /><InputError :message="form.errors.loan_date" />
                        </el-form-item>
                    </div>
                    <div class="flex flex-wrap items-center gap-4 my-4">
                        <el-checkbox v-model="form.no_interest" label="Prestamo sin intereses" border size="small" />
                        <el-checkbox v-model="form.automatic" label="Gestion automatica" border size="small">
                            <el-tooltip placement="top"><template #content><div class="max-w-xs text-xs">El sistema registrara automaticamente los ingresos y gastos por intereses en cada abono.</div></template><el-icon :size="14" class="ml-1" color="#296A6B"><InfoFilled /></el-icon></el-tooltip>
                        </el-checkbox>
                    </div>
                    <div v-if="!form.no_interest" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <el-form-item label="Interes" required>
                            <el-input v-model="form.profitability" :formatter="(v) => `${v}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')" :parser="(v) => v.replace(/\$\s?|(,*)/g, '')"><template #append><el-select v-model="form.profitability_mode" style="width:130px"><el-option label="% Porcentaje" value="Porcentaje" /><el-option label="Cantidad" value="Cantidad" /></el-select></template></el-input>
                        </el-form-item>
                        <el-form-item label="Tipo de interes"><div class="flex items-center gap-2"><el-select v-model="form.profitability_type" class="!w-full" disabled><el-option v-for="pt in profitabilityTypes" :key="pt" :label="pt" :value="pt" /></el-select><el-button size="small" @click="showGuide = true">Guia</el-button></div></el-form-item>
                        <el-form-item label="Periodo de interes"><el-select v-model="form.profitability_period" filterable class="!w-full"><el-option v-for="p in periodicities" :key="p" :label="p" :value="p" /></el-select></el-form-item>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <el-form-item label="Frecuencia de pago"><el-select v-model="form.payment_periodicity" filterable class="!w-full"><el-option v-for="p in periodicities" :key="p" :label="p" :value="p" /></el-select></el-form-item>
                        <el-form-item label="Fecha de vencimiento"><el-date-picker v-model="form.expired_date" type="date" class="!w-full" /></el-form-item>
                        <el-form-item label="Estado del prestamo"><el-select v-model="form.status" class="!w-full"><el-option label="En curso" value="En curso" /><el-option label="Pagado" value="Pagado" /></el-select></el-form-item>
                    </div>
                    <el-form-item label="Descripcion"><el-input v-model="form.description" maxlength="255" type="textarea" :rows="3" show-word-limit /></el-form-item>
                    <div class="flex justify-end mt-6"><el-button type="warning" :loading="form.processing" @click="update">Guardar cambios</el-button></div>
                </el-form>
            </div>
        </main>
        <el-dialog v-model="showGuide" title="Guia de Tipos de Interes" width="560px"><div class="space-y-4 text-sm"><div><p class="font-semibold">• Interes simple:</p><p class="ml-6 text-gray-600">Solo pagas una vez una cantidad fija de intereses.</p></div><div><p class="font-semibold">• Interes compuesto:</p><p class="ml-6 text-gray-600">Los intereses se calculan sobre lo que ya debias.</p></div><div><p class="font-semibold">• Interes amortizable:</p><p class="ml-6 text-gray-600">Los pagos cubren poco a poco la deuda y los intereses.</p></div></div><template #footer><el-button @click="showGuide = false">Cerrar</el-button></template></el-dialog>
    </AppLayout>
</template>
