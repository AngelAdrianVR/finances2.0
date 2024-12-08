<template>
    <main class="mx-2 lg:mx-10 mt-6">
        <!-- tabla starts -->
            <div class="lg:flex justify-between mb-2">

                <!-- pagination -->
                <div class="overflow-auto mb-2">
                    <PaginationWithNoMeta :pagination="bankCards" class="py-2" />
                </div>

                <!-- buttons -->
                <div class="mt-2 lg:mt-0 flex space-x-3">
                    <PrimaryButton @click="showModal = true">Registrar cuenta</PrimaryButton>
                    <button @click="deleteSelections" class="rounded-full bg-[#F5BABA] disabled:bg-grayD9 disabled:cursor-not-allowed size-8" :disabled="disableMassiveActions">
                        <i :class="disableMassiveActions ? 'text-gray9A' : 'text-red-600'" class="fa-regular fa-trash-can text-sm "></i>
                    </button>
                </div>
            </div>

            <el-table :data="bankCards?.data" @row-click="handleRowClick" max-height="670" style="width: 90%"
                @selection-change="handleSelectionChange" ref="multipleTableRef"
                :row-class-name="tableRowClassName">
                <el-table-column type="selection" width="30" />
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column label="Nombre" width="200">
                    <template #default="scope">
                        <div class="flex items-center space-x-1">
                            <el-tooltip v-if="!scope.row.is_active" content="Inactiva" placement="left" effect="dark">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <mask id="mask0_14872_40" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="14" height="14">
                                        <rect width="14" height="14" fill="#D9D9D9"/>
                                    </mask>
                                    <g mask="url(#mask0_14872_40)">
                                        <path d="M11.538 13.1973L10.2109 11.8702C9.73455 12.1813 9.22656 12.4195 8.68698 12.5848C8.1474 12.75 7.58594 12.8327 7.0026 12.8327C6.19566 12.8327 5.43733 12.6796 4.7276 12.3733C4.01788 12.0671 3.40052 11.6514 2.87552 11.1264C2.35052 10.6014 1.9349 9.98407 1.62865 9.27435C1.3224 8.56463 1.16927 7.80629 1.16927 6.99935C1.16927 6.41602 1.25191 5.85456 1.41719 5.31497C1.58247 4.77539 1.82066 4.2674 2.13177 3.79102L0.804688 2.46393L1.63594 1.63268L12.3693 12.366L11.538 13.1973ZM7.0026 11.666C7.42066 11.666 7.82656 11.6125 8.22031 11.5056C8.61406 11.3987 8.99566 11.2382 9.3651 11.0243L2.9776 4.63685C2.76372 5.00629 2.6033 5.38789 2.49635 5.78164C2.38941 6.17539 2.33594 6.58129 2.33594 6.99935C2.33594 8.2924 2.79045 9.39345 3.69948 10.3025C4.60851 11.2115 5.70955 11.666 7.0026 11.666ZM11.8734 10.2077L11.0276 9.36185C11.2415 8.9924 11.4019 8.61081 11.5089 8.21706C11.6158 7.82331 11.6693 7.4174 11.6693 6.99935C11.6693 5.70629 11.2148 4.60525 10.3057 3.69622C9.3967 2.7872 8.29566 2.33268 7.0026 2.33268C6.58455 2.33268 6.17865 2.38615 5.7849 2.4931C5.39115 2.60004 5.00955 2.76046 4.6401 2.97435L3.79427 2.12852C4.27066 1.8174 4.77865 1.57921 5.31823 1.41393C5.85781 1.24865 6.41927 1.16602 7.0026 1.16602C7.80955 1.16602 8.56788 1.31914 9.2776 1.62539C9.98733 1.93164 10.6047 2.34727 11.1297 2.87227C11.6547 3.39727 12.0703 4.01463 12.3766 4.72435C12.6828 5.43407 12.8359 6.1924 12.8359 6.99935C12.8359 7.58268 12.7533 8.14414 12.588 8.68372C12.4227 9.22331 12.1845 9.73129 11.8734 10.2077Z" fill="#D90537"/>
                                    </g>
                                </svg>
                            </el-tooltip>
                            <span :class="{'ml-[18px]': scope.row.is_active }">{{ scope.row.name }}</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="created_at" label="Fecha de creación" width="150">
                    <template #default="scope">
                        <p>{{ formatDate(scope.row.created_at) }}</p>
                    </template>
                </el-table-column>
                <el-table-column prop="type" label="Tipo de cuenta" width="150" />
                <el-table-column prop="bank_name" label="Institución financiera" width="200" />
                <el-table-column label="Notas" width="200">
                    <template #default="scope">
                        <div class="flex items-center space-x-2">
                            <p>{{ scope.row.notes ?? '-' }}</p>
                        </div>
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
                                    <el-dropdown-item :command="'edit-' + scope.row.id">
                                        Editar
                                    </el-dropdown-item>
                                    <el-dropdown-item :command="'toogleStatus-' + scope.row.id">
                                        {{ scope.row.is_active ? 'Marcar como inactiva' : 'Marcar como activa' }}
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                    </template>
                </el-table-column>
            </el-table>
        <!-- tabla ends -->

        <!-- -------------- Modal Create starts----------------------- -->
        <DialogModal :show="showModal" @close="showModal = false">
            <template #title>
                <p v-if="editFlag">Editar cuenta" {{ itemClicked.name }}"</p>
                <p v-else>Registrar nueva cuenta</p>
            </template>
            <template #content>
                <form @submit.prevent="editFlag ? update() : store()" class="space-y-3 py-4">
                    <div>
                        <InputLabel value="Nombre de la cuenta*" class="ml-3 mb-1" />
                        <el-input v-model="form.name" placeholder="Ej. Cuenta de ahorros Banamex" clearable maxlength="100" show-word-limit />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel value="Nombre del propietario" class="ml-3 mb-1" />
                        <el-input v-model="form.owner_name" placeholder="Ingresa el nombre del propietario de la cuenta" clearable maxlength="100" show-word-limit />
                        <InputError :message="form.errors.owner_name" />
                    </div>

                    <div class="w-full">
                        <InputLabel value="Tipo de cuenta" class="ml-3 mb-1" />
                        <el-select v-model="form.type" :teleported="false" clearable placeholder="Selecciona el tipo de cuenta">
                            <el-option v-for="item in accountTypes" :key="item" :label="item" :value="item" />
                        </el-select>
                        <InputError :message="form.errors.type" />
                    </div>

                    <div>
                        <InputLabel value="Institución financiera*" class="ml-3 mb-1" />
                        <el-input v-model="form.bank_name" placeholder="Ej. Banamex" clearable maxlength="100" show-word-limit />
                        <InputError :message="form.errors.bank_name" />
                    </div>

                    <div>
                        <InputLabel value="Notas o comentarios" class="ml-3 mb-1" />
                        <el-input
                            v-model="form.notes"
                            maxlength="255"
                            placeholder="Ej. Usada para compras en línea y transferencias rápidas."
                            show-word-limit
                            type="textarea"
                        />
                        <InputError :message="form.errors.notes" />
                    </div>
                </form>
            </template>
            <template #footer>
                <div class="w-full flex justify-between">
                    <el-switch v-model="form.is_active" inline-prompt
                        style="--el-switch-on-color: #296A6B; --el-switch-off-color: #CCCCCC" active-text="Activo"
                        inactive-text="Inactivo" />

                    <div class="flex items-center space-x-3">
                        <CancelButton @click="showModal = false; form.reset(); editFlag = false;" :disabled="form.processing">
                            Cancelar
                        </CancelButton>

                        <PrimaryButton @click="editFlag ? update() : store()" :disabled="form.processing">
                            <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                            {{ editFlag ? 'Actualizar' : 'Crear' }}
                        </PrimaryButton>
                    </div>
                </div>
            </template>
        </DialogModal>
        <!-- --------------------------- Modal Create ends ------------------------------------ -->
    </main>
