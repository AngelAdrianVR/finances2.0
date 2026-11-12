<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { Plus, Search, ArrowUp, ArrowDown, Refresh, TrendCharts, Coin, CreditCard } from '@element-plus/icons-vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Outcomes from './Tabs/Outcomes.vue';
import RecurringOutcomes from './Tabs/RecurringOutcomes.vue';

const props = defineProps({
    outcomes: Object,
    recurring_outcomes: Object,
    summary: { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({ q: '', category: null, payment_method: null, type: null }) },
    filtered: { type: Object, default: () => ({ count: 0, total: 0 }) },
});

const activeTab = ref('1');
const searchQuery = ref(props.filters?.q ?? '');
const showStats = ref(false);

let searchTimer = null;

const createButtonLabel = computed(() =>
    activeTab.value === '1' ? 'Nuevo gasto' : 'Nuevo recurrente'
);

const currency = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    minimumFractionDigits: 2,
});

const statCards = computed(() => [
    {
        key: 'total',
        label: 'Total acumulado',
        value: currency.format(Number(props.summary?.total ?? 0)),
        hint: `${props.summary?.count ?? 0} gastos registrados`,
        icon: TrendCharts,
        tone: 'danger',
        delta: null,
    },
    {
        key: 'month',
        label: 'Este mes',
        value: currency.format(Number(props.summary?.current_month ?? 0)),
        hint: props.summary?.month_label ?? '',
        icon: Coin,
        tone: 'info',
        delta: Number(props.summary?.month_change ?? 0),
    },
    {
        key: 'average',
        label: 'Promedio por gasto',
        value: currency.format(Number(props.summary?.average ?? 0)),
        hint: props.summary?.top_category
            ? `Mayor: ${props.summary.top_category.key}`
            : 'Monto medio histórico',
        icon: Refresh,
        tone: 'accent',
        delta: null,
    },
    {
        key: 'credit',
        label: 'Por pagar a crédito',
        value: currency.format(Number(props.summary?.credit_pending ?? 0)),
        hint: `${props.summary?.credit_pending_count ?? 0} pendiente(s)`,
        icon: CreditCard,
        tone: 'warning',
        delta: null,
    },
]);

function applySearch(term) {
    const params = { page: 1 };

    if (term) params.q = term;
    if (props.filters?.category) params.category = props.filters.category;
    if (props.filters?.payment_method) params.payment_method = props.filters.payment_method;
    if (props.filters?.type) params.type = props.filters.type;
    if (props.outcomes?.per_page) params.per_page = props.outcomes.per_page;

    router.get(route('outcomes.index'), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function onSearchInput() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => applySearch(searchQuery.value?.trim() ?? ''), 450);
}

function handleSearch() {
    clearTimeout(searchTimer);
    applySearch(searchQuery.value?.trim() ?? '');
}

function clearSearch() {
    searchQuery.value = '';
    applySearch('');
}

function handleCreate() {
    router.get(activeTab.value === '1' ? route('outcomes.create') : route('recurring-outcomes.create'));
}

function handleTabChange(nameOrPane) {
    const tabName = typeof nameOrPane === 'object' ? nameOrPane?.props?.name : nameOrPane;

    if (!tabName) return;

    const url = new URL(window.location.href);
    url.searchParams.set('currentTab', tabName);
    url.searchParams.delete('page');
    window.history.replaceState({}, document.title, url.href);
}

const currentURL = new URL(window.location.href);
const currentTabFromURL = currentURL.searchParams.get('currentTab');
if (currentTabFromURL) activeTab.value = currentTabFromURL;
</script>

<template>
    <AppLayout title="Gastos">
        <main class="page-shell">
            <!-- Heading -->
            <header class="page-heading">
                <div class="page-heading__text">
                    <p class="page-heading__eyebrow page-heading__eyebrow--expense">Transacciones</p>
                    <h1 class="page-heading__title">Gastos</h1>
                    <p class="page-heading__subtitle">
                        Controla en qué se va tu dinero y detecta a tiempo los pagos pendientes.
                    </p>
                </div>

                <div class="page-heading__actions">
                    <form class="page-search" role="search" @submit.prevent="handleSearch">
                        <el-input
                            v-model="searchQuery"
                            placeholder="Buscar gasto…"
                            :prefix-icon="Search"
                            clearable
                            enterkeyhint="search"
                            @input="onSearchInput"
                            @clear="clearSearch"
                        />
                    </form>

                    <el-button type="primary" :icon="Plus" @click="handleCreate">
                        {{ createButtonLabel }}
                    </el-button>
                </div>
            </header>

            <!-- Compact summary (mobile): tap to reveal every stat -->
            <button
                type="button"
                class="stat-summary-mobile"
                :class="{ 'is-open': showStats }"
                :aria-expanded="showStats"
                @click="showStats = !showStats"
            >
                <span class="stat-summary-mobile__icon" :class="`stat-summary-mobile__icon--${statCards[0].tone}`">
                    <el-icon :size="18"><component :is="statCards[0].icon" /></el-icon>
                </span>
                <span class="stat-summary-mobile__body">
                    <span class="stat-summary-mobile__label">{{ statCards[0].label }}</span>
                    <span class="stat-summary-mobile__value">{{ statCards[0].value }}</span>
                    <span class="stat-summary-mobile__hint">{{ statCards[0].hint }}</span>
                </span>
                <span class="stat-summary-mobile__toggle">
                    {{ showStats ? 'Ocultar' : 'Resumen' }}
                    <el-icon :size="14" class="stat-summary-mobile__chevron"><ArrowDown /></el-icon>
                </span>
            </button>

            <!-- Stat cards -->
            <section class="stat-grid" :class="{ 'is-open': showStats }">
                <article
                    v-for="card in statCards"
                    :key="card.key"
                    class="stat-card"
                    :class="`stat-card--${card.tone}`"
                >
                    <div class="stat-card__head">
                        <span class="stat-card__icon">
                            <el-icon :size="16"><component :is="card.icon" /></el-icon>
                        </span>
                        <span
                            v-if="card.delta !== null"
                            class="stat-card__delta"
                            :class="Number(card.delta) <= 0 ? 'is-up' : 'is-down'"
                        >
                            {{ Number(card.delta) >= 0 ? '▲' : '▼' }} {{ Math.abs(Number(card.delta)) }}%
                        </span>
                    </div>
                    <p class="stat-card__label">{{ card.label }}</p>
                    <p class="stat-card__value">{{ card.value }}</p>
                    <p class="stat-card__hint">{{ card.hint }}</p>
                </article>
            </section>

            <!-- Active search chip -->
            <div v-if="filters.q" class="search-chip-row">
                <span class="search-chip">
                    Búsqueda: <strong>{{ filters.q }}</strong>
                    <button type="button" class="search-chip__close" @click="clearSearch">×</button>
                </span>
            </div>

            <!-- Tabs -->
            <el-tabs v-model="activeTab" class="income-tabs" @tab-change="handleTabChange">
                <el-tab-pane name="1">
                    <template #label>
                        <span class="tab-label">
                            <el-icon :size="14"><ArrowUp /></el-icon>
                            Gastos
                        </span>
                    </template>
                    <Outcomes :outcomes="outcomes" :filters="filters" :filtered="filtered" />
                </el-tab-pane>

                <el-tab-pane name="2">
                    <template #label>
                        <span class="tab-label">
                            <el-icon :size="14"><Refresh /></el-icon>
                            Recurrentes
                        </span>
                    </template>
                    <RecurringOutcomes :recurring_outcomes="recurring_outcomes" />
                </el-tab-pane>
            </el-tabs>
        </main>
    </AppLayout>
</template>
