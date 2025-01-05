<template>
    <figure class="w-full relative bg-[#B1E33D] h-72 lg:h-44 rounded-lg">
        <!-- <img class="w-full object-contain" src="@/../../public/images/total_balance.png" alt=""> -->
        
        <section class="w-full absolute top-0 left-0">
            <h2 class="text-lg md:text-2xl font-bold text-gray-600 m-3">Balance total (mes actual vs anterior)</h2>
            
            <article class="flex flex-col md:flex-row items-center justify-start space-y-3 md:space-y-0 md:space-x-3 p-3">
                <!-- Ingresos -->
                <div class="bg-white bg-opacity-60 py-2 px-3 rounded-lg w-full">
                    <h2 class="text-gray-700 font-bold text-sm md:text-base">Ingresaste</h2>
                    <p class="text-base md:text-xl font-bold mt-1">$ {{ currentMonthIncome.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                    <p class="text-xs text-[#797A79] flex items-center space-x-1 mt-1">
                        <span class="rounded-sm px-1 py-[2px]" :class="incomeComparison === 'fa-arrow-up' ? 'bg-[#BBF9AC] text-green-600' : 'bg-[#F9ACAC] text-red-700'">
                            <i :class="incomeComparison" class="fa-solid"></i>
                            {{ incomePercentage }}%
                        </span>
                        <span>Comparado con el mes anterior</span>
                    </p>
                </div>
                <!-- Gastos -->
                <div class="bg-white bg-opacity-60 py-2 px-3 rounded-lg w-full">
                    <h2 class="text-gray-700 font-bold text-sm md:text-base">Gastaste</h2>
                    <p class="text-base md:text-xl font-bold mt-1">$ {{ currentMonthOutcome.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                    <p class="text-xs text-[#797A79] flex items-center space-x-1 mt-1">
                        <span class="rounded-sm px-1 py-[2px]" :class="outcomeComparison === 'fa-arrow-down' ? 'bg-[#BBF9AC] text-green-600' : 'bg-[#F9ACAC] text-red-700'">
                            <i :class="outcomeComparison" class="fa-solid"></i>
                            {{ outcomePercentage }}%
                        </span>
                        <span>Comparado con el mes anterior</span>
                    </p>
                </div>
                <!-- Saldo disponible -->
            </article>
            
        </section>
    </figure>
</template>

<script>
export default {
data() {
    return {
        currentMonthIncome: 0, // Ingresos del mes actual
        prevMonthIncome: 0, // Ingresos del mes anterior
        currentMonthOutcome: 0, // Gastos del mes actual
        prevMonthOutcome: 0, // Gastos del mes anterior
    }
},
props:{

},
computed:{
    incomePercentage() {
      const incomePercentage = ((this.currentMonthIncome - this.prevMonthIncome) / this.prevMonthIncome) * 100;
      return Math.abs(incomePercentage).toFixed(2); // Retorna el porcentaje sin signo
    },
    outcomePercentage() {
      const outcomePercentage = ((this.currentMonthOutcome - this.prevMonthOutcome) / this.prevMonthOutcome) * 100;
      return Math.abs(outcomePercentage).toFixed(2); // Retorna el porcentaje sin signo
    },
    incomeComparison() {
      const percentage = (this.currentMonthIncome - this.prevMonthIncome) / this.prevMonthIncome;
      return percentage >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'; // Clase basada en el porcentaje
    },
    outcomeComparison() {
      const percentage = (this.currentMonthOutcome - this.prevMonthOutcome) / this.prevMonthOutcome;
      return percentage >= 0 ? 'fa-arrow-down' : 'fa-arrow-up'; // Clase basada en el porcentaje
    },
},
methods:{
    async fetchDataComparison(){
        try {
            const response = await axios.get(route('dashboard.fetch-data-comparison'));
            if(response.status === 200){
                // console.log(response.data);
                this.currentMonthIncome = response.data.current_month_income;
                this.prevMonthIncome = response.data.prev_month_income;
                this.currentMonthOutcome = response.data.current_month_outcome;
                this.prevMonthOutcome = response.data.prev_month_outcome;
            }
        } catch (error) {
            console.error(error);
        }
    }
},
mounted(){
    this.fetchDataComparison();
}
}
</script>
