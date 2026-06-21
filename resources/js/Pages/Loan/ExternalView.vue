<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

const props = defineProps({ loan: Object });

const getRemainingAmount = computed(() => props.loan.amount - getTotalCapital.value);
const getTotalCapital = computed(() => props.loan.payments.reduce((a, i) => a + i.capital, 0));
const getTotalInterest = computed(() => props.loan.payments.reduce((a, i) => a + i.interest, 0));
const getTotalPaid = computed(() => getTotalCapital.value + getTotalInterest.value);

function formatDate(date) { return date ? format(new Date(date), 'dd MMMM yyyy', { locale: es }) : ''; }
function formatShortDate(date) { return date ? format(new Date(date), 'dd MMM yy', { locale: es }) : ''; }
function formatMoney(val) { return val?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','); }
</script>

<template>
    <Head title="Prestamo" />
    <main class="py-6 px-4 md:px-8 max-w-3xl mx-auto dark:text-gray-200">
        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-6">Detalles del prestamo</h1>

        <section class="space-y-6">
            <!-- Info card -->
            <article class="bg-white dark:bg-[#1E2424] border border-gray-200 dark:border-gray-700 rounded-xl p-5 grid grid-cols-2 gap-2 text-sm shadow-sm">
                <h2 class="font-semibold col-span-full mb-3 text-gray-800 dark:text-gray-200">Informacion del prestamo</h2>
                <p class="text-gray-500 dark:text-gray-400">{{ loan.type === 'Otorgado' ? 'Beneficiario:' : 'Prestamista:' }}</p><p class="dark:text-gray-300">{{ loan.type === 'Otorgado' ? loan.beneficiary_name : loan.lender_name }}</p>
                <p class="text-gray-500 dark:text-gray-400">Monto:</p><p class="font-mono font-semibold dark:text-gray-200">${{ formatMoney(loan.amount) }}</p>
                <p class="text-gray-500 dark:text-gray-400">Capital abonado:</p><p class="font-mono dark:text-gray-300">${{ formatMoney(getTotalCapital) }}</p>
                <p class="text-gray-500 dark:text-gray-400">Interes pagado:</p><p class="font-mono dark:text-gray-300">${{ formatMoney(getTotalInterest) }}</p>
                <p class="text-gray-500 dark:text-gray-400">Total abonado:</p><p class="font-mono dark:text-gray-300">${{ formatMoney(getTotalPaid) }}</p>
                <p class="text-gray-500 dark:text-gray-400">Saldo pendiente:</p><p class="font-mono font-bold" :class="getRemainingAmount === 0 ? 'text-[#2F9E5B]' : 'text-[#D99A2B]'">${{ formatMoney(getRemainingAmount) }}</p>
                <p class="text-gray-500 dark:text-gray-400">Fecha:</p><p class="dark:text-gray-300">{{ formatDate(loan.loan_date) }}</p>
                <p class="text-gray-500 dark:text-gray-400">Interes:</p><p class="dark:text-gray-300" v-if="loan.profitability">{{ loan.profitability }} {{ loan.profitability_mode === 'Porcentaje' ? '%' : '$' }}</p><p v-else class="dark:text-gray-300">0</p>
                <p class="text-gray-500 dark:text-gray-400">Vencimiento:</p><p class="dark:text-gray-300">{{ formatDate(loan.expired_date) ?? '-' }}</p>
                <p class="text-gray-500 dark:text-gray-400">Estado:</p><span class="text-xs px-2 py-0.5 rounded-full font-medium inline-flex items-center" :class="loan.status === 'Pagado' ? 'bg-[#EAF7EF] text-[#247A47]' : 'bg-[#FBF3E2] text-[#B47F1F]'">{{ loan.status }}</span>
            </article>

            <!-- Payments -->
            <article>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Abonos</h2>
                <el-table :data="loan.payments" max-height="500" style="width:100%">
                    <el-table-column label="Abono" width="100" align="right"><template #default="{row}"><span class="font-mono text-gray-800 dark:text-gray-200">${{ formatMoney(row.amount) }}</span></template></el-table-column>
                    <el-table-column label="Capital" width="100" align="right"><template #default="{row}"><span class="font-mono text-gray-800 dark:text-gray-200">${{ formatMoney(row.capital) }}</span></template></el-table-column>
                    <el-table-column label="Interes" width="100" align="right"><template #default="{row}"><span class="font-mono text-gray-800 dark:text-gray-200">${{ formatMoney(row.interest) }}</span></template></el-table-column>
                    <el-table-column label="Restante" width="110" align="right"><template #default="{row}"><span class="font-mono font-semibold" :class="row.remaining === 0 ? 'text-[#2F9E5B]' : 'text-[#D99A2B]'">${{ formatMoney(row.remaining) }}</span></template></el-table-column>
                    <el-table-column label="Fecha" width="120"><template #default="{row}"><span class="text-sm text-gray-600 dark:text-gray-400">{{ formatShortDate(row.date) }}</span></template></el-table-column>
                    <el-table-column label="Comentarios" min-width="140" show-overflow-tooltip><template #default="{row}">{{ row.notes ?? '-' }}</template></el-table-column>
                </el-table>
            </article>
        </section>
    </main>
</template>
