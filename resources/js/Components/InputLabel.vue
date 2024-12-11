<script setup>
defineProps({
    value: String,
});
</script>

<template>
    <label class="block font-medium text-sm ml-3 mb-px">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
