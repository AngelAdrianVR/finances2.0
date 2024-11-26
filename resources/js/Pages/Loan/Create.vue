<template>
    <AppLayout title="Registrar préstamo">
        <main class="px-3 md:px-16 py-8">
            <Back />
            <form @submit.prevent="store"
                class="rounded-xl border border-grayD9 lg:p-5 p-3 lg:w-2/3 xl:w-1/2 mx-auto mt-2 lg:grid lg:grid-cols-2 gap-3">
                <h1 class="font-bold ml-2 col-span-full mb-4">Registrar préstamo</h1>

                <div>
                    <InputLabel value="Tipo de préstamo" />
                    <el-select class="!w-full" filterable v-model="form.type" placeholder="Seleccione"
                        no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in loanTypes" :key="item" :value="item.label">
                            <p class="flex items-center justify-between">
                                <span>{{ item.label }}</span>
                                <span class="text-gray-400 text-xs">{{ item.description }}</span>
                            </p>
                        </el-option>
                    </el-select>
                    <InputError :message="form.errors.type" />
                </div>

                <div v-if="form.type === 'Otorgado'">
                    <InputLabel value="Nombre del beneficiario*" />
                    <el-input v-model="form.beneficiary_name" maxlength="50" placeholder="A quien otorgaste el préstamo"
                        show-word-limit type="text" />
                    <InputError :message="form.errors.beneficiary_name" />
                </div>

                <div v-else>
                    <InputLabel value="Prestamista*" />
                    <el-input v-model="form.lender_name" maxlength="50" placeholder="Quién te prestó" show-word-limit
                        type="text" />
                    <InputError :message="form.errors.lender_name" />
                </div>

                <div>
                    <InputLabel value="Monto del préstamo*" />
                    <el-input v-model="form.amount" placeholder="Ej. $500"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/\$\s?|(,*)/g, '')">
                        <template #prepend>
                            <span>$</span>
                        </template>
                    </el-input>
                    <InputError :message="form.errors.amount" />
                </div>
                <div>
                    <InputLabel value="Fecha de préstamo*" />
                    <el-date-picker class="!w-full" v-model="form.loan_date" type="date"
                        placeholder="Selecciona la fecha" :shortcuts="shortcuts" />
                    <InputError :message="form.errors.loan_date" />
                </div>
                <div class="flex items-center space-x-2 col-span-full my-1">
                    <el-checkbox @change="handleChecked()" v-model="form.no_interest" label="Préstamo sin intereses"
                        border size="small" />
                    <el-tooltip effect="dark" content="Solo se devuelve el monto prestado" placement="right">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4 text-primary">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                        </svg>
                    </el-tooltip>
                </div>
                <div v-if="!form.no_interest">
                    <InputLabel value="Interés*" />
                    <el-input v-model="form.profitability"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/\$\s?|(,*)/g, '')" placeholder="Ingresa el interés">
                        <template #append>
                            <el-select v-model="form.profitability_mode" placeholder="Select" style="width: 130px">
                                <el-option label="% Porcentaje" value="Porcentaje" />
                                <el-option label="Cantidad" value="Cantidad" />
                            </el-select>
                        </template>
                    </el-input>
                </div>

                <div v-if="!form.no_interest">
                    <div class="flex items-center justify-between">
                        <InputLabel value="Tipo de interés*" />
                        <button @click="showProfitabilityGuideModal = true" type="button"
                            class="rounded-sm px-3 text-sm text-[#575757] bg-grayD9">
                            Guía
                        </button>
                    </div>
                    <el-select class="!w-full" filterable v-model="form.profitability_type" placeholder="Seleccione"
                        no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in profitabilityTypes" :key="item" :label="item" :value="item">
                            <p class="flex items-center justify-between">
                                <span>{{ item }}</span>
                            </p>
                        </el-option>
                    </el-select>
                    <InputError :message="form.errors.profitability_type" />
                </div>

                <div v-if="!form.no_interest" class="col-span-full">
                    <InputLabel value="Periodo de interés" />
                    <el-select class="!w-1/2" filterable v-model="form.profiability_period" placeholder="Seleccione"
                        no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in periodicities" :key="item" :label="item" :value="item" />
                    </el-select>
                    <InputError :message="form.errors.profiability_period" />
                </div>

                <div>
                    <InputLabel value="Frecuencia de pago" />
                    <el-select class="!w-full" filterable v-model="form.payment_periodicity" placeholder="Seleccione"
                        no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in periodicities" :key="item" :label="item" :value="item" />
                    </el-select>
                    <InputError :message="form.errors.payment_periodicity" />
                </div>

                <div>
                    <InputLabel value="Fecha de Vencimiento" />
                    <el-date-picker class="!w-full" v-model="form.expired_date" type="date"
                        placeholder="Selecciona la fecha" :disabled-date="disabledDate" />
                    <InputError :message="form.errors.expired_date" />
                </div>
                <div>
                    <div class="flex items-center justify-between mx-3 mb-1">
                        <InputLabel value="Estado del préstamo" />
                        <svg v-if="form.status === 'En curso'" width="14" height="14" viewBox="0 0 12 12" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_14469_251" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                                width="12" height="12">
                                <rect width="12" height="12" fill="#D9D9D9" />
                            </mask>
                            <g mask="url(#mask0_14469_251)">
                                <path
                                    d="M3.925 10.5625C3.05833 10.1625 2.35417 9.55833 1.8125 8.75C1.27083 7.94167 1 7.02917 1 6.0125C1 5.79583 1.01042 5.58333 1.03125 5.375C1.05208 5.16667 1.0875 4.9625 1.1375 4.7625L0.5625 5.1L0.0625 4.2375L2.45 2.8625L3.825 5.2375L2.95 5.7375L2.275 4.5625C2.18333 4.7875 2.11458 5.02083 2.06875 5.2625C2.02292 5.50417 2 5.75417 2 6.0125C2 6.82083 2.22083 7.55625 2.6625 8.21875C3.10417 8.88125 3.69167 9.37083 4.425 9.6875L3.925 10.5625ZM7.75 4.5V3.5H9.1125C8.72917 3.025 8.26667 2.65625 7.725 2.39375C7.18333 2.13125 6.60833 2 6 2C5.54167 2 5.10833 2.07083 4.7 2.2125C4.29167 2.35417 3.91667 2.55417 3.575 2.8125L3.075 1.9375C3.49167 1.64583 3.94583 1.41667 4.4375 1.25C4.92917 1.08333 5.45 1 6 1C6.65833 1 7.2875 1.12292 7.8875 1.36875C8.4875 1.61458 9.025 1.97083 9.5 2.4375V1.75H10.5V4.5H7.75ZM7.425 12L5.0375 10.625L6.4125 8.25L7.275 8.75L6.5625 9.975C7.54583 9.83333 8.36458 9.3875 9.01875 8.6375C9.67292 7.8875 10 7.00833 10 6C10 5.90833 9.99792 5.82292 9.99375 5.74375C9.98958 5.66458 9.97917 5.58333 9.9625 5.5H10.975C10.9833 5.58333 10.9896 5.66458 10.9938 5.74375C10.9979 5.82292 11 5.90833 11 6C11 7.125 10.6646 8.13125 9.99375 9.01875C9.32292 9.90625 8.45 10.5042 7.375 10.8125L7.925 11.1375L7.425 12Z"
                                    fill="#3305BD" />
                            </g>
                        </svg>
                        <svg v-if="form.status === 'Pagado'" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-green-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <el-select class="!w-full" filterable v-model="form.status" placeholder="Seleccione"
                        no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in statuses" :key="item" :value="item.label">
                            <p class="flex items-center justify-between">
                                <span>{{ item.label }}</span>
                                <span v-html="item.icon"></span>
                            </p>
                        </el-option>
                    </el-select>
                    <InputError :message="form.errors.status" />
                </div>
                <div class="col-span-full">
                    <InputLabel value="Descripción" />
                    <el-input v-model="form.description" maxlength="255"
                        placeholder="Descripción del préstamo (opcional)" show-word-limit type="textarea" />
                    <InputError :message="form.errors.description" />
                </div>
                <div class="col-span-full space-x-4 text-right mt-7">
                    <PrimaryButton :disabled="form.processing">
                        <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Crear préstamo
                    </PrimaryButton>
                </div>
            </form>
        </main>

        <!-- -------------- Show profitability guide Modal starts----------------------- -->
        <DialogModal :show="showProfitabilityGuideModal" @close="showProfitabilityGuideModal = false">
            <template #title>
                <p class="font-bold text-left mb-5">Guía de Tipos de Interés</p>
            </template>

            <template #content>
                <section class="px-3">
                    <div class="my-4">
                        <p class="font-bold mb-1">• Interés simple: <span class="font-thin">Solo pagas una vez una
                                cantidad fija de intereses.</span></p>
                        <p class="font-bold ml-7">Ejemplo: <span class="font-thin">Pediste $100 a un amigo y acordaron
                                que le pagarías $10 extra después de 1 año por el favor. Total a pagar: $100 + $10 =
                                $110.</span></p>
                    </div>

                    <div class="my-4">
                        <p class="font-bold mb-1">• Interés compuesto: <span class="font-thin">Cada vez pagas más porque
                                los intereses se calculan sobre lo que ya debías.</span></p>
                        <p class="font-bold ml-7">Ejemplo: <span class="font-thin">Pides $100, pero en lugar de un solo
                                pago al final, los intereses se suman y siguen creciendo. <br> 
                                Año 1: Debes $100 + $10 (10%
                                de $100) = $110. <br>
                                Año 2: Ahora el 10% se calcula sobre los $110, entonces: $110 + $11
                                (10% de $110) = $121.</span></p>
                    </div>

                    <div class="my-4">
                        <p class="font-bold mb-1">• Interés fijo: <span class="font-thin">Se acorda en pagar la misma
                                cantidad de intereses, sin que suba ni baje.</span></p>
                        <p class="font-bold ml-7">Ejemplo: <span class="font-thin">Pides $1,000 y cada mes pagarás $100
                                sin que cambie.</span></p>
                    </div>

                    <div class="my-4">
                        <p class="font-bold mb-1">• Interés amortizable: <span class="font-thin">Pagos que poco a poco
                                cubren la deuda y los intereses.</span></p>
                        <p class="font-bold ml-7">Ejemplo: <span class="font-thin">Pides $10,000 y pagas cada mes $1,000 <br>
                                Al principio, $900 son intereses y solo $100 van al capital (a la deuda). <br>
                                Con el tiempo, pagas menos intereses y más de la deuda hasta terminar.</span></p>
                    </div>
                </section>
            </template>

            <template #footer>
                <PrimaryButton @click="showProfitabilityGuideModal = false">Cerrar</PrimaryButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from "@/Components/DialogModal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";

