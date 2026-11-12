<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import LoanForm from '@/Components/MyComponents/Forms/LoanForm.vue';

defineProps({
    loan: { type: Object, required: true },
});
</script>

<template>
    <AppLayout title="Editar préstamo">
        <LoanForm mode="edit" :loan="loan" />
    </AppLayout>
</template>
