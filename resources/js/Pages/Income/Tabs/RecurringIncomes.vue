<template>
    <main>
        <!-- tabla starts -->
        <div class="mx-2 lg:mx-10 mt-6">
            <div class="lg:flex justify-between mb-2">

                <!-- pagination -->
                <div class="overflow-auto mb-2">
                    <PaginationWithNoMeta :pagination="recurring_incomes" class="py-2" />
                </div>

                <!-- buttons -->
                <div class="mt-2 lg:mt-0">
                    <button @click="deleteSelections" class="rounded-full bg-[#F5BABA] disabled:bg-grayD9 disabled:cursor-not-allowed size-8" :disabled="disableMassiveActions">
                        <i :class="disableMassiveActions ? 'text-gray9A' : 'text-red-600'" class="fa-regular fa-trash-can text-sm "></i>
                    </button>
                </div>
            </div>

            <el-table :data="recurring_incomes.data" @row-click="handleRowClick" max-height="670" style="width: 90%"
                @selection-change="handleSelectionChange" ref="multipleTableRef"
                :row-class-name="tableRowClassName">
                <el-table-column type="selection" width="30" />
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="concept" label="Concepto del ingreso" width="200" />
                <el-table-column prop="amount" label="Monto" width="150">
                    <template #default="scope">
                        <p>${{ scope.row.amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                    </template>
                </el-table-column>
                <el-table-column prop="periodicity" label="Recurrencia del ingreso" width="150" />
                <el-table-column prop="category" label="Categoría" width="150" />
                <el-table-column label="Método de pago" width="150">
                    <template #default="scope">
                        <p>{{ scope.row.payment_method ?? '-' }}</p>
                    </template>
                </el-table-column>
                <el-table-column prop="created_at" label="Creado el" width="180">
                    <template #default="scope">
                        <p>{{ formatDate(scope.row.created_at) }}</p>
                    </template>
                </el-table-column>
                <el-table-column align="right">
                    <template #default="scope">
                        <el-dropdown trigger="click" @command="handleCommand">
                            <button @click.stop
                                class="mr-3 justify-center items-center size-8 rounded-full text-primary hover:bg-grayD9 transition-all duration-200 ease-in-out">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <template #dropdown>
                                <el-dropdown-menu>
                                    <el-dropdown-item @click="itemToShow = scope.row" :command="'show-' + scope.row.id">
                                        Ver
                                    </el-dropdown-item>
                                    <el-dropdown-item :command="'edit-' + scope.row.id">
                                        Editar
                                    </el-dropdown-item>
                                    <el-dropdown-item :command="'status-' + scope.row.id">
                                        {{ scope.row.is_active ? 'Deshabilitar ingreso recurrente' : 'Habilitar ingreso recurrente'}}
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <!-- tabla ends -->
    </main>

    <!-- -------------- Show Modal starts----------------------- -->
    <DialogModal :show="showDetailsModal" @close="showDetailsModal = false">
        <template #title>
            <div class="flex items-center justify-between">
                <p class="font-bold text-left mb-5">Detalles de ingreso</p>
                <el-tooltip v-if="itemToShow.automatically_created" effect="dark" content="El registro se ha realizado automáticamente" placement="right">
                    <div class="inline-flex items-center rounded-full px-3 py-1 bg-[#DDFEC0] text-[#47B61E] text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Ingreso recurrente
                    </div>
                </el-tooltip>
            </div>
        </template>

        <template #content>
            <section class="space-y-2">
                <div class="flex">
                    <p class="text-[#7a7a7a] w-44">ID</p>
                    <p>{{ itemToShow.id }}</p>
                </div>
                <div class="flex">
                    <p class="text-[#7a7a7a] w-44">Concepto del ingreso</p>
                    <p>{{ itemToShow.concept }}</p>
                </div>
                <div class="flex">
                    <p class="text-[#7a7a7a] w-44">Fecha</p>
                    <p>{{ formatDate(itemToShow.created_at) }}</p>
                </div>
                <div class="flex">
                    <p class="text-[#7a7a7a] w-44">Monto</p>
                    <p>${{ itemToShow.amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                </div>
                <div class="flex">
                    <p class="text-[#7a7a7a] w-44">Recurrencia del ingreso</p>
                    <p>{{ itemToShow.periodicity }}</p>
                </div>
                <div class="flex">
                    <p class="text-[#7a7a7a] w-44">Categoría del ingreso</p>
                    <p>{{ itemToShow.category }}</p>
                </div>
                <div class="flex">
                    <p class="text-[#7a7a7a] w-44">Método de pago</p>
                    <p>{{ itemToShow.payment_method }}</p>
                </div>
                <div class="flex">
                    <p class="text-[#7a7a7a] w-44">Descripción</p>
                    <p>{{ itemToShow.description ?? '-' }}</p>
                </div>
            </section>
        </template>

        <template #footer>
            <button @click="$inertia.get(route('recurring-incomes.edit', itemToShow.id))" class="size-9 border border-primary text-primary rounded-full flex justify-center items-center mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                </svg>
            </button>
            <PrimaryButton @click="showDetailsModal = false">Cerrar</PrimaryButton>
        </template>
    </DialogModal>
    <!-- --------------------------- Modal ends ------------------------------------ -->
</template>

<script>
import PaginationWithNoMeta from "@/Components/MyComponents/PaginationWithNoMeta.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import axios from 'axios';

export default {
data() {
    return {
        //tabla
        disableMassiveActions: true,
        showDetailsModal: false,
        itemToShow: null,
    }
},
components:{
    PaginationWithNoMeta,
    PrimaryButton,
    DialogModal
},
props:{
    recurring_incomes: Object
},
methods:{
    handleSelectionChange(val) {
        this.$refs.multipleTableRef.value = val;

        if (!this.$refs.multipleTableRef.value.length) {
            this.disableMassiveActions = true;
        } else {
            this.disableMassiveActions = false;
        }
    },
    handleCommand(command) {
        const commandName = command.split('-')[0];
        const rowId = command.split('-')[1];

        if ( commandName === 'status' ) {
            this.toggleStatus(rowId);
        } else if ( commandName === 'show' ) {
            this.showDetailsModal = true;
        } else {
            this.$inertia.get(route('recurring-incomes.' + commandName, rowId));
        }
    },
    async toggleStatus(rowId) {
        try {
            const response = await axios.get(route('recurring-incomes.toggle-status', rowId));

            if ( response.status === 200 ) {

                const incomeIndex = this.recurring_incomes.data.findIndex(item => item.id == rowId);

                if ( incomeIndex !== -1 ) {
                    this.recurring_incomes.data[incomeIndex].is_active = response.data.is_active;
                }

                this.$message({
                    type: 'success',
                    message: 'Estatus cambiado'
                });
            }
        } catch (error) {
            console.log(error);
            this.$message({
                type: 'error',
                message: error
            });
        }
    },
    async deleteSelections() {
        this.$confirm('¿Estás seguro que deseas continuar con la eliminación?', 'Confirmar', {
            confirmButtonText: 'Sí',
            cancelButtonText: 'No',
            type: 'warning'
        }).then(async () => {
            try {
                const response = await axios.post(route('recurring-incomes.massive-delete', {
                    recurring_incomes: this.$refs.multipleTableRef.value
                }));

                if (response.status === 200) {
                    this.$message({
                        type: 'success',
                        message: 'Eliminación correcta'
                    });

                    // update list of recurring_incomes
                    let deletedIndexes = [];
                    this.recurring_incomes.data.forEach((income, index) => {
                        if (this.$refs.multipleTableRef.value.includes(income)) {
                            deletedIndexes.push(index);
                        }
                    });

                    deletedIndexes.sort((a, b) => b - a);

                    for (const index of deletedIndexes) {
                        this.recurring_incomes.data.splice(index, 1);
                    }

                } else {
                    this.$message({
                        type: 'error',
                        message: 'Algo salió mal'
                    });
                }

            } catch (err) {
                this.$message({
                    type: 'error',
                    message: 'Algo salió mal'
                });
                console.log(err);
            }
        }).catch(() => {
            this.$message({
                type: 'info',
                message: 'Eliminación cancelada'
            });
        });
            
    },
    handleRowClick(row) {
        this.itemToShow = row;
        this.showDetailsModal = true;
        // this.$inertia.get(route('recurring-incomes.show', row.id));
        //en otra pestaña
        // const url = this.route('recurring-incomes.show', row.id);
        // window.open(url, '_blank');
    },
    tableRowClassName(row) {
        if ( !row.row.is_active ) {
            return 'cursor-pointer text-xs !bg-red-50';
        } 
            return 'cursor-pointer text-xs';
    },
    formatDate(date) {
        if ( date ) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'dd MMMM yyyy', { locale: es }); // Formato personalizado
        }
    },
}
}
</script>
