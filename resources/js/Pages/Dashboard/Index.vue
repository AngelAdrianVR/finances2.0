<template>
    <AppLayout title="Panel de inicio">
        <main class="lg:flex space-x-5 p-2 lg:p-5">
            <section class="lg:w-[70%] space-y-7">
                <!-- Balance total -->
                <TotalBalance />

                <!-- Gastos -->
                <Outcomes />

                <!-- Estadísticas -->
                <Statistics />
            </section>

            <section class="lg:w-[30%] space-y-7">
                <!-- Estado de préstamos -->
                <LoanStatus />
            </section>
        </main>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import TotalBalance from '@/Components/MyComponents/Dashboard/TotalBalance.vue';
import Outcomes from '@/Components/MyComponents/Dashboard/Outcomes.vue';
import Statistics from '@/Components/MyComponents/Dashboard/Statistics.vue';
import LoanStatus from '@/Components/MyComponents/Dashboard/LoanStatus.vue';

export default {
data() {
    return {

    }
},
components:{
    TotalBalance,
    Statistics,
    LoanStatus,
    AppLayout,
    Outcomes,
},
props:{

}, 
methods:{

}
}
</script>