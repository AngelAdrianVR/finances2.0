<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import RadialBar from '@/Components/MyComponents/Dashboard/Chart/RadialBar/Basic.vue';

const props = defineProps({
    loans: { type: Array, default: () => [] },
});

const totalGivenLoans = computed(() =>
    props.loans.filter(l => l.is_for_me == false).reduce((s, l) => s + l.amount, 0)
);

const totalLoansForMe = computed(() =>
    props.loans.filter(l => l.is_for_me == true).reduce((s, l) => s + l.amount, 0)
);

const totalPaidForMe = computed(() =>
    props.loans.filter(l => l.is_for_me == false).reduce((s, l) =>
        s + l.payments.reduce((ps, p) => ps + p.capital, 0), 0)
);

const totalPaidFromOthers = computed(() =>
    props.loans.filter(l => l.is_for_me == true).reduce((s, l) =>
        s + l.payments.reduce((ps, p) => ps + p.capital, 0), 0)
);

const receivedPct = computed(() =>
    totalLoansForMe.value > 0 ? ((totalPaidFromOthers.value * 100) / totalLoansForMe.value).toFixed(1) : 0
);

const givenPct = computed(() =>
    totalGivenLoans.value > 0 ? ((totalPaidForMe.value * 100) / totalGivenLoans.value).toFixed(1) : 0
);

const hasLoans = computed(() => props.loans && props.loans.length > 0);

function formatMoney(val) {
    return val?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}
</script>

<template>
    <div class="dashboard-card">
        <div v-if="hasLoans">
            <h2 class="dashboard-card-title">Estado de Prestamos</h2>

            <!-- Received loans -->
            <section class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Recibidos</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total recibido</p>
                    <p class="text-base font-mono font-semibold text-gray-800 dark:text-gray-200">$ {{ formatMoney(totalLoansForMe) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Total pagado</p>
                    <p class="text-base font-mono font-semibold text-gray-800 dark:text-gray-200">$ {{ formatMoney(totalPaidFromOthers) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Saldo restante</p>
                    <p class="text-base font-mono font-bold text-warning-600">$ {{ formatMoney(totalLoansForMe - totalPaidFromOthers) }}</p>
                </div>
                <div class="flex items-center justify-center">
                    <RadialBar v-if="totalLoansForMe > 0" :series="[parseFloat(receivedPct)]" />
                </div>
            </section>

            <!-- Given loans -->
            <section class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                <div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Otorgados</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total otorgado</p>
                    <p class="text-base font-mono font-semibold text-gray-800 dark:text-gray-200">$ {{ formatMoney(totalGivenLoans) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Total pagado</p>
                    <p class="text-base font-mono font-semibold text-gray-800 dark:text-gray-200">$ {{ formatMoney(totalPaidForMe) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Saldo restante</p>
                    <p class="text-base font-mono font-bold text-warning-600">$ {{ formatMoney(totalGivenLoans - totalPaidForMe) }}</p>
                </div>
                <div class="flex items-center justify-center">
                    <RadialBar v-if="totalGivenLoans > 0" :series="[parseFloat(givenPct)]" />
                </div>
            </section>
        </div>

        <!-- Empty state -->
        <div v-else class="empty-state py-6">
            <p class="empty-state-title">No tienes prestamos activos</p>
            <p class="empty-state-text">Agrega un prestamo para visualizar la informacion</p>
            <el-button type="primary" class="mt-4" @click="router.get(route('loans.create'))">+ Prestamo</el-button>
        </div>
    </div>
</template>
