<template>
    <AppLayout title="Ingresos">
        <main class="px-2 md:px-10 pt-1 pb-16">
            <h1 class="font-bold my-3 ml-4 text-lg">Ingresos</h1>

            <section class="md:flex justify-between items-center">
                <article class="flex items-center space-x-5 lg:w-1/3 mb-3 md:mb-0">
                    <div class="w-full relative">
                        <input v-model="searchQuery" @keydown.enter="handleSearch()" class="input w-full pl-9"
                            placeholder="Buscar" type="search"
                            ref="searchInput" />
                        <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
                    </div>
                    <el-tag @close="closedTag" v-if="searchedWord" closable type="primary">
                        {{ searchedWord }}
                    </el-tag>
                </article>

                <!-- buttons -->
                <div class="flex items-center space-x-2">
                    <PrimaryButton @click="handleCreate()"><i class="fa-solid fa-plus mr-2"></i>
                        {{ this.activeTab === '1' ? 'Nuevo Ingreso' : 'Nuevo ingreso recurrente'}}
                    </PrimaryButton>
                </div>
            </section>

            <LoadingLogo v-if="loading" class="mt-4 lg:mt-20" />

            <!-- Tabs -->
            <el-tabs v-else v-model="activeTab" class="mx-5 mt-3" @tab-click="handleClick">
                <el-tab-pane label="Ingresos" name="1">
                    <template #label>
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 5.25 7.5 7.5 7.5-7.5m-15 6 7.5 7.5 7.5-7.5" />
                            </svg>
                            <p>Ingresos</p>
                        </div>
                    </template>
                    <Incomes :incomes="filteredIncomes" />
                </el-tab-pane>
                <el-tab-pane label="Ingresos recurrentes" name="2">
                    <template #label>
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            <p>Ingresos recurrenter</p>
                        </div>
                    </template>
                    <RecurringIncomes :recurring_incomes="filteredRecurringIncomes" />
                </el-tab-pane>
            </el-tabs>
        </main>
    </AppLayout>
</template>

<script>
import Incomes from './Tabs/Incomes.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import RecurringIncomes from './Tabs/RecurringIncomes.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import LoadingLogo from "@/Components/MyComponents/LoadingLogo.vue";

export default {
data() {
    return {
        // buscador
        searchQuery: null,
        searchedWord: null,
        //carga
        loading: false,
        //tabs
        activeTab: '1',
        //tablas
        filteredIncomes: this.incomes,
        filteredRecurringIncomes: this.recurring_incomes,
    }
},
components:{
    RecurringIncomes,
    PrimaryButton,
    LoadingLogo,
    AppLayout,
    AppLayout,
    Incomes
},
props:{
    incomes: Object,
    recurring_incomes: Object
},
methods:{
    async handleSearch() {
        this.loading = true;
        this.searchedWord = this.searchQuery;
        this.searchQuery = null;
        try {
            if (!this.searchedWord) {
                this.filteredIncomes = this.incomes.data;
                this.filteredRecurringIncomes = this.recurring_incomes.data;
            } else {
                //si esta en la pestaña 1 de ingresos
                if ( this.activeTab === '1' ) {
                    const response = await axios.post(route('incomes.get-matches', { query: this.searchedWord }));
                    if (response.status === 200) {
                        this.filteredIncomes = response.data.items;
                    }
                } else {
                    const response = await axios.post(route('recurring-incomes.get-matches', { query: this.searchedWord }));
                    if (response.status === 200) {
                        this.filteredRecurringIncomes = response.data.items;
                    }
                }
            }
        } catch (error) {
            console.log(error);
            this.$message({
                type: 'error',
                message: error
            });

        } finally {
            this.loading = false;
        }
    },
    handleCreate() {
        if ( this.activeTab === '1' ) {
            this.$inertia.get(route('incomes.create'));
        } else {
            this.$inertia.get(route('recurring-incomes.create'));
        }
    },
    closedTag() {
        this.searchedWord = null;
        this.filteredIncomes = this.incomes;
        this.filteredRecurringIncomes = this.recurring_incomes;
    },
    handleClick(tab) {
        // Obtén la URL actual
        const currentURL = new URL(window.location.href);

        // Agrega la variable currentTab=tab.props.name a la URL
        currentURL.searchParams.set('currentTab', tab.props.name);

        //revisa si existe una variable de paginacion
        const paginationURL = currentURL.searchParams.get('page');

        // Elimina el parámetro de paginación "page"
        currentURL.searchParams.delete('page');

        // Actualiza la URL sin recargar la página
        window.history.replaceState({}, document.title, currentURL.href);

        //actualiza la pagina si se cambio paginacion en alguna pestaña para no afectar la otra
        if ( paginationURL ) {
            location.reload();
        }

        // Cierra las búsquedas de la otra pestaña
        this.closedTag();
    }
},
mounted() {
    // Obtener la URL actual
    const currentURL = new URL(window.location.href);
    // Extraer el valor de 'currentTab' de los parámetros de búsqueda
    const currentTabFromURL = currentURL.searchParams.get('currentTab');

    if (currentTabFromURL) {
        this.activeTab = currentTabFromURL;
    }
},
}
</script>
