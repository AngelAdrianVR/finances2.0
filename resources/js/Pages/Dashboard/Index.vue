<template>
    <AppLayout title="Panel de inicio">
        <main class="p-2">
            <!-- Temporalidad -->
            <div class="custom-style w-72 md:w-96 mx-auto">
              <el-segmented @change="changeType" v-model="periodicity" :options="options" block />

              <!-- Selector diario -->
              <div  class="mt-2 text-center" v-if=" periodicity === 'Por día'">
                  <el-date-picker
                    @change="fetchDataForPeriod()"
                    v-model="period"
                    type="date"
                    placeholder="Selecciona un día"
                  />
              </div>

              <!-- Selector semanal -->
              <div class="mt-2 text-center" v-if=" periodicity === 'Semanal'">
                <el-date-picker
                  @change="fetchDataForPeriod()"
                  v-model="period"
                  type="week"
                  format="[Week] ww"
                  placeholder="Selecciona una semana"
                />
              </div>

              <!-- Selector mensual -->
              <div class="mt-2 text-center" v-if=" periodicity === 'Mensual'">
                <el-date-picker
                  @change="fetchDataForPeriod()"
                  v-model="period"
                  type="month"
                  placeholder="Selecciona un mes"
                />
              </div>

              <!-- Selector anual -->
              <div class="mt-2 text-center" v-if=" periodicity === 'Anual'">
                <el-date-picker
                  @change="fetchDataForPeriod()"
                  v-model="period"
                  type="year"
                  placeholder="Selecciona un año"
                />
              </div>
            </div>

            <!-- estado de carga -->
            <LoadingLogo v-if="loading" class="mt-4 lg:mt-20" />

            <article v-else class="lg:flex lg:space-x-5 mt-5">
                <section class="lg:w-[70%] space-y-7">
                    <!-- Balance total -->
                    <TotalBalance />

                    <!-- Gastos -->
                    <Outcomes :outcomes="outcomes" />

                    <!-- Estadísticas -->
                    <Statistics :outcomes="outcomes" :incomes="incomes" />
                </section>

                <section class="lg:w-[30%] space-y-7 mt-7 lg:mt-0">
                    <!-- Estado de préstamos -->
                    <LoanStatus :loans="loans" />

                    <!-- Inversiones -->
                    <Investments />
                </section>
            </article>
        </main>        
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import TotalBalance from '@/Components/MyComponents/Dashboard/TotalBalance.vue';
import Outcomes from '@/Components/MyComponents/Dashboard/Outcomes.vue';
import Statistics from '@/Components/MyComponents/Dashboard/Statistics.vue';
import LoanStatus from '@/Components/MyComponents/Dashboard/LoanStatus.vue';
import Investments from '@/Components/MyComponents/Dashboard/Investments.vue';
import LoadingLogo from "@/Components/MyComponents/LoadingLogo.vue";
import axios from 'axios';

export default {
data() {
    return {
      loading: false,
      period: null, //periodo de tiempo seleccionado
      outcomes: null, //gastos recuperados de la petición
      incomes: null, //ingresos recuperados de la petición
      loans: null, //préstamos recuperados de la petición
      periodicity: 'Por día',
      options: [
        'Por día',
        'Semanal',
        'Mensual',
        'Anual',
      ],
    }
},
components:{
    TotalBalance,
    LoadingLogo,
    Investments,
    Statistics,
    LoanStatus,
    AppLayout,
    Outcomes,
},
props:{

}, 
methods:{
    changeType(newVal) {
        this.period = null;
    },
    async fetchDataForPeriod() {
      try {
        this.loading = true;
        const response = await axios.post(route('dashboard.fetch-data-for-period'), { periodicity: this.periodicity, period: this.period });
        if ( response.status === 200 ) {
          this.outcomes = response.data.outcomes;
          this.incomes = response.data.incomes;
          this.loans = response.data.loans;
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.loading = false;
      }
    }
},
mounted() {
    this.fetchDataForPeriod();

    //setear la fecha del periodo al dia de hoy despues de peticion porque daba error xD
    this.period = new Date().toISOString();
}
}
</script>

<style>
.custom-style .el-segmented {
  --el-segmented-item-selected-color: #000;
  --el-segmented-bg-color: #F2F2F2;
  --el-segmented-item-selected-bg-color: #fff;
  --el-border-radius-base: 10px;
  --el-segmented-item-selected-border-color: #D9D9D9;
}
</style>