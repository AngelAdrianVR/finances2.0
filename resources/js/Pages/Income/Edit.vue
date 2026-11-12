<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import IncomeForm from '@/Components/MyComponents/Forms/IncomeForm.vue';

defineProps({
    income: { type: Object, required: true },
});
</script>

<template>
    <AppLayout title="Editar ingreso">
        <IncomeForm mode="edit" :income="income" />
    </AppLayout>
</template>
