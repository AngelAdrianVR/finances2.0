<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import RecurringMovementForm from '@/Components/MyComponents/Forms/RecurringMovementForm.vue';

defineProps({
    recurring_income: { type: Object, required: true },
});
</script>

<template>
    <AppLayout title="Editar ingreso recurrente">
        <RecurringMovementForm type="income" mode="edit" :record="recurring_income" />
    </AppLayout>
</template>
