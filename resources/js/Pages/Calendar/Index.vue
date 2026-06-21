<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { Edit, Delete, Calendar as CalendarIcon, Refresh, Checked, Plus } from '@element-plus/icons-vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { ElMessage, ElMessageBox } from 'element-plus';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

const currentMonth = ref(new Date());
const buttonSelected = ref('Hoy');
const todayDate = new Date();
const reminders = ref(null);
const loading = ref(false);
const drawer = ref(false);
const drawerSize = ref('30%');
const reminderSelected = ref(null);
const showDeleteConfirmation = ref(false);
const deleteOption = ref(null);
const loadingDeleted = ref(false);

// Calendar grid
const weeks = computed(() => {
    const year = currentMonth.value.getFullYear();
    const month = currentMonth.value.getMonth();
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const grid = [];
    let week = new Array(7).fill(null);
    let day = 1;
    for (let i = 0; i < firstDay; i++) week[i] = null;
    for (let i = firstDay; i < 7 && day <= daysInMonth; i++) week[i] = day++;
    grid.push([...week]);
    while (day <= daysInMonth) {
        week = new Array(7).fill(null);
        for (let i = 0; i < 7 && day <= daysInMonth; i++) week[i] = day++;
        grid.push([...week]);
    }
    return grid;
});

const currentMonthLabel = computed(() =>
    currentMonth.value.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' }).toUpperCase()
);

function formatDate(date) { return date ? format(new Date(date), 'EEE, dd MMM yyyy', { locale: es }) : ''; }
function formatMoney(val) { return val?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','); }

function changeMonth(offset) { currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() + offset, 1); fetchMonthReminders(); }
function goToToday() { currentMonth.value = new Date(); buttonSelected.value = 'Hoy'; fetchMonthReminders(); }

function isToday(day) {
    if (!day) return false;
    return day === todayDate.getDate() && currentMonth.value.getMonth() === todayDate.getMonth() && currentMonth.value.getFullYear() === todayDate.getFullYear();
}

function isRemindDay(remind, day) {
    if (!day) return false;
    const rd = new Date(remind.date);
    const cd = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth(), day);
    return rd.getFullYear() === cd.getFullYear() && rd.getMonth() === cd.getMonth() && rd.getDate() === cd.getDate();
}

function getRemindersForDay(day) { return (reminders.value || []).filter(r => isRemindDay(r, day)); }

function handleShowReminderDetails(reminder) { reminderSelected.value = reminder; drawer.value = true; }

function updateDrawerSize() {
    const w = window.innerWidth;
    if (w < 768) drawerSize.value = '85%';
    else if (w <= 1024) drawerSize.value = '60%';
    else drawerSize.value = '30%';
}

async function handleDelete() {
    try {
        loadingDeleted.value = true;
        await axios.delete(route('calendars.destroy', reminderSelected.value.id), { params: { deleteOption: deleteOption.value } });
        ElMessage.success('Eliminacion exitosa');
        fetchMonthReminders();
    } catch (error) { console.error(error); }
    finally { loadingDeleted.value = false; drawer.value = false; showDeleteConfirmation.value = false; }
}

async function fetchMonthReminders() {
    try {
        loading.value = true;
        const response = await axios.post(route('calendars.fetch-month-reminders'), { month: currentMonth.value.getMonth() + 1, year: currentMonth.value.getFullYear() });
        if (response.status === 200) reminders.value = response.data.reminders;
    } catch (error) { console.error(error); }
    finally { loading.value = false; }
}

onMounted(() => { fetchMonthReminders(); updateDrawerSize(); window.addEventListener('resize', updateDrawerSize); });
onUnmounted(() => window.removeEventListener('resize', updateDrawerSize));
</script>

