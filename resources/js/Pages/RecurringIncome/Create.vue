<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import RecurringMovementForm from '@/Components/MyComponents/Forms/RecurringMovementForm.vue';
</script>

<template>
    <AppLayout title="Crear ingreso recurrente">
        <RecurringMovementForm type="income" mode="create" />
    </AppLayout>
</template>
