<script setup>
defineProps({
    name: { type: String, required: true },
    size: { type: [Number, String], default: 16 },
});
</script>

<template>
    <svg
        :width="size"
        :height="size"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.7"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
        class="payment-icon"
    >
        <template v-if="name === 'cash'">
            <rect x="2.75" y="6.75" width="18.5" height="10.5" rx="2.5" />
            <circle cx="12" cy="12" r="2.25" />
            <path d="M6 12h.01M18 12h.01" />
        </template>

        <template v-else-if="name === 'transfer'">
            <path d="M4 8.5h13" />
            <path d="m14 5.5 3 3-3 3" />
            <path d="M20 15.5H7" />
            <path d="m10 12.5-3 3 3 3" />
        </template>

        <template v-else-if="name === 'deposit'">
            <path d="M3.5 9.5 12 4.5l8.5 5" />
            <path d="M5.5 10.5v6M9.75 10.5v6M14.25 10.5v6M18.5 10.5v6" />
            <path d="M3.5 19.5h17" />
        </template>

        <template v-else-if="name === 'cheque'">
            <rect x="3.25" y="5.75" width="17.5" height="12.5" rx="2.5" />
            <path d="M7 10.5h6M7 14h4" />
            <path d="M15.5 14.5 17 13l1.5 1.5" />
        </template>

        <template v-else-if="name === 'card'">
            <rect x="2.75" y="5.75" width="18.5" height="12.5" rx="2.5" />
            <path d="M2.75 10h18.5" />
            <path d="M6.5 14.5h3" />
        </template>

        <template v-else-if="name === 'calendar'">
            <rect x="3.25" y="5.25" width="17.5" height="15.5" rx="2.5" />
            <path d="M8 3.5v3.5M16 3.5v3.5M3.25 10.5h17.5" />
        </template>

        <template v-else-if="name === 'refresh'">
            <path d="M20 12a8 8 0 1 1-2.4-5.7" />
            <path d="M20 4.5V10h-5.5" />
        </template>

        <template v-else>
            <circle cx="12" cy="12" r="3" />
        </template>
    </svg>
</template>
