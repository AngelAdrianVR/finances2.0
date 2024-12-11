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
                    <button @click="showDeleteConfirmation = true"
                        class="rounded-full bg-[#F5BABA] disabled:bg-grayD9 disabled:cursor-not-allowed size-8"
                        :disabled="disableMassiveActions">
                        <i :class="disableMassiveActions ? 'text-gray9A' : 'text-red-600'"
                            class="fa-regular fa-trash-can text-sm "></i>
                    </button>
                </div>
            </div>

            <el-table :data="loans.data" @row-click="handleRowClick" max-height="670" style="width: 90%"
                @selection-change="handleSelectionChange" ref="multipleTableRef" :row-class-name="tableRowClassName">
                <el-table-column type="selection" width="30" />
                <el-table-column label="Folio" width="80">
                    <template #default="scope">
                        <p>O-{{ scope.row.id?.toString().padStart(3, '0') }}</p>
                    </template>
                </el-table-column>
                <el-table-column prop="lender_name" label="Prestamista" width="140" />
                <el-table-column label="Fecha del préstamo" width="150">
                    <template #default="scope">
                        <p>{{ formatDate(scope.row.loan_date) }}</p>
                    </template>
                </el-table-column>
                <el-table-column label="Monto" width="120">
                    <template #default="scope">
                        <div class="flex items-center space-x-2">
                            <el-tooltip v-if="scope.row.automatically_created" effect="dark"
                                content="El registro se ha realizado automáticamente porque el ingreso es recurrente"
                                placement="top">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4 text-green-600">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
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
                        <p class="flex items-center space-x-1">
                            <i v-if="!getRemaining(scope.row)" class="fa-solid fa-check text-[#0CBE3B]"></i>
                            <svg v-else width="14" height="14" viewBox="0 0 12 12" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <mask id="mask0_14469_251" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                    y="0" width="12" height="12">
                                    <rect width="12" height="12" fill="#D9D9D9" />
                                </mask>
                                <g mask="url(#mask0_14469_251)">
                                    <path
                                        d="M3.925 10.5625C3.05833 10.1625 2.35417 9.55833 1.8125 8.75C1.27083 7.94167 1 7.02917 1 6.0125C1 5.79583 1.01042 5.58333 1.03125 5.375C1.05208 5.16667 1.0875 4.9625 1.1375 4.7625L0.5625 5.1L0.0625 4.2375L2.45 2.8625L3.825 5.2375L2.95 5.7375L2.275 4.5625C2.18333 4.7875 2.11458 5.02083 2.06875 5.2625C2.02292 5.50417 2 5.75417 2 6.0125C2 6.82083 2.22083 7.55625 2.6625 8.21875C3.10417 8.88125 3.69167 9.37083 4.425 9.6875L3.925 10.5625ZM7.75 4.5V3.5H9.1125C8.72917 3.025 8.26667 2.65625 7.725 2.39375C7.18333 2.13125 6.60833 2 6 2C5.54167 2 5.10833 2.07083 4.7 2.2125C4.29167 2.35417 3.91667 2.55417 3.575 2.8125L3.075 1.9375C3.49167 1.64583 3.94583 1.41667 4.4375 1.25C4.92917 1.08333 5.45 1 6 1C6.65833 1 7.2875 1.12292 7.8875 1.36875C8.4875 1.61458 9.025 1.97083 9.5 2.4375V1.75H10.5V4.5H7.75ZM7.425 12L5.0375 10.625L6.4125 8.25L7.275 8.75L6.5625 9.975C7.54583 9.83333 8.36458 9.3875 9.01875 8.6375C9.67292 7.8875 10 7.00833 10 6C10 5.90833 9.99792 5.82292 9.99375 5.74375C9.98958 5.66458 9.97917 5.58333 9.9625 5.5H10.975C10.9833 5.58333 10.9896 5.66458 10.9938 5.74375C10.9979 5.82292 11 5.90833 11 6C11 7.125 10.6646 8.13125 9.99375 9.01875C9.32292 9.90625 8.45 10.5042 7.375 10.8125L7.925 11.1375L7.425 12Z"
                                        fill="#3305BD" />
                                </g>
                            </svg>
                            <span>
                                ${{ getRemaining(scope.row).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                            </span>
                        </p>
                    </template>
                </el-table-column>
                <el-table-column label="Vence" width="130">
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

        <DeleteConfirmationModal :show="showDeleteConfirmation" @close="showDeleteConfirmation = false">
            <template #title>
                <div class="text-center">
                    <p>¿Estas seguro que deseas eliminar el/los préstamos seleccionados?</p>
                </div>
            </template>
            <template #content></template>
            <template #footer>
                <div class="w-full flex items-center justify-center space-x-2">
                    <CancelButton @click="showDeleteConfirmation = false">No, Cancelar
                    </CancelButton>
                    <DangerButton @click="deleteSelections" :disabled="deleting">
                        <i v-if="deleting" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Sí, estoy seguro
                    </DangerButton>
                </div>
            </template>
        </DeleteConfirmationModal>
    </main>
</template>

<script>
import CancelButton from "@/Components/CancelButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import DeleteConfirmationModal from "@/Components/DeleteConfirmationModal.vue";
import PaginationWithNoMeta from "@/Components/MyComponents/PaginationWithNoMeta.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    data() {
        return {
            //modales
            showDeleteConfirmation: false,
            //tabla
            disableMassiveActions: true,
            // cargas
            deleting: false,
        }
    },
    components: {
        PaginationWithNoMeta,
        PrimaryButton,
        DeleteConfirmationModal,
        CancelButton,
        DangerButton,
    },
    props: {
        loans: Object
    },
    methods: {
        getRemaining(loan) {
            return loan.payments[loan.payments.length - 1]?.remaining
                ?? loan.amount
        },
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
            try {
                this.deleting = true;
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

                    this.showDeleteConfirmation = false;

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
            } finally {
                this.deleting = false;
            }
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
            if (date) {
                const parsedDate = new Date(date);
                return format(parsedDate, 'dd MMMM yyyy', { locale: es }); // Formato personalizado
            }
        },
    }
}
</script>
