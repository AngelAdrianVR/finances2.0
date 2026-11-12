<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import RecurringMovementForm from '@/Components/MyComponents/Forms/RecurringMovementForm.vue';

defineProps({
    recurring_outcome: { type: Object, required: true },
});
</script>

<template>
    <AppLayout title="Editar gasto recurrente">
        <RecurringMovementForm type="outcome" mode="edit" :record="recurring_outcome" />
    </AppLayout>
</template>
