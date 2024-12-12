<template>
    <main class="border border-grayD9 rounded-xl p-5 shadow hover:shadow-lg transition-all ease-linear duration-200 hover:-translate-y-1">
        <h2 class="text-[#575757] text-2xl font-bold">INVERSIÓN</h2>
        <Spline :chartOptions="options" :series="series" />
    </main>
</template>

<script>
import Spline from '@/Components/MyComponents/Dashboard/Chart/Area/Spline.vue';

export default {
    data() {
        return {

        }
    },
    components:{
        Spline
    }
}
</script>
