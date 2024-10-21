<template>
    <AppLayout title="Ingresos">
        <main class="px-2 md:px-10 pt-1 pb-16">
            <h1 class="font-bold my-3 ml-4 text-lg">Ingresos</h1>

            <section class="md:flex justify-between items-center">
                <article class="flex items-center space-x-5 lg:w-1/3">
                    <div class="mb-3 md:mb-0 w-full relative">
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
                    <PrimaryButton @click="$inertia.get(route('incomes.create'))"><i class="fa-solid fa-plus mr-2"></i>Nuevo préstamo</PrimaryButton>
                    <div class="mt-2 lg:mt-0">
                        <button @click="deleteSelections" class="rounded-full bg-[#F5BABA] disabled:bg-grayD9 disabled:cursor-not-allowed size-8" :disabled="disableMassiveActions">
                            <i :class="{'text-gray9A': disableMassiveActions}" class="fa-regular fa-trash-can text-sm text-red-600"></i>
                        </button>
                    </div>
                </div>
            </section>

            <LoadingLogo v-if="loading" class="mt-4 lg:mt-20" />

            <!-- Tabs -->
            <el-tabs v-else v-model="activeTab" class="mx-5 mt-3" @tab-click="handleClick">
                <el-tab-pane label="Ingresos" name="1">
                    <Incomes />
                </el-tab-pane>
                <el-tab-pane label="Ingresos recurrentes" name="2">
                    <RecurringIncomes />
                </el-tab-pane>
            </el-tabs>

            <!-- tabla starts -->
            <div class="mx-2 lg:mx-10 mt-6">
                <div class="lg:flex justify-between mb-2">

                    <!-- pagination -->
                    <div v-if="!search" class="overflow-auto mb-2">
                        <PaginationWithNoMeta :pagination="incomes" class="py-2" />
                    </div>
                </div>

                <!-- <el-table :data="filteredQuotes" @row-click="handleRowClick()" max-height="670" style="width: 90%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef"
                    :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="30" />
                    <el-table-column prop="part_number_supplier" label="Num. de parte fabricante" width="200" />
                    <el-table-column prop="name" label="Nombre" width="150" />
                    <el-table-column prop="subcategory.category.name" label="Categoría" width="150" />
                    <el-table-column label="Subcategorías" width="150">
                        <template #default="scope">
                            <div class="flex flex-col">
                                <p v-for="subcategory in scope.row.bread_crumbles" :key="subcategory"
                                    class="flex text-xs space-x-2">
                                    <i class="fa-solid fa-caret-right mt-[2px]"></i>
                                    <span>{{ subcategory }}</span>
                                </p>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="location" label="Ubicación en almacén" width="150" />
                    <el-table-column prop="description" label="Descripción" />
                    <el-table-column align="right">
                        <template #default="scope">
                            <el-dropdown trigger="click" @command="handleCommand">
                                <button @click.stop
                                    class="el-dropdown-link mr-3 justify-center items-center size-8 rounded-full text-primary hover:bg-grayED transition-all duration-200 ease-in-out">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item :command="'show-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 mr-1">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            Ver</el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="$page.props.auth.user.permissions.includes('Editar productos')"
                                            :command="'edit-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 mr-1">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            Editar</el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </template>
                    </el-table-column>
                </el-table> -->
            </div>
            <!-- tabla ends -->
        </main>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import LoadingLogo from "@/Components/MyComponents/LoadingLogo.vue";
import PaginationWithNoMeta from "@/Components/MyComponents/PaginationWithNoMeta.vue";

export default {
data() {
    return {
        // buscador
        searchQuery: null,
        searchedWord: null,
        //carga
        loading: false,
        //tabla
        disableMassiveActions: true,
        filteredIncomes: this.incomes.data,
        //tabs
        activeTab: '1',
    }
},
components:{
    PaginationWithNoMeta,
    PrimaryButton,
    LoadingLogo,
    AppLayout,
},
props:{
    incomes: Object
},
methods:{
    handleClick(tab) {
      // Agrega la variable currentTab=tab.props.name a la URL para mejorar la navegacion al actalizar o cambiar de pagina
      const currentURL = new URL(window.location.href);
      currentURL.searchParams.set('currentTab', tab.props.name);
      // Actualiza la URL
      window.history.replaceState({}, document.title, currentURL.href);
    },
    async handleSearch() {
        this.loading = true;
        this.searchedWord = this.searchQuery;
        this.searchQuery = null;
        try {
            if (!this.search) {
                this.filteredQuotes = this.quotes.data;
            } else {
                const response = await axios.post(route('quotes.get-matches', { query: this.search }));

                if (response.status === 200) {
                    this.filteredQuotes = response.data.items;
                }
            }
        } catch (error) {
            console.log(error);
        } finally {
            this.loading = false;
        }
    },
    handleSelectionChange(val) {
        this.$refs.multipleTableRef.value = val;

        if (!this.$refs.multipleTableRef.value.length) {
            this.disableMassiveActions = true;
        } else {
            this.disableMassiveActions = false;
        }
    },
    async deleteSelections() {
        console.log('hola')
        this.$confirm('¿Estás seguro de eliminar este archivo?', 'Confirmar', {
            confirmButtonText: 'Sí',
            cancelButtonText: 'No',
            type: 'warning'
            }).then(() => {


            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: 'Eliminación cancelada'
                });
            });
            
        // try {
        //     const response = await axios.post(route('quotes.massive-delete', {
        //         quotes: this.$refs.multipleTableRef.value
        //     }));

        //     if (response.status == 200) {
        //         this.$notify({
        //             title: 'Éxito',
        //             message: response.data.message,
        //             type: 'success'
        //         });

        //         // update list of quotes
        //         let deletedIndexes = [];
        //         this.quotes.data.forEach((quote, index) => {
        //             if (this.$refs.multipleTableRef.value.includes(quote)) {
        //                 deletedIndexes.push(index);
        //             }
        //         });

        //         // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
        //         deletedIndexes.sort((a, b) => b - a);

        //         // Eliminar cotizaciones por índice
        //         for (const index of deletedIndexes) {
        //             this.quotes.data.splice(index, 1);
        //         }

        //     } else {
        //         this.$notify({
        //             title: 'Algo salió mal',
        //             message: response.data.message,
        //             type: 'error'
        //         });
        //     }

        // } catch (err) {
        //     this.$notify({
        //         title: 'Algo salió mal',
        //         message: err.message,
        //         type: 'error'
        //     });
        //     console.log(err);
        // }
    },
    closedTag() {
        this.searchedWord = null;
        // this.totalPagination = this.products.length / 3;
    },
    handleRowClick(row) {
        const url = this.route('incomes.show', row.id);
        window.open(url, '_blank');
    },
    tableRowClassName({ row, rowIndex }) {
        return 'cursor-pointer text-xs';
    },
}
}
</script>
