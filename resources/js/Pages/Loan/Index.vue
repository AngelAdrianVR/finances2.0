<script setup>
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Plus, Search, Download, Upload, TrendCharts, Clock } from '@element-plus/icons-vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import LoansGiven from './Tabs/LoansGiven.vue';
import LoansForMe from './Tabs/LoansForMe.vue';
import { formatCurrency } from '@/utils/loans';

const props = defineProps({
    loans_for_me: Object,
    loans_given: Object,
    summary: { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({ q: '', per_page: 50 }) },
});

const activeTab = ref('1');
const searchQuery = ref(props.filters?.q ?? '');
const searching = ref(false);

let searchTimer = null;

const givenSummary = computed(() => props.summary?.given ?? {});
const receivedSummary = computed(() => props.summary?.received ?? {});

const statCards = computed(() => [
    {
        key: 'given-total',
        label: 'Total otorgado',
        value: formatCurrency(givenSummary.value.total),
        hint: `${givenSummary.value.count ?? 0} préstamo(s) · ${givenSummary.value.active_count ?? 0} en curso`,
        icon: Upload,
        tone: 'primary',
    },
    {
        key: 'given-remaining',
        label: 'Por cobrar',
        value: formatCurrency(givenSummary.value.remaining),
        hint: `${givenSummary.value.progress ?? 0}% del capital recuperado`,
        icon: TrendCharts,
        tone: 'success',
    },
    {
        key: 'received-total',
        label: 'Total recibido',
        value: formatCurrency(receivedSummary.value.total),
        hint: `${receivedSummary.value.count ?? 0} préstamo(s) · ${receivedSummary.value.active_count ?? 0} en curso`,
        icon: Download,
        tone: 'info',
    },
    {
        key: 'received-remaining',
        label: 'Por pagar',
        value: formatCurrency(receivedSummary.value.remaining),
        hint: `${receivedSummary.value.progress ?? 0}% del capital liquidado`,
        icon: Clock,
        tone: 'warning',
    },
]);

function applySearch(term) {
    const params = { page: 1 };

    if (term) params.q = term;
    if (props.filters?.per_page) params.per_page = props.filters.per_page;

    router.get(route('loans.index'), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onStart: () => {
            searching.value = true;
        },
        onFinish: () => {
            searching.value = false;
        },
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

function handleTabChange(nameOrPane) {
    const tabName = typeof nameOrPane === 'object' ? nameOrPane?.props?.name : nameOrPane;

    if (!tabName) return;

    const url = new URL(window.location.href);
    url.searchParams.set('currentTab', tabName);
    const paginationURL = url.searchParams.get('page');
    url.searchParams.delete('page');
    window.history.replaceState({}, document.title, url.href);
    if (paginationURL) location.reload();
}

const currentURL = new URL(window.location.href);
const currentTabFromURL = currentURL.searchParams.get('currentTab');
if (currentTabFromURL) activeTab.value = currentTabFromURL;
</script>

<template>
    <AppLayout title="Préstamos">
        <main class="page-shell">
            <!-- Heading -->
            <header class="page-heading">
                <div class="page-heading__text">
                    <p class="page-heading__eyebrow">Finanzas · Préstamos</p>
                    <h1 class="page-heading__title">Préstamos</h1>
                    <p class="page-heading__subtitle">
                        Lleva el control del dinero que prestas y del que te deben, con sus abonos e intereses.
                    </p>
                </div>

                <div class="page-heading__actions loan-heading__actions">
                    <form class="page-search" role="search" @submit.prevent="handleSearch">
                        <el-input
                            v-model="searchQuery"
                            placeholder="Buscar préstamo…"
                            :prefix-icon="Search"
                            clearable
                            enterkeyhint="search"
                            @input="onSearchInput"
                            @clear="clearSearch"
                        />
                    </form>

                    <el-button type="primary" :icon="Plus" @click="router.get(route('loans.create'))">
                        Nuevo préstamo
                    </el-button>
                </div>
            </header>

            <!-- Stat cards -->
            <section class="stat-grid loan-stat-grid">
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
            <div v-loading="searching" element-loading-text="Buscando préstamos…" class="loan-tabs-wrap">
                <el-tabs v-model="activeTab" class="income-tabs" @tab-change="handleTabChange">
                    <el-tab-pane name="1">
                        <template #label>
                            <span class="tab-label">
                                <el-icon :size="14"><Upload /></el-icon>
                                Otorgados
                                <span class="tab-count">{{ loans_given?.total ?? 0 }}</span>
                            </span>
                        </template>
                        <LoansGiven :loans="loans_given" :summary="givenSummary" />
                    </el-tab-pane>

                    <el-tab-pane name="2">
                        <template #label>
                            <span class="tab-label">
                                <el-icon :size="14"><Download /></el-icon>
                                Recibidos
                                <span class="tab-count">{{ loans_for_me?.total ?? 0 }}</span>
                            </span>
                        </template>
                        <LoansForMe :loans="loans_for_me" :summary="receivedSummary" />
                    </el-tab-pane>
                </el-tabs>
            </div>
        </main>
    </AppLayout>
</template>
