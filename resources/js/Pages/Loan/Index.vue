<template>
    <AppLayout title="Préstamos">
        <main class="px-2 md:px-10 pt-1 pb-16">
            <h1 class="font-bold my-3 ml-4 text-lg">Préstamos</h1>

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
                    <PrimaryButton @click="$inertia.get(route('loans.create'))"><i class="fa-solid fa-plus mr-2"></i>Nuevo préstamo</PrimaryButton>
                </div>
            </section>

            <LoadingLogo v-if="loading" class="mt-4 lg:mt-20" />

            <!-- Tabs -->
            <el-tabs v-else v-model="activeTab" class="mx-5 mt-3" @tab-click="handleClick">
                <el-tab-pane label="Otorgados" name="1">
                    <template #label>
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75 12 3m0 0 3.75 3.75M12 3v18" />
                            </svg>
                            <p>Otorgados</p>
                        </div>
                    </template>
                    <LoansGiven :loans="filteredLoansGiven" />
                </el-tab-pane>
                <el-tab-pane label="Recibidos" name="2">
                    <template #label>
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25 12 21m0 0-3.75-3.75M12 21V3" />
                            </svg>
                            <p>Recibidos</p>
                        </div>
                    </template>
                    <LoansForMe :loans="filteredLoansForMe" />
                </el-tab-pane>
            </el-tabs>
        </main>
    </AppLayout>
</template>

<script>
import LoansForMe from './Tabs/LoansForMe.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import LoansGiven from './Tabs/LoansGiven.vue';
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
        filteredLoansForMe: this.loans_for_me,
        filteredLoansGiven: this.loans_given,
    }
},
components:{
    PrimaryButton,
    LoadingLogo,
    LoansGiven,
    LoansForMe,
    AppLayout,
    AppLayout,
},
props:{
    loans_for_me: Object,
    loans_given: Object
},
methods:{
    async handleSearch() {
        this.loading = true;
        this.searchedWord = this.searchQuery;
        this.searchQuery = null;
        try {
            if (!this.searchedWord) {
                this.filteredLoansForMe = this.loans_for_me.data;
                this.filteredLoansGiven = this.loans_given.data;
            } else {
                //si esta en la pestaña 1 de ingresos
                if ( this.activeTab === '2' ) {
                    const response = await axios.post(route('loans.get-matches', { query: this.searchedWord }));
                    if (response.status === 200) {
                        this.filteredLoansForMe = response.data.items;
                    }
                } else {
                    const response = await axios.post(route('loans.get-matches', { query: this.searchedWord }));
                    if (response.status === 200) {
                        this.filteredLoansGiven = response.data.items;
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
    closedTag() {
        this.searchedWord = null;
        this.filteredLoansForMe = this.loans_for_me;
        this.filteredLoansGiven = this.loans_given;
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
