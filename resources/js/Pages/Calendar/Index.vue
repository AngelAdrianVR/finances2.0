<template>
    <AppLayout title="Calendario">
        <main class="px-2 md:px-10 pt-1">
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
                <div class="flex justify-between text-lg dark:text-white border-b border-grayD9 pb-5">
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
                <section v-else class="w-11/12 mx-auto">
                    <table class="w-full mt-12">
                    <tr class="text-center *:py-2 *:w-[14.28%] *:text-gray99">
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
                                        <p>{{ reminder.title }}</p>
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
            <h4>set title by slot</h4>
            </template>
            <template #default>
            <div>
                <el-radio v-model="radio1" value="Option 1" size="large">
                Option 1
                </el-radio>
                <el-radio v-model="radio1" value="Option 2" size="large">
                Option 2
                </el-radio>
            </div>
            </template>
            <template #footer>
            <div style="flex: auto">
                <el-button @click="cancelClick">cancel</el-button>
                <el-button type="primary" @click="confirmClick">confirm</el-button>
            </div>
            </template>
        </el-drawer>
    </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
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
        loading: false,
        drawer: false, //ventana lateral de detalles de recordatorio
        drawerSize: '90%', // Tamaño inicial del drawer
        reminderSelected: null, //recordatorio seleccionado para mostrar sus detalles
    }
},
components:{
    PrimaryButton,
    LoadingLogo,
    AppLayout,
    Link
},
props:{
},
methods:{
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
        this.reminderSelected = reminder;
        this.drawer = true;
    },
    updateDrawerSize() {
        const width = window.innerWidth;
        if (width < 768) { // Pantallas pequeñas (celulares)
            this.drawerSize = '70%';
        } else if (width >= 768 && width <= 1024) { // Tablets
            this.drawerSize = '60%';
        } else { // Pantallas grandes
            this.drawerSize = '30%';
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