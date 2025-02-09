<template>
    <AppLayout title="Préstamos">
        <main class="px-2 md:px-10 pt-4 pb-16">
            <Back :to="route('loans.index', { currentTab: loan.type == 'Otorgado' ? 1 : 2 })" />
            <h1 class="font-bold my-4">Detalles del préstamo</h1>
            <div class="flex flex-col lg:flex-row justify-between">
                <div class="w-1/2 md:w-1/3 mr-2">
                    <el-select @change="$inertia.get(route('loans.show', loanSelected))" v-model="loanSelected"
                        clearable filterable placeholder="Buscar" no-data-text="No hay órdenes registradas"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in loans" :key="item.id" :label="item.type === 'Otorgado' ? 'O-' + item.id?.toString().padStart(3, '0') + ' - ' + item.beneficiary_name
                            : 'R-' + item.id?.toString().padStart(3, '0') + ' - ' + item.lender_name" :value="item.id">
                            <p class="flex items-center justify-between">
                                <span>{{ item.type === 'Otorgado' ? 'O-' + item.id?.toString().padStart(3, '0') + ' - '
                                    + item.beneficiary_name
                                    : 'R-' + item.id?.toString().padStart(3, '0') + ' - ' + item.lender_name }}</span>
                                <span class="text-gray-400 text-xs">${{
                                    item.amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                            </p>
                        </el-option>
                    </el-select>
                </div>
                <!-- Botones de acción -->
                <div class="flex items-center space-x-2 mt-3 lg:mt-0">
                    <ThirthButton @click="$inertia.get(route('loans.edit', loan.id))"
                        class="flex items-center space-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                        <p>Editar préstamo</p>
                    </ThirthButton>
                    <ThirthButton @click="showShareModal = true" class="!size-9 !p-0 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                        </svg>
                    </ThirthButton>
                    <ThirthButton @click="deleteItem()" class="!size-9 !p-0 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </ThirthButton>
                </div>
            </div>

            <section class="lg:flex lg:space-x-5 space-y-4 lg:space-y-0 mt-9 text-[15px]">
                <article class="lg:w-1/3 grid grid-cols-2 gap-2 rounded-xl bg-[#F2F2F2] border border-grayD9 p-5">
                    <h2 class="font-bold col-span-full mb-4">Información del préstamo</h2>
                    <p class="text-[#575757]">Folio:</p>
                    <p>{{ loan.type === 'Otorgado' ? 'O-' : 'R-' }}{{ loan.id?.toString().padStart(3, '0') }}</p>

                    <p class="text-[#575757]">Tipo de préstamo:</p>
                    <p>{{ loan.type }}</p>

                    <p class="text-[#575757]">
                        {{ loan.type === 'Otorgado'
                            ? 'Nombre del beneficiario:'
                            : 'Nombre del prestamista:' }}
                    </p>
                    <p>{{ loan.type === 'Otorgado' ? loan.beneficiary_name : loan.lender_name }}</p>

                    <p class="text-[#575757]">Monto del préstamo:</p>
                    <p>${{ loan.amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>

                    <p class="text-[#575757]">Total a capital:</p>
                    <p>${{ getTotalCapital.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                    <p class="text-[#575757]">Total de interés:</p>
                    <p>${{ getTotalInterest.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                    <p class="text-[#575757]">Total abonado:</p>
                    <p>${{ getTotalPaid.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>

                    <p class="text-[#575757]">Saldo pendiente:</p>
                    <p class="font-bold">${{ getRemainingAmount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>

                    <p class="text-[#575757]">Fecha del préstamo:</p>
                    <p>{{ formatDate(loan.loan_date) }}</p>

                    <p class="text-[#575757]">Interés:</p>
                    <p v-if="loan.profitability">{{ loan.profitability }} {{ loan.profitability_mode === 'Porcentaje' ?
                        '%' : '$' }}</p>
                    <p v-else>0.0</p>
                    <p class="text-[#575757]">Periodo de interés:</p>
                    <p>{{ loan.profitability_period }}</p>
                    <p class="text-[#575757]">Tipo de interés:</p>
                    <p>{{ loan.profitability_type ?? '-' }}</p>
                    <p class="text-[#575757]">Fecha de vencimiento:</p>
                    <p>{{ formatDate(loan.expired_date) ?? '-' }}</p>
                    <p class="text-[#575757]">Estado del préstamo:</p>
                    <div class="flex items-center space-x-2">
                        <svg v-if="loan.status === 'En curso'" width="14" height="14" viewBox="0 0 12 12" fill="none"
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
                        <svg v-if="loan.status === 'Pagado'" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-[#0CBE3B]">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <p>{{ loan.status }}</p>
                    </div>
                    <p class="text-[#575757]">Descripción:</p>
                    <p style="white-space: pre-line;">{{ loan.description ?? '-' }}</p>
                    <p class="text-[#575757]">Intereses a pagar de este periodo:</p>
                    <p>${{ calculatePeriodInterest().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                    <p class="text-[#575757]">Intereses desde el último pago hasta hoy:</p>
                    <p>${{ calculateAccumulatedInterest().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                </article>
                <article class="lg:w-2/3 rounded-xl border border-grayD9 py-5 px-8">
                    <div class="flex items-center justify-between">
                        <h2 class="font-bold">Desgloce del préstamo</h2>
                        <PrimaryButton v-if="getRemainingAmount" @click="createPayment" class="!rounded-full">
                            Registrar abono
                        </PrimaryButton>
                    </div>
                    <el-table :data="loan.payments" max-height="500" ref="multipleTableRef"
                        :row-class-name="tableRowClassName" class="mt-5">
                        <el-table-column label="Restante" width="100">
                            <template #default="scope">
                                <p>${{ scope.row.remaining.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                            </template>
                        </el-table-column>
                        <el-table-column label="Intereses" width="100">
                            <template #default="scope">
                                <p>${{ scope.row.interest.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                            </template>
                        </el-table-column>
                        <el-table-column prop="amount" label="Abono" width="100">
                            <template #default="scope">
                                <p>${{ scope.row.amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                            </template>
                        </el-table-column>
                        <el-table-column label="Pago a capital" width="140">
                            <template #default="scope">
                                <p>${{ scope.row.capital.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                            </template>
                        </el-table-column>
                        <el-table-column prop="date" label="Fecha" width="120">
                            <template #default="scope">
                                <p>{{ formatShortDate(scope.row.date) }}</p>
                            </template>
                        </el-table-column>
                        <el-table-column prop="notes" label="Comentarios" width="120">
                            <template #default="scope">
                                <p class="w-full truncate" :title="scope.row.notes">{{ scope.row.notes ?? '-' }}</p>
                            </template>
                        </el-table-column>
                        <el-table-column align="right">
                            <template #default="scope">
                                <el-dropdown trigger="click" @command="handleCommand">
                                    <button @click.stop
                                        class="mr-3 justify-center items-center size-8 rounded-full text-primary hover:bg-grayD9 transition-all duration-200 ease-in-out">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item :command="'edit-' + scope.row.id">
                                                Editar
                                            </el-dropdown-item>
                                            <el-dropdown-item @click="itemToShow = scope.row"
                                                :command="'delete-' + scope.row.id + '-' + scope.row">
                                                Eliminar
                                            </el-dropdown-item>
                                        </el-dropdown-menu>
                                    </template>
                                </el-dropdown>
                            </template>
                        </el-table-column>
                    </el-table>
                </article>
            </section>
        </main>

        <DialogModal :show="showPaymentModal" @close="showPaymentModal = false">
            <template #title>
                <p class="font-bold text-left">Registrar abono (Saldo pendiente ${{
                    getRemainingAmount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }})</p>
            </template>
            <template #content>
                <form @submit.prevent="storeOrUpdatePayment" class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <InputLabel value="Monto abonado*" />
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
                        <InputLabel value="Fecha del abono*" />
                        <el-date-picker class="!w-full" v-model="form.date" type="date"
                            placeholder="Selecciona la fecha" :teleported="false" />
                        <InputError :message="form.errors.date" />
                    </div>
                    <div>
                        <InputLabel value="Método de pago*" />
                        <el-select filterable v-model="form.payment_method" placeholder="Seleccione" :teleported="false"
                            no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                            <el-option v-for="item in paymentTypes" :key="item" :value="item" :label="item" />
                        </el-select>
                        <InputError :message="form.errors.payment_method" />
                    </div>
                    <div class="col-span-full">
                        <InputLabel value="Comentarios" />
                        <el-input v-model="form.notes" maxlength="500" placeholder="Ej. Pago mes septiembre"
                            :autosize="{ minRows: 4, maxRows: 6 }" show-word-limit type="textarea" />
                        <InputError :message="form.errors.notes" />
                    </div>
                </form>
            </template>
            <template #footer>
                <PrimaryButton @click="storeOrUpdatePayment" :disabled="form.processing">
                    <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                    {{ editingPayment ? 'Guardar cambios' : 'Registrar' }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <DialogModal :show="showShareModal" @close="showShareModal = false" max-width="md">
            <template #title>
                <p class="font-bold text-left">Compartir enlace</p>
            </template>
            <template #content>
                <p>
                    El enlace que se compartirá muestra el estado completo del préstamo pero no se puede editar,
                    eliminar o registrar abonos. Es meramente informativo.
                    <a :href="route('loans.external-view', encryptId())" target="_blank" rel="noopener noreferrer"
                        class="text-primary underline">Vista previa</a>
                </p>
                <br>

                <section>
                    <h2>Compartir con:</h2>
                    <article class="flex justify-around mt-3">
                        <button @click="shareByWhatsapp" type="button" class="flex flex-col items-center space-x-2">
                            <i class="fa-brands fa-whatsapp text-green-600 text-4xl"></i>
                            <span>Whatsapp</span>
                        </button>
                        <button @click="copyToClipboard" type="button" class="flex flex-col items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-10">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                            </svg>
                            <span>Copiar link</span>
                        </button>
                    </article>
                </section>
            </template>
            <template #footer>
                <CancelButton @click="showShareModal = false">
                    Cancelar
                </CancelButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import Back from "@/Components/MyComponents/Back.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import DialogModal from '@/Components/DialogModal.vue';
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import CancelButton from '@/Components/CancelButton.vue';

export default {
    data() {
        const form = useForm({
            amount: null,
            date: format(new Date(), "yyyy-MM-dd"), // Establece la fecha de hoy por defecto,
            payment_method: 'Transferencia',
            notes: null,
            loan_id: this.loan.id,
        });

        return {
            form,
            editingPayment: false,
            loanSelected: this.loan.id,
            selectedPayment: null,
            showPaymentModal: false,
            showShareModal: false,
            paymentTypes: ['Efectivo', 'Transferencia', 'Depósito'],
        }
    },
    components: {
        PrimaryButton,
        ThirthButton,
        CancelButton,
        AppLayout,
        Back,
        DialogModal,
        InputLabel,
        InputError,
    },
    props: {
        loan: Object,
        loans: Array,
    },
    computed: {
        getRemainingAmount() {
            return this.loan.amount - this.getTotalCapital;
        },
        getTotalCapital() {
            return this.loan.payments.reduce((accum, item) => {
                return accum + item.capital;
            }, 0);
        },
        getTotalInterest() {
            return this.loan.payments.reduce((accum, item) => {
                return accum + item.interest;
            }, 0);
        },
        getTotalPaid() {
            return this.getTotalCapital + this.getTotalInterest;
        }
    },
    methods: {
        encryptId() {
            return btoa(this.loan.id);
        },
        shareByWhatsapp() {
            const url = 'https://finanzas.dtw.com.mx/loans/external-view/' + this.encryptId();
            const message = `¡Hola! Quiero compartirte este enlace: ${url}`;
            const whatsappUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank');
        },
        copyToClipboard() {
            const url = 'https://finanzas.dtw.com.mx/loans/external-view/' + this.encryptId();
            navigator.clipboard.writeText(url).then(() => {
                this.showShareModal = false;
                this.$notify({
                    title: 'Enlace copiado al portapapeles',
                    type: 'success',
                });
            }).catch(() => {
                this.showShareModal = false;
                this.$message({
                    type: 'error',
                    message: 'Error al copiar el enlace',
                });
            });
        },
        calculatePeriodInterest() {
            const principal = this.loan.payments.length
                ? this.loan.payments[this.loan.payments.length - 1].remaining
                : this.loan.amount;

            if (this.loan.profitability_mode === 'Porcentaje') {
                const interestRate = this.loan.profitability / 100;
                return principal * interestRate;
            } else if (this.loan.profitability_mode === 'Cantidad') {
                return this.loan.profitability;
            }
        },
        calculateAccumulatedInterest() {
            const loanDate = new Date(this.loan.loan_date);
            const today = new Date();
            const lastPaymentDate = this.loan.payments.length ? new Date(this.loan.payments[this.loan.payments.length - 1].date) : loanDate;

            const timeDiff = today - lastPaymentDate;
            const daysDiff = timeDiff / (1000 * 3600 * 24);

            const principal = this.loan.amount;
            let accumulatedInterest = 0;

            const calculateDailyInterest = (rate, period) => {
                switch (period) {
                    case 'Todos los días':
                        return rate;
                    case 'Semanal':
                        return rate / 7;
                    case 'Quincenal':
                        return rate / 15;
                    case 'Mensual':
                        return rate / 30;
                    case 'Anual':
                        return rate / 365;
                    default:
                        return 0;
                }
            };

            if (this.loan.profitability_mode === 'Porcentaje') {
                const interestRate = this.loan.profitability / 100;
                const dailyInterestRate = calculateDailyInterest(interestRate, this.loan.profitability_period);
                accumulatedInterest = principal * dailyInterestRate * daysDiff;
            } else if (this.loan.profitability_mode === 'Cantidad') {
                const dailyInterest = calculateDailyInterest(this.loan.profitability, this.loan.profitability_period);
                accumulatedInterest = dailyInterest * daysDiff;
            }

            return accumulatedInterest;
        },
        createPayment() {
            this.form.reset();
            this.showPaymentModal = true;
            this.editingPayment = false;
        },
        storeOrUpdatePayment() {
            if (this.editingPayment) {
                this.form.put(route('payments.update', this.selectedPayment), {
                    onSuccess: () => {
                        this.showPaymentModal = false;
                        this.editingPayment = false;
                        this.form.reset();
                    },
                    onError: (error) => {
                        console.log(error);
                    }
                });
            } else {
                this.form.post(route('payments.store'), {
                    onSuccess: () => {
                        this.showPaymentModal = false;
                        this.form.reset();
                    },
                    onError: (error) => {
                        console.log(error);
                    }
                });
            }
        },
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName === 'edit') {
                this.selectedPayment = this.loan.payments.find(item => item.id == rowId);
                // llenar formulario con datos de abono seleccionado
                this.form.amount = this.selectedPayment.amount,
                    this.form.date = this.selectedPayment.date,
                    this.form.payment_method = this.selectedPayment.payment_method,
                    this.form.notes = this.selectedPayment.notes,
                    // abrir modal
                    this.editingPayment = true;
                this.showPaymentModal = true;
            } else if (commandName === 'delete') {
                this.deletePayment(rowId);
            }
        },
        formatShortDate(date) {
            if (date) {
                const parsedDate = new Date(date);
                return format(parsedDate, 'dd MMM yyyy', { locale: es }); // Formato personalizado
            }
        },
        formatDate(date) {
            if (date) {
                const parsedDate = new Date(date);
                return format(parsedDate, 'dd MMMM yyyy', { locale: es }); // Formato personalizado
            }
        },
        tableRowClassName({ row, rowIndex }) {
            return 'cursor-pointer text-xs';
        },
        deleteItem() {
            this.$confirm('¿Estás seguro que deseas continuar con la eliminación?', 'Confirmar', {
                confirmButtonText: 'Sí',
                cancelButtonText: 'No',
                type: 'warning'
            }).then(() => {
                this.$inertia.delete(route('loans.destroy', this.loan.id));
            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: 'Eliminación cancelada'
                });
            });
        },
        deletePayment(paymentId) {
            this.$confirm('¿Estás seguro que deseas continuar con la eliminación?', 'Confirmar', {
                confirmButtonText: 'Sí',
                cancelButtonText: 'No',
                type: 'warning'
            }).then(() => {
                this.form.delete(route('payments.destroy', paymentId, {
                    onSuccess: () => {

                    },
                    onError: (error) => {
                        console.log(error)
                    },
                }));
            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: 'Eliminación cancelada'
                });
            });
        },
    }
}
</script>