export default {
    data() {
        const form = useForm({
            type: null,
            beneficiary_name: null,
            payment_periodicity: null,
            lender_name: null,
            profitability: null,
            profitability_mode: 'Porcentaje',
            profitability_type: null,
            profiability_period: null,
            expired_date: null,
            amount: null,
            status: 'En curso',
            loan_date: null,
            description: null,
            no_interest: false,
        });

        return {
            form,
            showProfitabilityGuideModal: false,
            shortcuts: [
                {
                    text: 'Hoy',
                    value: new Date(),
                },
                {
                    text: 'Ayer',
                    value: () => {
                        const date = new Date();
                        date.setTime(date.getTime() - 3600 * 1000 * 24);
                        return date;
                    },
                },
                {
                    text: 'Hace una semana',
                    value: () => {
                        const date = new Date();
                        date.setTime(date.getTime() - 3600 * 1000 * 24 * 7);
                        return date;
                    },
                },
            ],
            loanTypes: [{ label: 'Otorgado', description: 'Presté dinero' }, { label: 'Recibido', description: 'Me prestaron a mí' }],
            profitabilityTypes: ['Simple', 'Compuesto', 'Fijo', 'Amortizable'],
            periodicities: ['Todos los días', 'Semanal', 'Quincenal', 'Mensual', 'Anual'],
            statuses: [
                {
                    label: 'En curso',
                    icon: '<svg width="14" height="14" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><mask id="mask0_14469_251" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="12" height="12"><rect width="12" height="12" fill="#D9D9D9"/></mask><g mask="url(#mask0_14469_251)"><path d="M3.925 10.5625C3.05833 10.1625 2.35417 9.55833 1.8125 8.75C1.27083 7.94167 1 7.02917 1 6.0125C1 5.79583 1.01042 5.58333 1.03125 5.375C1.05208 5.16667 1.0875 4.9625 1.1375 4.7625L0.5625 5.1L0.0625 4.2375L2.45 2.8625L3.825 5.2375L2.95 5.7375L2.275 4.5625C2.18333 4.7875 2.11458 5.02083 2.06875 5.2625C2.02292 5.50417 2 5.75417 2 6.0125C2 6.82083 2.22083 7.55625 2.6625 8.21875C3.10417 8.88125 3.69167 9.37083 4.425 9.6875L3.925 10.5625ZM7.75 4.5V3.5H9.1125C8.72917 3.025 8.26667 2.65625 7.725 2.39375C7.18333 2.13125 6.60833 2 6 2C5.54167 2 5.10833 2.07083 4.7 2.2125C4.29167 2.35417 3.91667 2.55417 3.575 2.8125L3.075 1.9375C3.49167 1.64583 3.94583 1.41667 4.4375 1.25C4.92917 1.08333 5.45 1 6 1C6.65833 1 7.2875 1.12292 7.8875 1.36875C8.4875 1.61458 9.025 1.97083 9.5 2.4375V1.75H10.5V4.5H7.75ZM7.425 12L5.0375 10.625L6.4125 8.25L7.275 8.75L6.5625 9.975C7.54583 9.83333 8.36458 9.3875 9.01875 8.6375C9.67292 7.8875 10 7.00833 10 6C10 5.90833 9.99792 5.82292 9.99375 5.74375C9.98958 5.66458 9.97917 5.58333 9.9625 5.5H10.975C10.9833 5.58333 10.9896 5.66458 10.9938 5.74375C10.9979 5.82292 11 5.90833 11 6C11 7.125 10.6646 8.13125 9.99375 9.01875C9.32292 9.90625 8.45 10.5042 7.375 10.8125L7.925 11.1375L7.425 12Z" fill="#3305BD"/></g></svg>'

                },
                {
                    label: 'Pagado',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-green-600"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>'
                }
            ],
        }
    },
    components: {
        PrimaryButton,
        DialogModal,
        InputLabel,
        InputError,
        AppLayout,
        Back
    },
    methods: {
        store() {
            this.form.post(route("loans.store"), {
                onSuccess: () => {
                    // message
                    this.$message({
                        type: 'success',
                        message: 'Préstamo registrado'
                    });
                },
            });
        },
        handleChecked() {
            if (!this.form.no_interest) {
                this.form.profitability = null;
                this.form.profitability_type = null;
            }
        },
        disabledDate(time) {
            return time.getTime() < Date.now();
        },
    }
}
</script>
