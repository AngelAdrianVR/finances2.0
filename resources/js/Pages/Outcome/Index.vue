<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { Plus, Search, ArrowUp, Refresh } from '@element-plus/icons-vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Outcomes from './Tabs/Outcomes.vue';
import RecurringOutcomes from './Tabs/RecurringOutcomes.vue';
import axios from 'axios';

const props = defineProps({
    outcomes: Object,
    recurring_outcomes: Object,
});

const activeTab = ref('1');
const searchQuery = ref('');
const searchedWord = ref(null);
const loading = ref(false);

const filteredOutcomes = ref(props.outcomes);
const filteredRecurringOutcomes = ref(props.recurring_outcomes);

const createButtonLabel = computed(() =>
    activeTab.value === '1' ? 'Nuevo gasto' : 'Nuevo gasto recurrente'
);

async function handleSearch() {
    loading.value = true;
    searchedWord.value = searchQuery.value;
    searchQuery.value = '';
    try {
        if (!searchedWord.value) {
            filteredOutcomes.value = props.outcomes;
            filteredRecurringOutcomes.value = props.recurring_outcomes;
        } else {
            if (activeTab.value === '1') {
                const response = await axios.post(route('outcomes.get-matches', { query: searchedWord.value }));
                if (response.status === 200) filteredOutcomes.value = response.data.items;
            } else {
                const response = await axios.post(route('recurring-outcomes.get-matches', { query: searchedWord.value }));
                if (response.status === 200) filteredRecurringOutcomes.value = response.data.items;
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
    filteredOutcomes.value = props.outcomes;
    filteredRecurringOutcomes.value = props.recurring_outcomes;
}

function handleCreate() {
    router.get(activeTab.value === '1' ? route('outcomes.create') : route('recurring-outcomes.create'));
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

const currentURL = new URL(window.location.href);
const currentTabFromURL = currentURL.searchParams.get('currentTab');
if (currentTabFromURL) activeTab.value = currentTabFromURL;
</script>

<template>
    <AppLayout title="Gastos">
        <main class="py-6 px-4 md:px-8 max-w-7xl mx-auto">
            <h1 class="page-header">Gastos</h1>

            <section class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div class="flex items-center gap-3 sm:w-1/3 w-full">
                    <el-input v-model="searchQuery" placeholder="Buscar..." :prefix-icon="Search" clearable
                        class="search-bar w-full" @keydown.enter="handleSearch" @clear="closeTag" />
                    <el-tag v-if="searchedWord" closable type="danger" @close="closeTag">{{ searchedWord }}</el-tag>
                </div>
                <el-button type="danger" :icon="Plus" @click="handleCreate">{{ createButtonLabel }}</el-button>
            </section>

            <div v-if="loading" class="space-y-4">
                <el-skeleton animated :rows="8" />
            </div>

            <el-tabs v-else v-model="activeTab" class="income-tabs" @tab-change="handleTabChange">
                <el-tab-pane label="Gastos" name="1">
                    <template #label>
                        <span class="flex items-center gap-1.5">
                            <el-icon :size="14"><ArrowUp /></el-icon>
                            Gastos
                        </span>
                    </template>
                    <Outcomes :outcomes="filteredOutcomes" />
                </el-tab-pane>
                <el-tab-pane label="Gastos recurrentes" name="2">
                    <template #label>
                        <span class="flex items-center gap-1.5">
                            <el-icon :size="14"><Refresh /></el-icon>
                            Gastos recurrentes
                        </span>
                    </template>
                    <RecurringOutcomes :recurring_outcomes="filteredRecurringOutcomes" />
                </el-tab-pane>
            </el-tabs>
        </main>
    </AppLayout>
</template>
