<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { Edit, Delete, Calendar as CalendarIcon, Refresh, Checked, Plus, ArrowLeft, ArrowRight } from '@element-plus/icons-vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { ElMessage } from 'element-plus';
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

const weekdays = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

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

const flatDays = computed(() => weeks.value.flat());

const currentMonthLabel = computed(() =>
    currentMonth.value.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' }).toUpperCase()
);

const incomeReminders = computed(() => (reminders.value || []).filter((item) => item.type === 'Ingreso recurrente'));
const outcomeReminders = computed(() => (reminders.value || []).filter((item) => item.type === 'Gasto fijo'));

const incomeTotal = computed(() => incomeReminders.value.reduce((sum, item) => sum + Number(item.amount || 0), 0));
const outcomeTotal = computed(() => outcomeReminders.value.reduce((sum, item) => sum + Number(item.amount || 0), 0));

const monthSummary = computed(() => {
    const total = reminders.value?.length || 0;

    if (!total) return 'Sin recordatorios programados este mes';

    return `${total} ${total === 1 ? 'recordatorio programado' : 'recordatorios programados'}`;
});

function toCalendarDate(value) {
    if (!value) return null;

    const parsed = new Date(value);
    if (Number.isNaN(parsed.getTime())) return null;

    // The API sends UTC midnights; keep the calendar day exactly as stored.
    return new Date(parsed.getUTCFullYear(), parsed.getUTCMonth(), parsed.getUTCDate());
}

function formatDate(date) {
    const parsed = toCalendarDate(date);

    return parsed ? format(parsed, 'EEE, dd MMM yyyy', { locale: es }) : '';
}
function formatMoney(val) { return Number(val || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','); }

function changeMonth(offset) { currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() + offset, 1); fetchMonthReminders(); }
function goToToday() { currentMonth.value = new Date(); buttonSelected.value = 'Hoy'; fetchMonthReminders(); }

function isToday(day) {
    if (!day) return false;
    return day === todayDate.getDate() && currentMonth.value.getMonth() === todayDate.getMonth() && currentMonth.value.getFullYear() === todayDate.getFullYear();
}

function isRemindDay(remind, day) {
    if (!day) return false;
    const rd = toCalendarDate(remind.date);
    const cd = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth(), day);
    if (!rd) return false;
    return rd.getFullYear() === cd.getFullYear() && rd.getMonth() === cd.getMonth() && rd.getDate() === cd.getDate();
}

function getRemindersForDay(day) { return (reminders.value || []).filter(r => isRemindDay(r, day)); }

function handleShowReminderDetails(reminder) { reminderSelected.value = reminder; drawer.value = true; }

function isIncomeReminder(reminder) { return reminder?.type === 'Ingreso recurrente'; }

function updateDrawerSize() {
    const w = window.innerWidth;
    if (w < 768) drawerSize.value = '85%';
    else if (w <= 1024) drawerSize.value = '60%';
    else drawerSize.value = '30%';
}

// Quita localmente las ocurrencias afectadas por la eliminacion, para reflejarlo al instante.
function applyDeleteLocally(list, target, mode) {
    return list.filter((r) => {
        if (r.type !== target.type || r.title !== target.title) return true;
        if (mode === 'Este') return r.id !== target.id;
        if (mode === 'Este y los siguientes') return new Date(r.date).getTime() < new Date(target.date).getTime();
        return false; // 'Todos'
    });
}

async function handleDelete() {
    if (!reminderSelected.value || !deleteOption.value) return;

    const target = reminderSelected.value;
    const mode = deleteOption.value;
    const backup = reminders.value ? [...reminders.value] : [];

    // Actualizacion optimista: desaparece de inmediato, sin recargar la pagina.
    reminders.value = applyDeleteLocally(reminders.value || [], target, mode);

    try {
        loadingDeleted.value = true;
        await axios.delete(route('calendars.destroy', target.id), { params: { deleteOption: mode } });
        ElMessage.success('Eliminación exitosa.');
    } catch (error) {
        if (error?.response?.status === 404) {
            // La ocurrencia ya no existia (lista desactualizada): se sincroniza sin mostrar error.
            ElMessage.info('El recordatorio ya no existía.');
        } else {
            reminders.value = backup; // restaura si fue un fallo real
            console.error(error);
            ElMessage.error('No se pudo eliminar el recordatorio.');
        }
    } finally {
        loadingDeleted.value = false;
        drawer.value = false;
        showDeleteConfirmation.value = false;
        deleteOption.value = null;
        // Resincroniza con el servidor sin mostrar el skeleton.
        await fetchMonthReminders({ silent: true });
    }
}

