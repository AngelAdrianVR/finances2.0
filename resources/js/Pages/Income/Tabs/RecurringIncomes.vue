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
                <el-table-column label="Método de pago">
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
                                    <el-dropdown-item :command="'show-' + scope.row.id">
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
</template>

<script>
import PaginationWithNoMeta from "@/Components/MyComponents/PaginationWithNoMeta.vue";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import axios from 'axios';

export default {
data() {
    return {
        //tabla
        disableMassiveActions: true,
    }
},
components:{
    PaginationWithNoMeta,
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
        } else {
            this.$inertia.get(route('recurring-incomes.' + commandName, rowId));
        }
    },
    async toggleStatus(rowId) {
        try {
            const response = await axios.post(route('recurring-incomes.toggle-status', rowId));

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
        this.$inertia.get(route('recurring-incomes.show', row.id))
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