<template>
    <AppLayout title="Calendario">
        <main class="py-6 px-4 md:px-8 max-w-7xl mx-auto">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <h1 class="page-header !mb-0">Calendario</h1>
                <el-dropdown split-button type="primary" @click="router.get(route('calendars.create', { type: 'Ingreso recurrente' }))">
                    <el-icon :size="14" class="mr-1"><Plus /></el-icon>Agregar ingreso recurrente
                    <template #dropdown>
                        <el-dropdown-menu>
                            <el-dropdown-item @click="router.get(route('calendars.create', { type: 'Gasto fijo' }))">Agregar gasto recurrente</el-dropdown-item>
                        </el-dropdown-menu>
                    </template>
                </el-dropdown>
            </div>

            <div class="bg-white dark:bg-[#1E2424] border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm p-4 md:p-8">
                <!-- Month navigation -->
                <div class="flex flex-col md:flex-row justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-5 gap-4">
                    <p class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ currentMonthLabel }}</p>
                    <div class="flex items-center text-sm">
                        <button @click="changeMonth(-1); buttonSelected = 'Mes anterior'" class="px-3 py-1.5 border border-gray-200 dark:border-gray-600 rounded-l-lg transition-colors" :class="buttonSelected === 'Mes anterior' ? 'bg-gray-100 dark:bg-gray-700 text-[#296A6B] dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'">Mes anterior</button>
                        <button @click="goToToday(); buttonSelected = 'Hoy'" class="px-3 py-1.5 border-t border-b border-gray-200 dark:border-gray-600 transition-colors" :class="buttonSelected === 'Hoy' ? 'bg-gray-100 dark:bg-gray-700 text-[#296A6B] dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'">Hoy</button>
                        <button @click="changeMonth(1); buttonSelected = 'Mes siguiente'" class="px-3 py-1.5 border border-gray-200 dark:border-gray-600 rounded-r-lg transition-colors" :class="buttonSelected === 'Mes siguiente' ? 'bg-gray-100 dark:bg-gray-700 text-[#296A6B] dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'">Mes siguiente</button>
                    </div>
                </div>

                <!-- Loading -->
                <div v-if="loading" class="py-12"><el-skeleton animated :rows="6" /></div>

                <!-- Calendar grid -->
                <table v-else class="w-full mt-8 border-collapse">
                    <thead>
                        <tr class="text-center *:py-2 *:w-[14.28%] *:text-xs md:*:text-sm *:text-gray-500 dark:*:text-gray-400 *:font-medium">
                            <th>Dom</th><th>Lun</th><th>Mar</th><th>Mie</th><th>Jue</th><th>Vie</th><th>Sab</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(week, wi) in weeks" :key="wi" class="text-sm">
                            <td v-for="(day, di) in week" :key="di" class="h-28 md:h-32 align-top border border-gray-100 dark:border-gray-700 p-1 relative" :class="{ 'bg-gray-50/50 dark:bg-gray-800/30': !day }">
                                <span v-if="day" class="absolute top-1 left-1 rounded-md size-6 flex items-center justify-center text-center text-xs font-bold"
                                    :class="isToday(day) ? 'bg-[#296A6B] text-white' : 'text-gray-600 dark:text-gray-300'">{{ day }}</span>
                                <div v-if="day" class="mt-7 space-y-0.5">
                                    <div v-for="reminder in getRemindersForDay(day)" :key="reminder.id"
                                        @click.stop="handleShowReminderDetails(reminder)"
                                        class="text-[10px] md:text-xs px-1.5 py-0.5 rounded-full cursor-pointer truncate flex items-center gap-1 transition-colors hover:opacity-80"
                                        :class="{
                                            'line-through opacity-50': reminder.status === 'Registrado',
                                            'bg-[#E2FFD5] text-[#2E8A14]': reminder.type === 'Ingreso recurrente',
                                            'bg-[#CBEAF8] text-[#1023A1]': reminder.type === 'Gasto fijo',
                                        }">
                                        <el-icon :size="12"><Checked v-if="reminder.status === 'Registrado'" /><Refresh v-else /></el-icon>
                                        <span class="hidden md:inline truncate">{{ reminder.title }}</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>

        <!-- Drawer -->
        <el-drawer v-model="drawer" :size="drawerSize" direction="rtl">
            <template #header>
                <span class="inline-flex items-center gap-1.5 text-xs px-3 py-1 rounded-full font-medium"
                    :class="reminderSelected?.type === 'Ingreso recurrente' ? 'bg-[#EAF7EF] text-[#247A47]' : 'bg-[#EAF1FB] text-[#2E63AD]'">
                    <el-icon :size="14"><Refresh /></el-icon>
                    {{ reminderSelected?.type }}
                </span>
            </template>
            <div v-if="reminderSelected">
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-600 pb-3 mb-4">
                    <h2 class="text-base font-bold text-gray-800 dark:text-gray-200">{{ reminderSelected.title }}</h2>
                    <span class="text-xs px-3 py-1 rounded-full font-medium"
                        :class="reminderSelected.status === 'Registrado' ? 'bg-[#EAF7EF] text-[#247A47]' : 'bg-[#FBF3E2] text-[#B47F1F]'">{{ reminderSelected.status }}</span>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex"><span class="text-gray-500 dark:text-gray-400 w-32">Fecha:</span><span class="dark:text-gray-300">{{ formatDate(reminderSelected.date) }}</span></div>
                    <div class="flex"><span class="text-gray-500 dark:text-gray-400 w-32">Monto:</span><span class="font-mono font-semibold dark:text-gray-200">${{ formatMoney(reminderSelected.amount) }}</span></div>
                    <div class="flex"><span class="text-gray-500 dark:text-gray-400 w-32">Categoria:</span><span class="dark:text-gray-300">{{ reminderSelected.category ?? '-' }}</span></div>
                    <div class="flex"><span class="text-gray-500 dark:text-gray-400 w-32">Metodo de pago:</span><span class="dark:text-gray-300">{{ reminderSelected.payment_method ?? '-' }}</span></div>
                    <div class="flex"><span class="text-gray-500 dark:text-gray-400 w-32">Frecuencia:</span><span class="dark:text-gray-300">{{ reminderSelected.periodicity ?? '-' }}</span></div>
                    <div class="flex"><span class="text-gray-500 dark:text-gray-400 w-32">Descripcion:</span><span class="dark:text-gray-300">{{ reminderSelected.description ?? '-' }}</span></div>
                </div>
            </div>
            <template #footer>
                <div class="flex justify-end gap-2">
                    <el-button @click="router.get(route('calendars.edit', reminderSelected?.id))" :icon="Edit">Editar</el-button>
                    <el-button type="danger" :icon="Delete" @click="showDeleteConfirmation = true" />
                </div>
            </template>
        </el-drawer>

        <!-- Delete confirmation dialog -->
        <el-dialog v-model="showDeleteConfirmation" title="Eliminar recordatorio" width="460px">
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Deseas eliminar el {{ reminderSelected?.type }}?</p>
            <el-radio-group v-model="deleteOption" class="flex flex-col gap-2">
                <el-radio value="Este">Este {{ reminderSelected?.type === 'Ingreso recurrente' ? 'ingreso' : 'gasto' }}</el-radio>
                <el-radio value="Este y los siguientes">Este y los siguientes</el-radio>
                <el-radio value="Todos">Todos los {{ reminderSelected?.type === 'Ingreso recurrente' ? 'ingresos' : 'gastos' }}</el-radio>
            </el-radio-group>
            <template #footer>
                <el-button @click="showDeleteConfirmation = false; deleteOption = null">Cancelar</el-button>
                <el-button type="danger" :disabled="!deleteOption" :loading="loadingDeleted" @click="handleDelete">Eliminar</el-button>
            </template>
        </el-dialog>
    </AppLayout>
</template>
