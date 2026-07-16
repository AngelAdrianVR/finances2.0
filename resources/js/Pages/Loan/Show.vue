<script setup>
import { ref, computed } from 'vue';
import { useForm, router, Link } from '@inertiajs/vue3';
import { Edit, Share, Plus } from '@element-plus/icons-vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { ElMessage, ElMessageBox } from 'element-plus';
import AppLayout from '@/Layouts/AppLayout.vue';
import Back from '@/Components/MyComponents/Back.vue';

const props = defineProps({ loan: Object, loans: Array });

const form = useForm({
    amount: null, date: format(new Date(), 'yyyy-MM-dd'), payment_method: 'Transferencia',
    notes: null, loan_id: props.loan.id,
});

const editingPayment = ref(false);
const loanSelected = ref(props.loan.id);
const selectedPayment = ref(null);
const showPaymentModal = ref(false);
const showShareModal = ref(false);
const paymentTypes = ['Efectivo', 'Transferencia', 'Deposito'];

const getRemainingAmount = computed(() => props.loan.amount - getTotalCapital.value);
const getTotalCapital = computed(() => props.loan.payments.reduce((a, i) => a + i.capital, 0));
const getTotalInterest = computed(() => props.loan.payments.reduce((a, i) => a + i.interest, 0));
const getTotalPaid = computed(() => getTotalCapital.value + getTotalInterest.value);

function encryptId() { return btoa(props.loan.id); }
function formatDate(date) { return date ? format(new Date(date), 'dd MMMM yyyy', { locale: es }) : ''; }
function formatShortDate(date) { return date ? format(new Date(date), 'dd MMM yy', { locale: es }) : ''; }
function formatMoney(val) { return val?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','); }

function shareByWhatsapp() {
    const url = 'https://finanzas.dtw.com.mx/loans/external-view/' + encryptId();
    window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent('Hola! Quiero compartirte este enlace: ' + url), '_blank');
}
function copyToClipboard() {
    const url = 'https://finanzas.dtw.com.mx/loans/external-view/' + encryptId();
    navigator.clipboard.writeText(url).then(() => ElMessage.success('Enlace copiado.'));
}

async function deleteItem() {
    try {
        await ElMessageBox.confirm('Estas seguro que deseas eliminar este prestamo?', 'Confirmar', { type: 'warning' });
        router.delete(route('loans.destroy', props.loan.id), { onSuccess: () => ElMessage.success('Prestamo eliminado.') });
    } catch {}
}

function openEditPayment(payment) {
    editingPayment.value = true; selectedPayment.value = payment;
    form.amount = payment.amount; form.date = payment.date;
    form.payment_method = payment.payment_method; form.notes = payment.notes;
    form.loan_id = payment.loan_id; showPaymentModal.value = true;
}

function storeOrUpdatePayment() {
    const url = editingPayment.value ? route('payments.update', selectedPayment.value.id) : route('payments.store');
    const method = editingPayment.value ? 'put' : 'post';
    form[method](url, {
        onSuccess: () => {
            ElMessage.success(editingPayment.value ? 'Abono editado.' : 'Abono registrado.');
            showPaymentModal.value = false; editingPayment.value = false; selectedPayment.value = null;
            form.reset(); form.loan_id = props.loan.id; form.date = format(new Date(), 'yyyy-MM-dd');
        },
    });
}

function openNewPayment() {
    editingPayment.value = false; selectedPayment.value = null; form.reset();
    form.loan_id = props.loan.id; form.date = format(new Date(), 'yyyy-MM-dd');
    showPaymentModal.value = true;
}
</script>

