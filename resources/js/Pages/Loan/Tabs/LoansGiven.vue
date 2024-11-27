<template>
    <main>
        <!-- tabla starts -->
        <div class="mx-2 lg:mx-10 mt-6">
            <div class="lg:flex justify-between mb-2">

                <!-- pagination -->
                <div class="overflow-auto mb-2">
                    <PaginationWithNoMeta :pagination="loans" class="py-2" />
                </div>

                <!-- buttons -->
                <div class="mt-2 lg:mt-0">
                    <button @click="deleteSelections" class="rounded-full bg-[#F5BABA] disabled:bg-grayD9 disabled:cursor-not-allowed size-8" :disabled="disableMassiveActions">
                        <i :class="disableMassiveActions ? 'text-gray9A' : 'text-red-600'" class="fa-regular fa-trash-can text-sm "></i>
                    </button>
                </div>
            </div>

            <el-table :data="loans.data" @row-click="handleRowClick" max-height="670" style="width: 90%"
                @selection-change="handleSelectionChange" ref="multipleTableRef"
                :row-class-name="tableRowClassName">
                <el-table-column type="selection" width="30" />
                <el-table-column label="Folio" width="80">
                    <template #default="scope">
                        <p>O-{{ scope.row.id?.toString().padStart(3, '0') }}</p>
                    </template>
                </el-table-column>
                <el-table-column prop="beneficiary_name" label="Beneficiario" width="200" />
                <el-table-column label="Fecha del préstamo" width="150">
                    <template #default="scope">
                        <p>{{ formatDate(scope.row.loan_date) }}</p>
                    </template>
                </el-table-column>
                <el-table-column label="Monto" width="150">
                    <template #default="scope">
                        <div class="flex items-center space-x-2">
                            <el-tooltip v-if="scope.row.automatically_created" effect="dark" content="El registro se ha realizado automáticamente porque el ingreso es recurrente" placement="top">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-green-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                            </el-tooltip>
                            <p>${{ scope.row.amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="Tipo de interés" width="150">
                    <template #default="scope">
                        <p>{{ scope.row.profitability_type ?? '-' }}</p>
                    </template>
                </el-table-column>
                <el-table-column label="Saldo pendiente" width="140">
                    <template #default="scope">
                        <p>
                            <!-- <i v-if="condition" class="fa-solid fa-check"></i> -->
                            <span>{{ scope.row.payment_method ?? '-' }}</span>
                        </p>
                    </template>
                </el-table-column>
                <el-table-column label="Vence" width="150">
                    <template #default="scope">
                        <p>{{ formatDate(scope.row.expired_date) ?? '-' }}</p>
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
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
data() {
    return {
        //tabla
        disableMassiveActions: true,
    }
},
components:{
    PaginationWithNoMeta,
    PrimaryButton,
},
props:{
    loans: Object
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

        this.$inertia.get(route('loans.' + commandName, rowId));
    },
    async deleteSelections() {
        this.$confirm('¿Estás seguro que deseas continuar con la eliminación?', 'Confirmar', {
            confirmButtonText: 'Sí',
            cancelButtonText: 'No',
            type: 'warning'
        }).then(async () => {
            try {
                const response = await axios.post(route('loans.massive-delete', {
                    loans: this.$refs.multipleTableRef.value
                }));

                if (response.status === 200) {
                    this.$message({
                        type: 'success',
                        message: 'Eliminación correcta'
                    });

                    // update list of loans
                    let deletedIndexes = [];
                    this.loans.data.forEach((income, index) => {
                        if (this.$refs.multipleTableRef.value.includes(income)) {
                            deletedIndexes.push(index);
                        }
                    });

                    deletedIndexes.sort((a, b) => b - a);

                    for (const index of deletedIndexes) {
                        this.loans.data.splice(index, 1);
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
        this.$inertia.get(route('loans.show', row.id));

        //en otra pestaña
        // const url = this.route('recurring-loans.show', row.id);
        // window.open(url, '_blank');
    },
    tableRowClassName({ row, rowIndex }) {
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
