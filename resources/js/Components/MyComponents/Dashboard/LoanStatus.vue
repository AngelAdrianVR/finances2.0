<template>
    <main class="border border-grayD9 rounded-xl p-5 text-sm">
        <h2 class="text-[#575757] text-2xl border-b border-grayD9 font-bold pb-3">ESTADO DE PRÉSTAMOS</h2>

        <section class="my-2 pt-2 grid grid-cols-2 gap-3 pb-2">
            <div>
                <p class="font-bold">Recibidos</p>

                <p class="text-[#717171] mt-2">Total de préstamos recibidos:</p>
                <p class="ml-2">$ 14,000.00</p>

                <p class="text-[#717171] mt-2">Total pagado:</p>
                <p class="ml-2">$ 4,000.00</p>

                <p class="text-[#717171] mt-2">Saldo restante:</p>
                <p class="ml-2 font-bold">$ 10,000.00</p>
            </div>

            <div>
                <Basic :series="recievedLoans" />
            </div>
        </section>

        <section class="my-2 grid grid-cols-2 gap-3 border-t border-grayD9 pt-4">
            <div>
                <p class="font-bold">Otorgados</p>

                <p class="text-[#717171] mt-2">Total de préstamos otorgados:</p>
                <p class="ml-2">$ 14,000.00</p>

                <p class="text-[#717171] mt-2">Total pagado:</p>
                <p class="ml-2">$ 4,000.00</p>

                <p class="text-[#717171] mt-2">Saldo restante:</p>
                <p class="ml-2 font-bold">$ 10,000.00</p>

                <p class="text-[#717171] mt-2">Interés acumulado:</p>
                <p class="ml-2 font-bold">$ 3,000.00</p>
            </div>

            <div>
                <Basic :series="givenLoans" />
            </div>
        </section>
    </main>
</template>

<script>
import Basic from '@/Components/MyComponents/Dashboard/Chart/RadialBar/Basic.vue';

export default {
data() {
    return {
        //charts
        recievedLoans: [78], //Recibidos
        givenLoans: [56], //Otorgados
    }
},
components:{
    Basic
},
props:{

},
methods:{

}
}
</script>