<template>
    <AppLayout title="Prestamo">
        <main class="py-6 px-4 md:px-8 max-w-7xl mx-auto">
            <Back :to="route('loans.index', { currentTab: loan.type == 'Otorgado' ? 1 : 2 })" />
            <h1 class="page-header">Detalles del prestamo</h1>

            <div class="flex flex-col lg:flex-row justify-between gap-3 mb-6">
                <el-select v-model="loanSelected" filterable placeholder="Buscar" class="w-full lg:w-1/3" @change="router.get(route('loans.show', loanSelected))">
                    <el-option v-for="item in loans" :key="item.id" :value="item.id"
                        :label="(item.type === 'Otorgado' ? 'O-' : 'R-') + item.id?.toString().padStart(3, '0') + ' - ' + (item.type === 'Otorgado' ? item.beneficiary_name : item.lender_name)">
                        <span>{{ (item.type === 'Otorgado' ? 'O-' : 'R-') + item.id?.toString().padStart(3, '0') + ' - ' + (item.type === 'Otorgado' ? item.beneficiary_name : item.lender_name) }}</span>
                        <span class="text-gray-400 text-xs ml-4">${{ formatMoney(item.amount) }}</span>
                    </el-option>
                </el-select>

                <div class="flex items-center gap-2">
                    <el-button @click="router.get(route('loans.edit', loan.id))" :icon="Edit">Editar prestamo</el-button>
                    <el-button @click="showShareModal = true" :icon="Share" circle />
                    <el-button @click="deleteItem" type="danger" circle>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                    </el-button>
                </div>
            </div>

            <section class="lg:flex lg:space-x-6 space-y-6 lg:space-y-0">
                <!-- Loan info -->
                <article class="lg:w-1/3 bg-white dark:bg-[#1E2424] border border-gray-200 dark:border-gray-700 rounded-xl p-5 grid grid-cols-2 gap-2 text-sm shadow-sm">
                    <h2 class="font-semibold col-span-full mb-3 text-gray-800 dark:text-gray-200">Informacion del prestamo</h2>
                    <p class="text-gray-500 dark:text-gray-400">Folio:</p><p class="dark:text-gray-300">{{ loan.type === 'Otorgado' ? 'O-' : 'R-' }}{{ loan.id?.toString().padStart(3, '0') }}</p>
                    <p class="text-gray-500 dark:text-gray-400">Tipo:</p><p class="dark:text-gray-300">{{ loan.type }}</p>
                    <p class="text-gray-500 dark:text-gray-400">{{ loan.type === 'Otorgado' ? 'Beneficiario:' : 'Prestamista:' }}</p>
                    <p>{{ loan.type === 'Otorgado' ? loan.beneficiary_name : loan.lender_name }}</p>
                    <p class="text-gray-500 dark:text-gray-400">Monto:</p><p class="font-mono font-semibold dark:text-gray-200">${{ formatMoney(loan.amount) }}</p>
                    <p class="text-gray-500 dark:text-gray-400">Capital abonado:</p><p class="font-mono dark:text-gray-300">${{ formatMoney(getTotalCapital) }}</p>
                    <p class="text-gray-500 dark:text-gray-400">Interes pagado:</p><p class="font-mono dark:text-gray-300">${{ formatMoney(getTotalInterest) }}</p>
                    <p class="text-gray-500 dark:text-gray-400">Total abonado:</p><p class="font-mono dark:text-gray-300">${{ formatMoney(getTotalPaid) }}</p>
                    <p class="text-gray-500 dark:text-gray-400">Saldo pendiente:</p><p class="font-mono font-bold" :class="getRemainingAmount === 0 ? 'text-[#2F9E5B]' : 'text-[#D99A2B]'">${{ formatMoney(getRemainingAmount) }}</p>
                    <p class="text-gray-500 dark:text-gray-400">Fecha:</p><p class="dark:text-gray-300">{{ formatDate(loan.loan_date) }}</p>
                    <p class="text-gray-500 dark:text-gray-400">Interes:</p><p class="dark:text-gray-300" v-if="loan.profitability">{{ loan.profitability }} {{ loan.profitability_mode === 'Porcentaje' ? '%' : '$' }}</p><p v-else class="dark:text-gray-300">0</p>
                    <p class="text-gray-500 dark:text-gray-400">Periodo interes:</p><p class="dark:text-gray-300">{{ loan.profitability_period }}</p>
                    <p class="text-gray-500 dark:text-gray-400">Vencimiento:</p><p class="dark:text-gray-300">{{ formatDate(loan.expired_date) ?? '-' }}</p>
                    <p class="text-gray-500 dark:text-gray-400">Estado:</p>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium inline-flex items-center gap-1" :class="loan.status === 'Pagado' ? 'bg-[#EAF7EF] text-[#247A47]' : 'bg-[#FBF3E2] text-[#B47F1F]'">{{ loan.status }}</span>
                </article>

                <!-- Payments -->
                <article class="lg:w-2/3">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Abonos</h2>
                        <el-button type="warning" :icon="Plus" @click="openNewPayment">Registrar abono</el-button>
                    </div>
                    <el-table :data="loan.payments" max-height="500" style="width:100%">
                        <el-table-column label="Abono" width="100" align="right"><template #default="{row}"><span class="font-mono text-gray-800 dark:text-gray-200">${{ formatMoney(row.amount) }}</span></template></el-table-column>
                        <el-table-column label="Capital" width="100" align="right"><template #default="{row}"><span class="font-mono text-gray-800 dark:text-gray-200">${{ formatMoney(row.capital) }}</span></template></el-table-column>
                        <el-table-column label="Interes" width="100" align="right"><template #default="{row}"><span class="font-mono text-gray-800 dark:text-gray-200">${{ formatMoney(row.interest) }}</span></template></el-table-column>
                        <el-table-column label="Restante" width="110" align="right"><template #default="{row}"><span class="font-mono font-semibold" :class="row.remaining === 0 ? 'text-[#2F9E5B]' : 'text-[#D99A2B]'">${{ formatMoney(row.remaining) }}</span></template></el-table-column>
                        <el-table-column label="Fecha" width="120"><template #default="{row}"><span class="text-sm text-gray-600 dark:text-gray-400">{{ formatShortDate(row.date) }}</span></template></el-table-column>
                        <el-table-column label="Comentarios" min-width="140" show-overflow-tooltip><template #default="{row}">{{ row.notes ?? '-' }}</template></el-table-column>
                        <el-table-column align="right" width="60" fixed="right">
                            <template #default="{row}">
                                <button class="row-actions-btn" @click="openEditPayment(row)">
                                    <el-icon :size="16"><Edit /></el-icon>
                                </button>
                            </template>
                        </el-table-column>
                    </el-table>
                </article>
            </section>
        </main>

        <!-- Payment modal -->
        <el-dialog v-model="showPaymentModal" :title="editingPayment ? 'Editar abono' : 'Registrar abono'" width="480px" destroy-on-close>
            <el-form :model="form" label-position="top">
                <el-form-item label="Monto del abono" required>
                    <el-input v-model="form.amount" :formatter="(v) => `${v}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')" :parser="(v) => v.replace(/\$\s?|(,*)/g, '')"><template #prepend>$</template></el-input>
                </el-form-item>
                <el-form-item label="Fecha del abono" required>
                    <el-date-picker v-model="form.date" type="date" class="!w-full" />
                </el-form-item>
                <el-form-item label="Metodo de pago" required>
                    <el-select v-model="form.payment_method" class="!w-full"><el-option v-for="pm in paymentTypes" :key="pm" :label="pm" :value="pm" /></el-select>
                </el-form-item>
                <el-form-item label="Comentarios">
                    <el-input v-model="form.notes" maxlength="500" type="textarea" :rows="3" show-word-limit placeholder="Ej. Pago mes septiembre" />
                </el-form-item>
            </el-form>
            <template #footer>
                <el-button @click="showPaymentModal = false">Cancelar</el-button>
                <el-button type="warning" :loading="form.processing" @click="storeOrUpdatePayment">{{ editingPayment ? 'Guardar cambios' : 'Registrar' }}</el-button>
            </template>
        </el-dialog>

        <!-- Share modal -->
        <el-dialog v-model="showShareModal" title="Compartir enlace" width="480px">
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">El enlace muestra el estado completo del prestamo pero no se puede editar, eliminar o registrar abonos. Es meramente informativo.
                <a :href="route('loans.external-view', encryptId())" target="_blank" class="text-[#296A6B] underline ml-1">Vista previa</a>
            </p>
            <div class="flex justify-around mt-6">
                <button @click="shareByWhatsapp" class="flex flex-col items-center gap-1 text-sm hover:opacity-80 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#25D366" class="size-10"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" /></svg>
                    <span>WhatsApp</span>
                </button>
                <button @click="copyToClipboard" class="flex flex-col items-center gap-1 text-sm hover:opacity-80 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10 text-gray-600"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" /></svg>
                    <span>Copiar link</span>
                </button>
            </div>
            <template #footer><el-button @click="showShareModal = false">Cerrar</el-button></template>
        </el-dialog>
    </AppLayout>
</template>
