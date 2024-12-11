<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center justify-center px-4 py-2 border border-grayD9 rounded-md font-semibold text-xs text-[#575757] uppercase hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-2 transition ease-in-out duration-150">
        <slot />
    </button>
</template>
