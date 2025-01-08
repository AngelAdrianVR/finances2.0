<template>

    <Head title="Préstamo" />
    <main class="px-2 md:px-10 pt-4 pb-16">
        <h1 class="font-bold mx-10 my-4 flex items-center justify-between">
            <span>Detalles del préstamo</span>
            <PrimaryButton @click="$inertia.visit(route('register'))" class="flex items-center space-x-2">
                <svg width="27" height="31" viewBox="0 0 33 37" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12.4802 11.3056C12.4802 7.34127 5.89246 2.58225 0 0V13.3611C0 16.004 5.41599 20.611 12.4802 23.9325V11.3056Z"
                        fill="white" />
                    <path
                        d="M32.4484 3.23016C23.9325 5.43254 14.8294 14.8294 14.8294 17.9127V37C15.4113 36.6944 15.7338 36.5254 16.2976 36.2659C16.8115 29.5161 18.1604 26.4677 22.7579 22.3175C25.5157 17.8366 27.5972 15.835 32.4484 13.5079V3.23016Z"
                        fill="white" />
                    <path
                        d="M21.1429 33.7698L18.0595 35.3849C18.4186 31.0712 19.1807 29.1855 21.2897 26.5754C20.9616 28.9928 20.8527 30.4461 21.1429 33.7698Z"
                        fill="white" />
                    <path
                        d="M32.4484 21.877C31.3934 25.856 29.4334 28.3021 22.4643 33.1825C21.942 29.1534 22.1713 27.6155 23.0516 25.1071C24.8316 22.1055 27.0116 21.0216 32.4484 19.9683V21.877Z"
                        fill="white" />
                    <path
                        d="M32.4484 17.9127C29.4179 18.4833 27.9556 19.1956 25.4008 20.5556C27.4388 17.6 29.0731 16.3004 32.4484 14.9762V17.9127Z"
                        fill="white" />
                </svg>
                <span>Crear cuenta o iniciar sesión</span>
            </PrimaryButton>
        </h1>
        <section class="lg:flex lg:space-x-5 space-y-4 lg:space-y-0 mt-9 text-[15px]">
            <article class="lg:w-1/3 grid grid-cols-2 gap-2 rounded-xl bg-[#F2F2F2] border border-grayD9 p-5">
                <h2 class="font-bold col-span-full mb-4">Información del préstamo</h2>
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
                <!-- <p class="text-[#575757]">Intereses desde el último pago hasta hoy:</p>
                <p>${{ calculateAccumulatedInterest().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p> -->
            </article>
            <article class="lg:w-2/3 rounded-xl border border-grayD9 py-5 px-8">
                <el-table :data="loan.payments" max-height="500" :row-class-name="tableRowClassName" class="mt-5">
                    <el-table-column label="Restante">
                        <template #default="scope">
                            <p>${{ scope.row.remaining.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column label="Intereses">
                        <template #default="scope">
                            <p>${{ scope.row.interest.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column prop="amount" label="Abono">
                        <template #default="scope">
                            <p>${{ scope.row.amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column label="Pago a capital">
                        <template #default="scope">
                            <p>${{ scope.row.capital.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column prop="date" label="Fecha">
                        <template #default="scope">
                            <p>{{ formatShortDate(scope.row.date) }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column prop="notes" label="Comentarios">
                        <template #default="scope">
                            <p class="w-full truncate" :title="scope.row.notes">{{ scope.row.notes ?? '-' }}</p>
                        </template>
                    </el-table-column>
                </el-table>
            </article>
        </section>
    </main>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { Head } from '@inertiajs/vue3';

export default {
    data() {
        return {
        }
    },
    components: {
        PrimaryButton,
        Head,
    },
    props: {
        loan: Object,
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
    }
}
</script>
