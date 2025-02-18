<template>
    <figure class="w-full relative bg-[#B1E33D] h-72 lg:h-44 rounded-lg">
        
        <section class="w-full absolute top-0 left-0">
            <h2 class="text-lg md:text-2xl font-bold text-gray-600 m-3">Balance total {{ periodicity }} (actual vs anterior)</h2>
            
            <article class="flex flex-col md:flex-row items-center justify-start space-y-3 md:space-y-0 md:space-x-3 p-3">
                <!-- Ingresos -->
                <div class="bg-white bg-opacity-60 py-2 px-3 rounded-lg w-full">
                    <h2 class="text-gray-700 font-bold text-sm md:text-base">Ingresaste</h2>
                    <p class="text-base md:text-xl font-bold mt-1">$ {{ currentIncome?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                    <p class="text-xs text-[#797A79] flex items-center space-x-1 mt-1">
                        <span class="rounded-sm px-1 py-[2px]" :class="incomeComparison === 'fa-arrow-up' ? 'bg-[#BBF9AC] text-green-600' : 'bg-[#F9ACAC] text-red-700'">
                            <i :class="incomeComparison" class="fa-solid"></i>
                            {{ incomePercentage }}%
                        </span>
                        <span>Comparado con el anterior (${{ prevIncome?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }})</span>
                    </p>
                </div>
                <!-- Gastos -->
                <div class="bg-white bg-opacity-60 py-2 px-3 rounded-lg w-full">
                    <h2 class="text-gray-700 font-bold text-sm md:text-base">Gastaste</h2>
                    <p class="text-base md:text-xl font-bold mt-1">$ {{ currentOutcome?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                    <p class="text-xs text-[#797A79] flex items-center space-x-1 mt-1">
                        <span class="rounded-sm px-1 py-[2px]" :class="outcomeComparison === 'fa-arrow-down' ? 'bg-[#BBF9AC] text-green-600' : 'bg-[#F9ACAC] text-red-700'">
                            <i :class="outcomeComparison" class="fa-solid"></i>
                            {{ outcomePercentage }}%
                        </span>
                        <span>Comparado con el anterior (${{ prevOutcome?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }})</span>
                    </p>
                </div>
            </article>
            
        </section>
    </figure>
</template>

<script>
export default {
data() {
    return {
        currentIncome: 0, // Ingresos del mes actual
        prevIncome: 0, // Ingresos del mes anterior
        currentOutcome: 0, // Gastos del mes actual
        prevOutcome: 0, // Gastos del mes anterior
    }
},
props:{
    periodicity: String
},
computed:{
    incomePercentage() {
        if (this.prevIncome === 0 && this.currentIncome > 0) {
            return 100; // Evita la división por cero y devuelve 100
        }
        
        const incomePercentage = ((this.currentIncome - this.prevIncome) / this.prevIncome) * 100;
        return incomePercentage ? Math.abs(incomePercentage).toFixed(2) : 0; // Retorna el porcentaje sin signo
    },
    outcomePercentage() {
        if (this.prevOutcome === 0 && this.currentOutcome > 0) {
            return 100; // Evita la división por cero y devuelve 100
        }

        const outcomePercentage = ((this.currentOutcome - this.prevOutcome) / this.prevOutcome) * 100;
        return outcomePercentage ? Math.abs(outcomePercentage).toFixed(2) : 0; // Retorna el porcentaje sin signo
    },
    incomeComparison() {
      const percentage = (this.currentIncome - this.prevIncome) / this.prevIncome;
      return percentage >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'; // Clase basada en el porcentaje
    },
    outcomeComparison() {
      const percentage = (this.currentOutcome - this.prevOutcome) / this.prevOutcome;
      return percentage <= 0 ? 'fa-arrow-down' : 'fa-arrow-up'; // Clase basada en el porcentaje
    },
},
methods:{
    async fetchDataComparison(){
        try {
            const response = await axios.post(route('dashboard.fetch-data-comparison'), { periodicity: this.periodicity });
            if(response.status === 200){
                // console.log(response.data);
                this.currentIncome = response.data.current_income;
                this.prevIncome = response.data.prev_income;
                this.currentOutcome = response.data.current_outcome;
                this.prevOutcome = response.data.prev_outcome;
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
