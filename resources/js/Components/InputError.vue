<script setup>
defineProps({
    message: String,
});
</script>

<template>
    <div v-show="message">
        <p class="text-xs ml-3 text-red-600">
            {{ message }}
        </p>
    </div>
</template>
