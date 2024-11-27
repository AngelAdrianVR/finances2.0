<template>
    <AppLayout title="Calendario">
        <main class="px-2 md:px-5 lg:mx-12 pt-1">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h1 class="font-bold my-3 ml-4 text-lg">Calendario</h1>
                <el-dropdown split-button type="primary" @click="$inertia.get(route('calendars.create', { type: 'Ingreso recurrente'}))">
                    Agregar ingreso recurrente
                    <template #dropdown>
                        <el-dropdown-menu>
                            <el-dropdown-item @click="$inertia.get(route('calendars.create', { type: 'Gasto fijo' }))">Agregar gasto recurrente</el-dropdown-item>
                        </el-dropdown-menu>
                    </template>
                </el-dropdown>
            </div>
            
            <section class="border border-grayD9 rounded-lg my-9 py-9 px-5">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-center text-lg dark:text-white border-b border-grayD9 pb-5 space-y-3 md:space-y-0">
                    <div class="flex justify-between items-center">
                        <p class="text-xl font-bold ml-2 uppercase">{{ currentMonth.toLocaleDateString('es-ES', {
                            month: 'long', year: 'numeric'
                        })
                        }}</p>
                    </div>

                    <div class="flex items-center text-sm *:border *:border-grayD9 *:py-1 *:px-3">
                        <button 
                            @click="changeMonth(-1); buttonSelected = 'Mes anterior'" 
                            :class="buttonSelected === 'Mes anterior' ? 'bg-[#EDEDED] text-primary' : 'text-gray99'">
                            Mes anterior
                        </button>
                        <button 
                            @click="goToToday(); buttonSelected = 'Hoy'" 
                            :class="buttonSelected === 'Hoy' ? 'bg-[#EDEDED] text-primary' : 'text-gray99'">
                            Hoy
                        </button>
                        <button 
                            @click="changeMonth(1); buttonSelected = 'Mes siguiente'" 
                            :class="buttonSelected === 'Mes siguiente' ? 'bg-[#EDEDED] text-primary' : 'text-gray99'">
                            Mes siguiente
                        </button>
                    </div>
                </div>
                
                <!-- estado de carga -->
                <LoadingLogo v-if="loading" class="mt-4 lg:mt-20" />

                <!-- -------------- calendar section --------------- -->
                <section v-else class="md:w-11/12 mx-auto">
                    <table class="w-full mt-12">
                    <tr class="text-center *:py-2 *:w-[14.28%] *:text-gray99 text-xs md:text-base">
                        <th>Domingo</th>
                        <th>Lunes</th>
                        <th>Martes</th>
                        <th>Miércoles</th>
                        <th>Jueves</th>
                        <th>Viernes</th>
                        <th>Sábado</th>
                    </tr>
                    <tr v-for="(week, index) in weeks" :key="index" class="text-sm text-right">
                        <td v-for="day in week" :key="day" class="h-32 pb-4 border border-[#D9D9D9] relative">
                            <p 
                                :class="{ 
                                    'bg-black/60 text-white': isToday(day), 
                                    'text-black': !isToday(day) 
                                }" 
                                class="absolute top-1 left-1 rounded-md size-6 flex items-center justify-center text-center font-bold">
                                <span>{{ day }}</span>
                            </p>

                            <!-- Agregar recordatorio en día -->
                            <div v-for="reminder in reminders" :key="reminder.id">
                                <div class="mt-1" v-if="isRemaindDay(reminder, day)">
                                    <div @click.stop="handleShowReminderDetails(reminder)"
                                        class="h-5 text-xs justify-center items-center cursor-pointer space-x-2 flex relative rounded-full"
                                        :class="{
                                            'bg-[#E2FFD5]': reminder.type === 'Ingreso recurrente',
                                            'bg-[#CBEAF8]': reminder.type === 'Gasto fijo',
                                        }">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                        </svg>
                                        <p class="hidden md:block">{{ reminder.title }}</p>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </table>
                </section>
            </section>
        </main>

        <!-- Ventana lateral -->
        <el-drawer v-model="drawer" :size="drawerSize" >
            <template #header>
                <div v-if="reminderSelected?.type === 'Ingreso recurrente'">
                    <span class="inline-flex items-center space-x-2 text-[#47B61E] bg-[#E2FFD5] rounded-full py-1 px-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        <span class="tex-sm">Ingreso recurrente</span>
                    </span>
                </div>
                <div v-else>
                    <span class="inline-flex items-center space-x-2 text-[#1023A1] bg-[#CBEAF8] rounded-full py-1 px-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        <span class="tex-sm">Gasto fijo</span>
                    </span>
                </div>
            </template>
            <template #default>
                <div>
                    <h2 class="text-lg font-bold border-b border-grayD9 pb-3">{{ reminderSelected?.title }}</h2>

                    <section class="space-y-2 mt-4 text-sm md:text-base">
                        <div class="flex space-x-1">
                            <p class="text-[#575757] w-36">Fecha:</p>
                            <p>{{ formatDate(reminderSelected?.date) }}</p>
                        </div>
                        <div class="flex space-x-1">
                            <p class="text-[#575757] w-36">Monto:</p>
                            <p>${{ reminderSelected?.amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                        </div>
                        <div class="flex space-x-1">
                            <p class="text-[#575757] w-36">Categoría:</p>
                            <p>{{ reminderSelected?.category ?? '-' }}</p>
                        </div>
                        <div class="flex space-x-1">
                            <p class="text-[#575757] w-36">Método de pago:</p>
                            <p>{{ reminderSelected?.payment_method ?? '-' }}</p>
                        </div>
                        <div class="flex space-x-1">
                            <p class="text-[#575757] w-36">Frecuencia:</p>
                            <p>{{ reminderSelected?.periodicity ?? '-' }}</p>
                        </div>
                        <div class="flex space-x-1">
                            <p class="text-[#575757] w-36">Descripción:</p>
                            <p>{{ reminderSelected?.description ?? '-' }}</p>
                        </div>
                    </section>
                </div>
            </template>
            <template #footer>
                <div class="flex justify-end items-center space-x-2">
                    <ThirthButton @click="$inertia.get(route('calendars.edit', reminderSelected.id))">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                        Editar
                    </ThirthButton>
                    <ThirthButton @click="showDeleteConfirmation = true" class="!px-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </ThirthButton>
                </div>
            </template>
        </el-drawer>

        <!-- Confirmacion de eliminacion -->
        <DeleteConfirmationModal :show="showDeleteConfirmation" @close="showDeleteConfirmation = false; deleteOption = null">
            <template #title>
                <div class="text-center">
                    <p>¿Deseas elimninar el {{ reminderSelected?.type }}?</p>
                </div>
            </template>

            <template #content>
                <div class="ml-4">
                    <el-radio-group v-model="deleteOption">
                        <el-radio class="w-full" :value="'Este'">Este {{ reminderSelected?.type === 'Ingreso recurrente' ? 'ingreso' : 'gasto' }}</el-radio>
                        <el-radio class="w-full" :value="'Este y los siguientes'">Este y los siguientes {{ reminderSelected?.type === 'Ingreso recurrente' ? 'ingresos' : 'gastos' }}</el-radio>
                        <el-radio class="w-full" :value="'Todos'">Todos los {{ reminderSelected?.type === 'Ingreso recurrente' ? 'ingresos' : 'gastos' }}</el-radio>
                    </el-radio-group>
                </div>
            </template>

            <template #footer>
                <div class="flex items-center space-x-2">
                    <CancelButton @click="showDeleteConfirmation = false; deleteOption = null">Cancelar</CancelButton>
                    <DangerButton @click="handleDelete()" :disabled="!deleteOption || loadingDeleted">
                        <i v-if="loadingDeleted" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Eliminar
                    </DangerButton>
                </div>
            </template>

        </DeleteConfirmationModal>
    </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import CancelButton from "@/Components/CancelButton.vue";
import DeleteConfirmationModal from "@/Components/DeleteConfirmationModal.vue";
import ThirthButton from "@/Components/MyComponents/ThirthButton.vue";
import LoadingLogo from "@/Components/MyComponents/LoadingLogo.vue";
import axios from 'axios';
import moment from 'moment';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { Link } from "@inertiajs/vue3";

export default {
data() {
    return {
        currentMonth: new Date(),
        buttonSelected: 'Hoy', //boton seleccionado en calendario
        todayDate: new Date(),   // Fecha de hoy
        reminders: null, //recordatorios recuperados
        loading: false, //estado de carga para recuperar recordatorios por cada mes seleccionado
        drawer: false, //ventana lateral de detalles de recordatorio
        drawerSize: '90%', // Tamaño inicial del drawer
        reminderSelected: null, //recordatorio seleccionado para mostrar sus detalles
        showDeleteConfirmation: false, //modal de confirmacion de eliminación
        deleteOption: null, //opción de eliminado
        loadingDeleted: false, //estado de carga para eliminación
    }
},
components:{
    DeleteConfirmationModal,
    PrimaryButton,
    DangerButton,
    CancelButton,
    ThirthButton,
    LoadingLogo,
    AppLayout,
    Link
},
props:{
},
methods:{
    formatDate(date) {
        if (date) {
            const parsedDate = new Date(date);
            // Formato: lu, 02 ago 2024
            return format(parsedDate, 'EEE, dd MMM yyyy', { locale: es });
        }
        return ''; // Retorna cadena vacía si `date` no está definido
    },
    changeMonth(offset) {
      const newMonth = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth() + offset, 1);
      this.currentMonth = newMonth;
      this.fetchMonthReminders(); //recupera los recordatorios de ese mes
    },
    goToToday() {
      this.currentMonth = new Date(); // Restablece a la fecha actual
      this.fetchMonthReminders(); //recupera los recordatorios del mes
    },
    isToday(day) {
      const today = this.todayDate;
      const viewingMonth = this.currentMonth;
      return (
        day === today.getDate() &&
        viewingMonth.getMonth() === today.getMonth() &&
        viewingMonth.getFullYear() === today.getFullYear()
      );
    },
    isRemaindDay(remind, day) {
        if (day) {
            // Convierte `remind.date` a un objeto de fecha
            const remindDate = new Date(remind.date); // Asegúrate de que `remind.date` está en formato ISO o válido para Date

            // Construye la fecha del día actual que se está iterando en el calendario
            const currentDay = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth(), day);

            // Compara si las fechas son iguales (día, mes y año)
            return (
                remindDate.getFullYear() === currentDay.getFullYear() &&
                remindDate.getMonth() === currentDay.getMonth() &&
                remindDate.getDate() === currentDay.getDate()
            );
        }
        return false;
    },
    handleShowReminderDetails(reminder) {
        //guarda el recodatorio en una variable para mostrar sus detalles en el drawer
        this.reminderSelected = reminder;
        this.drawer = true;
    },
    updateDrawerSize() {
        const width = window.innerWidth;
        if (width < 768) { // Pantallas pequeñas (celulares)
            this.drawerSize = '85%';
        } else if (width >= 768 && width <= 1024) { // Tablets
            this.drawerSize = '60%';
        } else { // Pantallas grandes
            this.drawerSize = '30%';
        }
    },
    async handleDelete() {
        try {
            this.loadingDeleted = true;
            const response = await axios.delete(route('calendars.destroy', this.reminderSelected.id), {
                params: { deleteOption: this.deleteOption }
            });
            
            if ( response.status === 200 ) {
                this.$message({
                    type: 'success',
                    message: 'Eliminación exitosa'
                });
                this.fetchMonthReminders(); //actualiza los recordatorios
            }
            
        } catch (error) {
            console.log(error);
        } finally {
            this.loadingDeleted = false;
            this.drawer = false;
            this.showDeleteConfirmation = false;
        }
    },
    async fetchMonthReminders() {
        try {
            this.loading = true;

            // Obtener el mes y el año del currentMonth
            const currentMonth = this.currentMonth.getMonth() + 1; // Meses en JavaScript comienzan en 0
            const currentYear = this.currentMonth.getFullYear();

            // Realizar la petición al backend
            const response = await axios.post(route('calendars.fetch-month-reminders'), {
                month: currentMonth,
                year: currentYear,
            });

            if (response.status === 200) {
                this.reminders = response.data.reminders; // Ajusta según el nombre en el backend
            }
        } catch (error) {
            console.error(error);
        } finally {
            this.loading = false;
        }
    },
},
computed: {
    weeks() {
      const firstDayOfMonth = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth(), 1);
      const lastDayOfMonth = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth() + 1, 0);
      const startDayOfWeek = firstDayOfMonth.getDay(); // 0 (Domingo) a 6 (Sábado)
      const totalDaysInMonth = lastDayOfMonth.getDate();
      const days = [];
      let currentWeek = [];

      // Llena los días previos al primer día del mes con celdas vacías
      for (let i = 0; i < startDayOfWeek; i++) {
        currentWeek.push('');
      }

      // Agrega los números de los días del mes
      for (let i = 1; i <= totalDaysInMonth; i++) {
        currentWeek.push(i);
        if (currentWeek.length === 7) {
          days.push([...currentWeek]);
          currentWeek = [];
        }
      }

      // Añade la última semana si es necesario
      if (currentWeek.length > 0) {
        days.push([...currentWeek]);
      }

      return days;
    },
},
mounted() {
    this.fetchMonthReminders();
    this.updateDrawerSize(); // Ajusta el tamaño al cargar
    window.addEventListener('resize', this.updateDrawerSize); // Escucha cambios en el tamaño
  },
  beforeUnmount() {
    window.removeEventListener('resize', this.updateDrawerSize); // Limpia el evento
  },
}
</script>