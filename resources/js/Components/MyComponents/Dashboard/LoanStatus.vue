<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { ArrowRight, Coin, Plus, TrendCharts, Wallet } from '@element-plus/icons-vue';
import RadialBar from '@/Components/MyComponents/Dashboard/Chart/RadialBar/Basic.vue';

const props = defineProps({
    loans: { type: Array, default: () => [] },
});

const currency = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const receivedTotal = computed(() =>
    props.loans.filter((loan) => loan.is_for_me == true).reduce((sum, loan) => sum + loan.amount, 0)
);

const receivedPaid = computed(() =>
    props.loans
        .filter((loan) => loan.is_for_me == true)
        .reduce(
            (sum, loan) => sum + loan.payments.reduce((paid, payment) => paid + payment.capital, 0),
            0
        )
);

const receivedRemaining = computed(() => Math.max(receivedTotal.value - receivedPaid.value, 0));

const givenTotal = computed(() =>
    props.loans.filter((loan) => loan.is_for_me == false).reduce((sum, loan) => sum + loan.amount, 0)
);

const givenPaid = computed(() =>
    props.loans
        .filter((loan) => loan.is_for_me == false)
        .reduce(
            (sum, loan) => sum + loan.payments.reduce((paid, payment) => paid + payment.capital, 0),
            0
        )
);

const givenRemaining = computed(() => Math.max(givenTotal.value - givenPaid.value, 0));

function progress(paid, total) {
    return total > 0 ? Math.min((paid * 100) / total, 100) : 0;
}

const receivedProgress = computed(() => progress(receivedPaid.value, receivedTotal.value));
const givenProgress = computed(() => progress(givenPaid.value, givenTotal.value));

const hasLoans = computed(() => props.loans.length > 0);

const activeCountLabel = computed(() =>
    props.loans.length === 1 ? '1 activo' : `${props.loans.length} activos`
);

function formatMoney(value) {
    return currency.format(Number(value || 0));
}
</script>

<template>
    <div class="dashboard-card dashboard-card--flush">
        <div class="dashboard-card__head">
            <div>
                <h2 class="dashboard-card__title">Estado de préstamos</h2>
                <p class="dashboard-card__subtitle">Avance de lo que debes y de lo que te deben</p>
            </div>
            <span v-if="hasLoans" class="dashboard-chip">{{ activeCountLabel }}</span>
        </div>

        <div v-if="hasLoans" class="loan-status">
            <!-- Préstamos recibidos -->
            <section class="loan-block">
                <header class="loan-block__head">
                    <span class="loan-block__icon loan-block__icon--received">
                        <el-icon :size="17"><Wallet /></el-icon>
                    </span>
                    <div class="loan-block__heading">
                        <p class="loan-block__title">Recibidos</p>
                        <p class="loan-block__hint">Saldo por cubrir</p>
                    </div>
                    <span class="loan-block__badge loan-block__badge--received">
                        {{ receivedProgress.toFixed(0) }}% pagado
                    </span>
                </header>

                <div class="loan-block__body">
                    <div class="loan-block__gauge">
                        <RadialBar :series="[Number(receivedProgress.toFixed(1))]" width="104px" height="118" />
                    </div>

                    <div class="loan-block__numbers">
                        <p class="loan-block__due-label">Saldo restante</p>
                        <p class="loan-block__due-value">{{ formatMoney(receivedRemaining) }}</p>
                        <dl class="loan-block__mini">
                            <div>
                                <dt>Pagado</dt>
                                <dd class="is-paid">{{ formatMoney(receivedPaid) }}</dd>
                            </div>
                            <div>
                                <dt>Total</dt>
                                <dd>{{ formatMoney(receivedTotal) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </section>

            <!-- Préstamos otorgados -->
            <section class="loan-block">
                <header class="loan-block__head">
                    <span class="loan-block__icon loan-block__icon--given">
                        <el-icon :size="17"><Coin /></el-icon>
                    </span>
                    <div class="loan-block__heading">
                        <p class="loan-block__title">Otorgados</p>
                        <p class="loan-block__hint">Saldo por recuperar</p>
                    </div>
                    <span class="loan-block__badge loan-block__badge--given">
                        {{ givenProgress.toFixed(0) }}% recuperado
                    </span>
                </header>

                <div class="loan-block__body">
                    <div class="loan-block__gauge">
                        <RadialBar :series="[Number(givenProgress.toFixed(1))]" width="104px" height="118" />
                    </div>

                    <div class="loan-block__numbers">
                        <p class="loan-block__due-label">Saldo restante</p>
                        <p class="loan-block__due-value">{{ formatMoney(givenRemaining) }}</p>
                        <dl class="loan-block__mini">
                            <div>
                                <dt>Recuperado</dt>
                                <dd class="is-paid">{{ formatMoney(givenPaid) }}</dd>
                            </div>
                            <div>
                                <dt>Total</dt>
                                <dd>{{ formatMoney(givenTotal) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </section>

            <el-button
                class="loan-status__link"
                link
                type="primary"
                size="small"
                @click="router.get(route('loans.index'))"
            >
                Ver todos los préstamos
                <el-icon class="ml-1"><ArrowRight /></el-icon>
            </el-button>
        </div>

        <!-- Empty state -->
        <div v-else class="empty-state py-6">
            <el-icon :size="32" class="empty-state-icon"><TrendCharts /></el-icon>
            <p class="empty-state-title">Sin préstamos activos</p>
            <p class="empty-state-text">
                Registra un préstamo para seguir aquí su avance y su saldo pendiente.
            </p>
            <el-button type="primary" :icon="Plus" class="mt-4" @click="router.get(route('loans.create'))">
                Nuevo préstamo
            </el-button>
        </div>
    </div>
</template>
