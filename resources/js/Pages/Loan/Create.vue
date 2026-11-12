<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import LoanForm from '@/Components/MyComponents/Forms/LoanForm.vue';
</script>

<template>
    <AppLayout title="Registrar préstamo">
        <LoanForm mode="create" />
    </AppLayout>
</template>
