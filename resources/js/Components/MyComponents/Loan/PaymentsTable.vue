<template>
    <div class="flex items-center justify-between mb-5">
        <h2 class="font-bold col-span-full mb-4">Desgloce del préstamo</h2>
        <PrimaryButton>Registrar abono</PrimaryButton>
    </div>
    <table class="min-w-full bg-white border border-gray-300">
        <thead>
            <tr class="*:px-4 *:py-2 *:border-b *:border-gray-300 *:text-left *:text-gray-600 *:font-semibold">
                <th>Monto</th>
                <th>Intereses</th>
                <th>Abono</th>
                <th>Fecha</th>
                <th>Pago a capital</th>
                <th>Comentarios</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b border-gray-300">
                <td class="px-4 py-2 text-gray-800">$35,000.00</td>
                <td class="px-4 py-2 text-gray-800">$700.00</td>
                <td class="px-4 py-2 text-gray-800">$800.00</td>
                <td class="px-4 py-2 text-gray-800">13 nov 2024</td>
                <td class="px-4 py-2 text-gray-800">$100.00</td>
                <td class="px-4 py-2 text-gray-800">Mes diciembre</td>
                <td class="px-4 py-2 text-gray-800 relative">
                    <button class="text-gray-500 hover:text-gray-700 focus:outline-none">⋮</button>
                    <!-- Dropdown menu -->
                    <div class="absolute right-0 mt-2 w-40 bg-white rounded-md shadow-lg border border-gray-200 hidden">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Editar abono</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Eliminar abono</a>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <DialogModal :show="showPendentInvitationsModal" @close="showPendentInvitationsModal = false">
        <template #title>
            Invitaciones pendientes
        </template>
        <template #content>
            
        </template>
        <template #footer>

        </template>
    </DialogModal>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DialogModal from "@/Components/DialogModal.vue";

export default {
data() {
    return {

    }
},
components:{
    PrimaryButton,
    DialogModal
},
props:{

},
methods:{

}
}
</script>