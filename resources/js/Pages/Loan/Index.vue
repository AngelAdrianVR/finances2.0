<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Plus, Search, Upload, Download } from '@element-plus/icons-vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import LoansGiven from './Tabs/LoansGiven.vue';
import LoansForMe from './Tabs/LoansForMe.vue';
import axios from 'axios';

const props = defineProps({ loans_for_me: Object, loans_given: Object });

const activeTab = ref('1');
const searchQuery = ref('');
const searchedWord = ref(null);
const loading = ref(false);
const filteredLoansForMe = ref(props.loans_for_me);
const filteredLoansGiven = ref(props.loans_given);

async function handleSearch() {
    loading.value = true;
    searchedWord.value = searchQuery.value;
    searchQuery.value = '';
    try {
        if (!searchedWord.value) {
            filteredLoansForMe.value = props.loans_for_me;
            filteredLoansGiven.value = props.loans_given;
        } else {
            const response = await axios.post(route('loans.get-matches', { query: searchedWord.value }));
            if (response.status === 200) {
                if (activeTab.value === '1') filteredLoansGiven.value = response.data.items;
                else filteredLoansForMe.value = response.data.items;
            }
        }
    } catch (error) { console.error(error); ElMessage.error('Error al buscar'); }
    finally { loading.value = false; }
}

function closeTag() {
    searchedWord.value = null;
    filteredLoansForMe.value = props.loans_for_me;
    filteredLoansGiven.value = props.loans_given;
}

function handleTabChange(tab) {
    const url = new URL(window.location.href);
    url.searchParams.set('currentTab', tab.props.name);
    const p = url.searchParams.get('page');
    url.searchParams.delete('page');
    window.history.replaceState({}, document.title, url.href);
    if (p) location.reload();
    closeTag();
}

const currentURL = new URL(window.location.href);
const currentTabFromURL = currentURL.searchParams.get('currentTab');
if (currentTabFromURL) activeTab.value = currentTabFromURL;
</script>

<template>
    <AppLayout title="Prestamos">
        <main class="py-6 px-4 md:px-8 max-w-7xl mx-auto">
            <h1 class="page-header">Prestamos</h1>
            <section class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div class="flex items-center gap-3 sm:w-1/3 w-full">
                    <el-input v-model="searchQuery" placeholder="Buscar..." :prefix-icon="Search" clearable class="search-bar w-full" @keydown.enter="handleSearch" @clear="closeTag" />
                    <el-tag v-if="searchedWord" closable type="warning" @close="closeTag">{{ searchedWord }}</el-tag>
                </div>
                <el-button type="warning" :icon="Plus" @click="router.get(route('loans.create'))">Nuevo prestamo</el-button>
            </section>
            <div v-if="loading" class="space-y-4"><el-skeleton animated :rows="8" /></div>
            <el-tabs v-else v-model="activeTab" class="income-tabs" @tab-change="handleTabChange">
                <el-tab-pane label="Otorgados" name="1">
                    <template #label><span class="flex items-center gap-1.5"><el-icon :size="14"><Upload /></el-icon>Otorgados</span></template>
                    <LoansGiven :loans="filteredLoansGiven" />
                </el-tab-pane>
                <el-tab-pane label="Recibidos" name="2">
                    <template #label><span class="flex items-center gap-1.5"><el-icon :size="14"><Download /></el-icon>Recibidos</span></template>
                    <LoansForMe :loans="filteredLoansForMe" />
                </el-tab-pane>
            </el-tabs>
        </main>
    </AppLayout>
</template>
