<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import OutcomeForm from '@/Components/MyComponents/Forms/OutcomeForm.vue';

defineProps({
    outcome: { type: Object, required: true },
});
</script>

<template>
    <AppLayout title="Editar gasto">
        <OutcomeForm mode="edit" :outcome="outcome" />
    </AppLayout>
</template>
