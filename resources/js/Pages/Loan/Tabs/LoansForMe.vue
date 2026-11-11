<script setup>
import LoansTable from '@/Components/MyComponents/Loan/LoansTable.vue';

defineProps({
    loans: { type: Object, required: true },
    summary: { type: Object, default: () => ({}) },
});
</script>

<template>
    <LoansTable :loans="loans" type="Recibido" :summary="summary" />
</template>