</template>

<script>
import PaginationWithNoMeta from "@/Components/MyComponents/PaginationWithNoMeta.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import { useForm } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
data() {
    const form = useForm({
        name: null,
        owner_name: null,
        bank_name: null,
        type: null,
        notes: null,
        is_active: true,
    });

    return {
        //tabla
        disableMassiveActions: true,
        showDetailsModal: false,
        itemToShow: null,

        //form
        form,
        showModal: false,
        editFlag: false,
        itemClicked: null,
        accountTypes: ['Cuenta de ahorro', 'Cuenta corriente', 'Nómina', 'Inversión', 'Crédito', 'Débito'],
    }
},
components:{
    PaginationWithNoMeta,
    PrimaryButton,
    CancelButton,
    DialogModal,
    InputLabel,
    InputError,
},
props:{
    bankCards: Object
},
methods:{
    update() {
        this.form.put(route('bank-cards.update', this.itemClicked), {
            onSuccess: () => {
                this.$notify({
                    title: 'Correcto',
                    message: '',
                    type: 'success'
                });
                this.form.reset();
                this.showModal = false;
            }
        });
    },
    store() {
        this.form.post(route('bank-cards.store'), {
            onSuccess: () => {
                this.$notify({
                    title: 'Correcto',
                    message: '',
                    type: 'success'
                });
                this.form.reset();
                this.showModal = false;
            }
        });
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

        if ( commandName === 'edit' ) {
            //busca la informacion del registro seleccionado utilizando el id
            const row = this.bankCards.data.find(item => item.id == rowId);
            this.handleRowClick(row);
        } else {
            this.$inertia.get(route('bank-cards.toogle-status', rowId));
        }
    },
    async deleteSelections() {
        this.$confirm('¿Estás seguro que deseas continuar con la eliminación?', 'Confirmar', {
            confirmButtonText: 'Sí',
            cancelButtonText: 'No',
            type: 'warning'
        }).then(async () => {
            try {
                const response = await axios.post(route('bank-cards.massive-delete', {
                    bankCards: this.$refs.multipleTableRef.value
                }));

                if (response.status === 200) {
                    this.$message({
                        type: 'success',
                        message: 'Eliminación correcta'
                    });

                    // update list of bankCards
                    let deletedIndexes = [];
                    this.bankCards.data.forEach((income, index) => {
                        if (this.$refs.multipleTableRef.value.includes(income)) {
                            deletedIndexes.push(index);
                        }
                    });

                    deletedIndexes.sort((a, b) => b - a);

                    for (const index of deletedIndexes) {
                        this.bankCards.data.splice(index, 1);
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
        this.itemClicked = row;
        this.editFlag = true;
        this.showModal = true;

        //preparar el formulario con los datos del registro seleccionado
        this.form.name = row.name;
        this.form.owner_name = row.owner_name;
        this.form.bank_name = row.bank_name;
        this.form.type = row.type;
        this.form.notes = row.notes;
        this.form.is_active = Boolean(row.is_active);
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