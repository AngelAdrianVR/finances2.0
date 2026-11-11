<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import RecurringMovementForm from '@/Components/MyComponents/Forms/RecurringMovementForm.vue';
</script>

<template>
    <AppLayout title="Crear gasto recurrente">
        <RecurringMovementForm type="outcome" mode="create" />
    </AppLayout>
</template>
