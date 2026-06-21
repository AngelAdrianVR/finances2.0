<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { Plus, Search, ArrowDown, Refresh } from '@element-plus/icons-vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Incomes from './Tabs/Incomes.vue';
import RecurringIncomes from './Tabs/RecurringIncomes.vue';
import axios from 'axios';

const props = defineProps({
    incomes: Object,
    recurring_incomes: Object,
});

// Tab state
const activeTab = ref('1');

// Search state
const searchQuery = ref('');
const searchedWord = ref(null);
const loading = ref(false);

// Filtered data
const filteredIncomes = ref(props.incomes);
const filteredRecurringIncomes = ref(props.recurring_incomes);

// Button label
const createButtonLabel = computed(() =>
    activeTab.value === '1' ? 'Nuevo ingreso' : 'Nuevo ingreso recurrente'
);

// Search
async function handleSearch() {
    loading.value = true;
    searchedWord.value = searchQuery.value;
    searchQuery.value = '';

    try {
        if (!searchedWord.value) {
            filteredIncomes.value = props.incomes;
            filteredRecurringIncomes.value = props.recurring_incomes;
        } else {
            if (activeTab.value === '1') {
                const response = await axios.post(route('incomes.get-matches', { query: searchedWord.value }));
                if (response.status === 200) filteredIncomes.value = response.data.items;
            } else {
                const response = await axios.post(route('recurring-incomes.get-matches', { query: searchedWord.value }));
                if (response.status === 200) filteredRecurringIncomes.value = response.data.items;
            }
        }
    } catch (error) {
        console.error(error);
        ElMessage.error('Error al buscar');
    } finally {
        loading.value = false;
    }
}

function closeTag() {
    searchedWord.value = null;
    filteredIncomes.value = props.incomes;
    filteredRecurringIncomes.value = props.recurring_incomes;
}

function handleCreate() {
    if (activeTab.value === '1') {
        router.get(route('incomes.create'));
    } else {
        router.get(route('recurring-incomes.create'));
    }
}

function handleTabChange(tab) {
    const url = new URL(window.location.href);
    url.searchParams.set('currentTab', tab.props.name);
    const paginationURL = url.searchParams.get('page');
    url.searchParams.delete('page');
    window.history.replaceState({}, document.title, url.href);
    if (paginationURL) location.reload();
    closeTag();
}

// Restore tab from URL on mount
const currentURL = new URL(window.location.href);
const currentTabFromURL = currentURL.searchParams.get('currentTab');
if (currentTabFromURL) activeTab.value = currentTabFromURL;
</script>

<template>
    <AppLayout title="Ingresos">
        <main class="py-6 px-4 md:px-8 max-w-7xl mx-auto">
            <h1 class="page-header">Ingresos</h1>

            <!-- Toolbar -->
            <section class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <!-- Search -->
                <div class="flex items-center gap-3 sm:w-1/3 w-full">
                    <el-input
                        v-model="searchQuery"
                        placeholder="Buscar..."
                        :prefix-icon="Search"
                        clearable
                        class="search-bar w-full"
                        @keydown.enter="handleSearch"
                        @clear="closeTag"
                    />
                    <el-tag
                        v-if="searchedWord"
                        closable
                        type="primary"
                        @close="closeTag"
                    >
                        {{ searchedWord }}
                    </el-tag>
                </div>

                <!-- Create button -->
                <el-button type="primary" :icon="Plus" @click="handleCreate">
                    {{ createButtonLabel }}
                </el-button>
            </section>

            <!-- Loading skeleton -->
            <div v-if="loading" class="space-y-4">
                <el-skeleton animated :rows="8" />
            </div>

            <!-- Tabs -->
            <el-tabs v-else v-model="activeTab" class="income-tabs" @tab-change="handleTabChange">
                <el-tab-pane label="Ingresos" name="1">
                    <template #label>
                        <span class="flex items-center gap-1.5">
                            <el-icon :size="14"><ArrowDown /></el-icon>
                            Ingresos
                        </span>
                    </template>
                    <Incomes :incomes="filteredIncomes" />
                </el-tab-pane>
                <el-tab-pane label="Ingresos recurrentes" name="2">
                    <template #label>
                        <span class="flex items-center gap-1.5">
                            <el-icon :size="14"><Refresh /></el-icon>
                            Ingresos recurrentes
                        </span>
                    </template>
                    <RecurringIncomes :recurring_incomes="filteredRecurringIncomes" />
                </el-tab-pane>
            </el-tabs>
        </main>
    </AppLayout>
</template>
