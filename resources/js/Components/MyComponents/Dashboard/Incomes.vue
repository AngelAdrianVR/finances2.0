<template>
    <main class="border border-grayD9 rounded-xl p-5 shadow hover:shadow-lg transition-all ease-linear duration-200 hover:-translate-y-1">
        <h1 class="text-2xl text-[#575757] font-bold my-3 ml-4">INGRESOS</h1>

        <section class="sm:grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 space-y-2 sm:space-y-0">
            <!-- Ventas -->
            <div @click="handleShowDetailIncomes('Ventas')" class="border border-grayD9 rounded-lg p-4 cursor-pointer flex space-x-5 transition-all ease-linear duration-200 hover:scale-105">
                <div class="rounded-full size-11 flex items-center justify-center bg-green-100 text-[#147D85]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-[#353535]">Ventas</p>
                    <p class="text-base md:text-lg font-bold text-[#575757]">$ {{ sales.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                </div>
            </div>
            <!-- Intereses -->
            <div @click="handleShowDetailIncomes('Intereses')" class="border border-grayD9 rounded-lg p-4 cursor-pointer flex space-x-5 transition-all ease-linear duration-200 hover:scale-105">
                <div class="rounded-full size-11 flex items-center justify-center bg-sky-100 text-sky-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-[#353535]">Intereses</p>
                    <p class="text-base md:text-lg font-bold text-[#575757]">$ {{ intereses.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                </div>
            </div>
            <!-- Nómina -->
            <div @click="handleShowDetailIncomes('Nómina')" class="border border-grayD9 rounded-lg p-4 cursor-pointer flex space-x-5 transition-all ease-linear duration-200 hover:scale-105">
                <div class="rounded-full size-11 flex items-center justify-center bg-slate-200 text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-[#353535]">Nómina</p>
                    <p class="text-base md:text-lg font-bold text-[#575757]">$ {{ payroll.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                </div>
            </div>
            <!-- Prestación de servicios -->
            <div @click="handleShowDetailIncomes('Prestación de servicios')" class="border border-grayD9 rounded-lg p-4 cursor-pointer flex space-x-5 transition-all ease-linear duration-200 hover:scale-105">
                <div class="rounded-full size-11 flex items-center justify-center bg-stone-200 text-stone-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-[#353535]">Prestación de servicios</p>
                    <p class="text-base md:text-lg font-bold text-[#575757]">$ {{ service.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                </div>
            </div>
            <!-- Comisión -->
            <div @click="handleShowDetailIncomes('Comisión')" class="border border-grayD9 rounded-lg p-4 cursor-pointer flex space-x-5 transition-all ease-linear duration-200 hover:scale-105">
                <div class="rounded-full size-11 flex items-center justify-center bg-pink-100 text-pink-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-[#353535]">Comisión</p>
                    <p class="text-base md:text-lg font-bold text-[#575757]">$ {{ comision.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                </div>
            </div>
            <!-- Renta -->
            <div @click="handleShowDetailIncomes('Renta')" class="border border-grayD9 rounded-lg p-4 cursor-pointer flex space-x-5 transition-all ease-linear duration-200 hover:scale-105">
                <div class="rounded-full size-11 flex items-center justify-center bg-indigo-100 text-indigo-500">
                    <svg width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.30262 21.7348V11.3028H1.67485C0.669061 11.3028 0.529547 9.88183 1.67371 8.59949L4.96619 5.93086V2.84633C4.96619 2.22249 5.07016 2.01454 5.65934 2.01454H7.25359C7.74835 1.97325 7.95665 2.08019 8.01606 2.84633V3.6088L11.7591 0.558941C12.8681 -0.168887 13.0414 -0.203539 13.9425 0.558941L24.1319 8.5302C25.8301 9.88185 24.9637 11.3028 24.1319 11.3028H22.399V21.9427C22.399 22.9131 22.3306 23.5 21.4988 23.5C20.2778 23.5 20.3635 23.3637 15.5 23.5V14.5H11V23.5H4.49878C3.21645 23.5 3.30262 22.9478 3.30262 21.7348Z" fill="#4B0082"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-[#353535]">Renta</p>
                    <p class="text-base md:text-lg font-bold text-[#575757]">$ {{ rent.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                </div>
            </div>
            <!-- Otros -->
            <div @click="handleShowDetailIncomes('Otro')" class="border border-grayD9 rounded-lg p-4 cursor-pointer flex space-x-5 transition-all ease-linear duration-200 hover:scale-105">
                <div class="rounded-full size-11 flex items-center justify-center bg-[#F3C9C9]">
                    <i class="fa-solid fa-ellipsis text-2xl text-[#851414]"></i>
                </div>
                <div>
                    <p class="text-xs text-[#353535]">Otros</p>
                    <p class="text-base md:text-lg font-bold text-[#575757]">$ {{ another.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                </div>
            </div>
        </section>

        <!-- -------------- Show Modal starts----------------------- -->
        <DialogModal :show="showIncomesDetailsModal" @close="showIncomesDetailsModal = false">
            <template #title>
                <h1>Ingresos de {{ categorySelected }}</h1>
            </template>

            <template #content>
                   <div class="mt-4 max-h-80 overflow-auto">
                        <table v-if="incomes.some(item => item.category === categorySelected)" class="w-full mx-auto text-xs md:text-sm">
                            <thead>
                                <tr class="text-left border-b border-primary">
                                    <th class="font-bold">Concepto</th>
                                    <th class="font-bold">Monto</th>
                                    <th class="font-bold">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-left"
                                    v-for="item in incomes.filter(item => item.category === categorySelected)" :key="item">
                                    <td class="py-1">
                                        <p class="truncate w-28 md:w-44 lg:w-56">
                                            {{ item.concept }}
                                        </p>
                                    </td>
                                    <td class="py-1">$ {{ item.amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                                    <td class="py-1">{{ formatDate(item.created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-else>
                            <p class="text-sm text-center text-gray-400 mt-4">No hay registros para mostrar</p>
                        </div>
                    </div>
            </template>
            <template #footer>
                <PrimaryButton @click="showIncomesDetailsModal = false">Cerrar</PrimaryButton>
            </template>
        </DialogModal>
        <!-- --------------------------- Modal ends ------------------------------------ -->
    </main>
</template>

<script>
import DialogModal from "@/Components/DialogModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
data() {
    return {
        sales: 0,
        intereses: 0,
        payroll: 0,
        service: 0,
        comision: 0,
        rent: 0,
        education: 0,
        another: 0,

        //general
        categorySelected: null,
        showIncomesDetailsModal: false,
    }
},
components:{
    PrimaryButton,
    DialogModal,
},
props:{
    incomes: Array
},
watch: {
    incomes: {
        immediate: true, // Ejecuta la lógica cuando `incomes` se inicializa
        handler(newIncomes) {
            this.calculateCategoryTotals(newIncomes);
        },
    },
},
methods:{
    formatDate(date) {
        if ( date ) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'dd MMM yyyy', { locale: es }); // Formato personalizado
        }
    },
    handleShowDetailIncomes(category) {
        this.categorySelected = category;
        this.showIncomesDetailsModal = true; 
    },
    calculateCategoryTotals(incomes) {
        // Reinicia los totales
        this.sales = 0;
        this.intereses = 0;
        this.payroll = 0;
        this.service = 0;
        this.comision = 0;
        this.rent = 0;
        this.another = 0;
        
        // Itera sobre los incomes y asigna los montos según la categoría
        incomes?.forEach((income) => {
            switch (income.category) {
            case "Ventas":
                this.sales += income.amount;
                break;
            case "Intereses":
                this.intereses += income.amount;
                break;
            case "Nómina":
                this.payroll += income.amount;
                break;
            case "Prestación de servicios":
                this.service += income.amount;
                break;
            case "Comisión":
                this.comision += income.amount;
                break;
            case "Renta":
                this.rent += income.amount;
                break;
            default:
                this.another += income.amount;
                break;
            }
        });
    },
},
}
</script>