async function fetchMonthReminders({ silent = false } = {}) {
    try {
        if (!silent) loading.value = true;
        const response = await axios.post(route('calendars.fetch-month-reminders'), { month: currentMonth.value.getMonth() + 1, year: currentMonth.value.getFullYear() });
        if (response.status === 200) reminders.value = response.data.reminders;
    } catch (error) { console.error(error); }
    finally { if (!silent) loading.value = false; }
}

onMounted(() => { fetchMonthReminders(); updateDrawerSize(); window.addEventListener('resize', updateDrawerSize); });
onUnmounted(() => window.removeEventListener('resize', updateDrawerSize));
</script>

<template>
    <AppLayout title="Calendario">
        <div class="page-shell">
            <header class="page-heading">
                <div>
                    <p class="page-heading__eyebrow">Planificación</p>
                    <h1 class="page-heading__title">Calendario</h1>
                    <p class="page-heading__subtitle">
                        Visualiza tus ingresos y gastos recurrentes y anticípate a cada pago.
                    </p>
                </div>

                <div class="page-heading__actions">
                    <el-dropdown
                        split-button
                        type="primary"
                        @click="router.get(route('recurring-incomes.create'))"
                    >
                        <el-icon :size="14" class="mr-1"><Plus /></el-icon>
                        Agregar ingreso recurrente
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item @click="router.get(route('recurring-outcomes.create'))">
                                    Agregar gasto recurrente
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </div>
            </header>

            <section class="calendar-card">
                <!-- Toolbar -->
                <div class="calendar-toolbar">
                    <div>
                        <p class="calendar-toolbar__label">{{ currentMonthLabel }}</p>
                        <p class="calendar-toolbar__meta">{{ monthSummary }}</p>
                    </div>

                    <div class="calendar-toolbar__nav">
                        <button type="button" class="calendar-nav-btn" title="Mes anterior" @click="changeMonth(-1); buttonSelected = 'Mes anterior'">
                            <el-icon :size="16"><ArrowLeft /></el-icon>
                        </button>
                        <button
                            type="button"
                            class="calendar-today-btn"
                            :class="{ 'is-active': buttonSelected === 'Hoy' }"
                            @click="goToToday(); buttonSelected = 'Hoy'"
                        >
                            Hoy
                        </button>
                        <button type="button" class="calendar-nav-btn" title="Mes siguiente" @click="changeMonth(1); buttonSelected = 'Mes siguiente'">
                            <el-icon :size="16"><ArrowRight /></el-icon>
                        </button>
                    </div>
                </div>

                <!-- Summary -->
                <div class="calendar-summary">
                    <div class="calendar-summary__card">
                        <span class="calendar-summary__icon calendar-summary__icon--neutral">
                            <el-icon :size="16"><CalendarIcon /></el-icon>
                        </span>
                        <div>
                            <p class="calendar-summary__label">Recordatorios</p>
                            <p class="calendar-summary__value">{{ reminders?.length || 0 }}</p>
                        </div>
                    </div>

                    <div class="calendar-summary__card">
                        <span class="calendar-summary__icon calendar-summary__icon--income">
                            <el-icon :size="16"><Refresh /></el-icon>
                        </span>
                        <div>
                            <p class="calendar-summary__label">Ingresos recurrentes</p>
                            <p class="calendar-summary__value">
                                {{ incomeReminders.length }}
                                <span class="calendar-summary__amount calendar-summary__amount--income">${{ formatMoney(incomeTotal) }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="calendar-summary__card">
                        <span class="calendar-summary__icon calendar-summary__icon--outcome">
                            <el-icon :size="16"><Refresh /></el-icon>
                        </span>
                        <div>
                            <p class="calendar-summary__label">Gastos fijos</p>
                            <p class="calendar-summary__value">
                                {{ outcomeReminders.length }}
                                <span class="calendar-summary__amount calendar-summary__amount--outcome">${{ formatMoney(outcomeTotal) }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Loading -->
                <div v-if="loading" class="py-12"><el-skeleton animated :rows="6" /></div>

                <!-- Calendar grid -->
                <div v-else class="calendar-grid">
                    <div class="calendar-grid__weekdays">
                        <span v-for="label in weekdays" :key="label">{{ label }}</span>
                    </div>

                    <div class="calendar-grid__days">
                        <div
                            v-for="(day, index) in flatDays"
                            :key="index"
                            class="calendar-day"
                            :class="{ 'is-empty': !day, 'is-today': isToday(day) }"
                        >
                            <span v-if="day" class="calendar-day__number">{{ day }}</span>

                            <div v-if="day && getRemindersForDay(day).length" class="calendar-day__events">
                                <button
                                    v-for="reminder in getRemindersForDay(day)"
                                    :key="reminder.id"
                                    type="button"
                                    class="calendar-event"
                                    :class="[
                                        isIncomeReminder(reminder) ? 'calendar-event--income' : 'calendar-event--outcome',
                                        { 'is-registered': reminder.status === 'Registrado' },
                                    ]"
                                    :title="reminder.title"
                                    @click.stop="handleShowReminderDetails(reminder)"
                                >
                                    <el-icon :size="12">
                                        <Checked v-if="reminder.status === 'Registrado'" />
                                        <Refresh v-else />
                                    </el-icon>
                                    <span class="calendar-event__title">{{ reminder.title }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Legend -->
                <footer class="calendar-legend">
                    <span class="calendar-legend__item">
                        <span class="calendar-legend__dot calendar-legend__dot--income" />
                        Ingreso recurrente
                    </span>
                    <span class="calendar-legend__item">
                        <span class="calendar-legend__dot calendar-legend__dot--outcome" />
                        Gasto fijo
                    </span>
                    <span class="calendar-legend__item">
                        <span class="calendar-legend__dot calendar-legend__dot--registered" />
                        Registrado
                    </span>
                </footer>
            </section>
        </div>

        <!-- Drawer -->
        <el-drawer v-model="drawer" :size="drawerSize" direction="rtl" class="calendar-drawer">
            <template #header>
                <div class="calendar-drawer__header">
                    <span
                        class="calendar-type-badge"
                        :class="isIncomeReminder(reminderSelected) ? 'calendar-type-badge--income' : 'calendar-type-badge--outcome'"
                    >
                        <el-icon :size="14"><Refresh /></el-icon>
                        {{ reminderSelected?.type }}
                    </span>
                </div>
            </template>

            <div v-if="reminderSelected" class="calendar-drawer__body">
                <h2 class="calendar-drawer__title">{{ reminderSelected.title }}</h2>

                <p
                    class="calendar-drawer__amount"
                    :class="isIncomeReminder(reminderSelected) ? 'is-income' : 'is-outcome'"
                >
                    ${{ formatMoney(reminderSelected.amount) }}
                </p>

                <div class="calendar-drawer__meta">
                    <span
                        class="calendar-status"
                        :class="reminderSelected.status === 'Registrado' ? 'calendar-status--done' : 'calendar-status--pending'"
                    >
                        {{ reminderSelected.status }}
                    </span>
                    <span class="calendar-drawer__date">{{ formatDate(reminderSelected.date) }}</span>
                </div>

                <dl class="calendar-detail">
                    <div class="calendar-detail__row">
                        <dt>Categoría</dt>
                        <dd>{{ reminderSelected.category ?? '—' }}</dd>
                    </div>
                    <div class="calendar-detail__row">
                        <dt>Método de pago</dt>
                        <dd>{{ reminderSelected.payment_method ?? '—' }}</dd>
                    </div>
                    <div class="calendar-detail__row">
                        <dt>Frecuencia</dt>
                        <dd>{{ reminderSelected.periodicity ?? '—' }}</dd>
                    </div>
                    <div class="calendar-detail__row">
                        <dt>Descripción</dt>
                        <dd>{{ reminderSelected.description ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <template #footer>
                <div class="calendar-drawer__footer">
                    <el-button @click="router.get(route('calendars.edit', reminderSelected?.id))" :icon="Edit">
                        Editar
                    </el-button>
                    <el-button type="danger" plain :icon="Delete" @click="showDeleteConfirmation = true">
                        Eliminar
                    </el-button>
                </div>
            </template>
        </el-drawer>

        <!-- Delete confirmation dialog -->
        <el-dialog v-model="showDeleteConfirmation" title="Eliminar recordatorio" width="460px" append-to-body>
            <p class="calendar-dialog__text">
                ¿Deseas eliminar este {{ reminderSelected?.type === 'Ingreso recurrente' ? 'ingreso' : 'gasto' }}?
            </p>
            <el-radio-group v-model="deleteOption" class="calendar-dialog__options">
                <el-radio value="Este">
                    Este {{ reminderSelected?.type === 'Ingreso recurrente' ? 'ingreso' : 'gasto' }}
                </el-radio>
                <el-radio value="Este y los siguientes">Este y los siguientes</el-radio>
                <el-radio value="Todos">
                    Todos los {{ reminderSelected?.type === 'Ingreso recurrente' ? 'ingresos' : 'gastos' }}
                </el-radio>
            </el-radio-group>
            <template #footer>
                <el-button @click="showDeleteConfirmation = false; deleteOption = null">Cancelar</el-button>
                <el-button type="danger" :disabled="!deleteOption" :loading="loadingDeleted" @click="handleDelete">
                    Eliminar
                </el-button>
            </template>
        </el-dialog>
    </AppLayout>
</template>
