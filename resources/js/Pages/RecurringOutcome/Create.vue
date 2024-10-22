<template>
    <AppLayout title="Crear gasto recurrente">
        <main class="px-3 md:px-16 py-8">
            <Back />
            <form @submit.prevent="store" class="rounded-xl border border-grayD9 lg:p-5 p-3 lg:w-2/3 xl:w-1/2 mx-auto mt-2 lg:grid lg:grid-cols-2 gap-3">
                <h1 class="font-bold ml-2 col-span-full mb-4">Registrar gasto recurrente</h1>
                
                <div>
                    <InputLabel value="Concepto*" class="ml-3 mb-1" />
                    <el-input
                        v-model="form.concept"
                        maxlength="50"
                        placeholder="Ej. Pago de uber"
                        show-word-limit
                        type="text"
                    />
                    <InputError :message="form.errors.concept" />
                </div>

                <div>
                    <InputLabel value="Monto*" class="ml-3 mb-1" />
                    <el-input
                        v-model="form.amount"
                        placeholder="Ej. $500"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/\$\s?|(,*)/g, '')"
                    >
                        <template #prepend>
                            <span>$</span>
                        </template>   
                    </el-input>
                    <InputError :message="form.errors.amount" />
                </div>

                <div>
                    <InputLabel value="Categoría" class="ml-3 mb-1" />
                    <el-select class="!w-full" filterable v-model="form.category"
                        placeholder="Seleccione" no-data-text="No hay opciones registradas"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="category in categories" :key="category" :label="category" :value="category">
                            <p class="flex items-center justify-between">
                                <span>{{ category }}</span>
                            </p>
                        </el-option>
                    </el-select>
                    <InputError :message="form.errors.category" />
                </div>

                <div>
                    <InputLabel value="Método de pago" class="ml-3 mb-1" />
                    <el-select class="!w-full" filterable v-model="form.payment_method"
                        placeholder="Seleccione" no-data-text="No hay opciones registradas"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in payment_methods" :key="item" :label="item" :value="item" />
                    </el-select>
                    <InputError :message="form.errors.payment_method" />
                </div>

                <div>
                    <InputLabel value="Recurrencia del gasto*" class="ml-3 mb-1" />
                    <el-select class="!w-full" filterable v-model="form.periodicity"
                        placeholder="Seleccione" no-data-text="No hay opciones registradas"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in periodicities" :key="item" :label="item" :value="item" />
                    </el-select>
                    <InputError :message="form.errors.periodicity" />
                </div>

                <div class="col-span-full">
                    <InputLabel value="Descripción" class="ml-3 mb-1" />
                    <el-input
                        v-model="form.description"
                        maxlength="255"
                        placeholder="Descripción del gasto (opcional)"
                        show-word-limit
                        type="textarea"
                    />
                    <InputError :message="form.errors.description" />
                </div>

                <div class="col-span-full space-x-4 text-right mt-7">
                    <PrimaryButton :disabled="form.processing">
                        <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Crear gasto recurrente
                    </PrimaryButton>
                </div>
            </form>
        </main>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";

export default {
data() {
    const form = useForm({
        amount: null,
        category: null,
        concept: null,
        payment_method: null,
        periodicity: null,
        description: null,
    });

    return {
        form,
        categories: ['Servicios', 'Comida', 'Transporte', 'Deuda', 'Renta', 'Otro'],
        payment_methods: ['Efectivo', 'Transferencia', 'Depósito', 'Pago con tarjeta'],
        periodicities: ['Todos los días', 'Semanal', 'Mensual', 'Anual'],
    }
},
components:{
    PrimaryButton,
    InputLabel,
    InputError,
    AppLayout,
    Back
},
methods:{
    store() {
        this.form.post(route("recurring-outcomes.store"), {
            onSuccess: () => {
                // message
                this.$message({
                    type: 'success',
                    message: 'Ingreso recurrente registrado'
                });
            },
        });
    },
}
}
</script>
