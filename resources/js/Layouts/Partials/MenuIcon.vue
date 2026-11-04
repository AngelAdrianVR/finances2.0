<script setup>
defineProps({
    name: { type: String, required: true },
    size: { type: [Number, String], default: 20 },
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
        class="menu-icon-svg"
    >
        <!-- Panel / dashboard -->
        <template v-if="name === 'dashboard'">
            <rect x="3" y="3" width="7.5" height="7.5" rx="2" />
            <rect x="13.5" y="3" width="7.5" height="4.5" rx="2" />
            <rect x="13.5" y="10.5" width="7.5" height="10.5" rx="2" />
            <rect x="3" y="13.5" width="7.5" height="7.5" rx="2" />
        </template>

        <!-- Transactions -->
        <template v-else-if="name === 'transactions'">
            <path d="M4 7.5h13.5" />
            <path d="M14.5 4.5 17.5 7.5l-3 3" />
            <path d="M20 16.5H6.5" />
            <path d="M9.5 13.5l-3 3 3 3" />
        </template>

        <!-- Income -->
        <template v-else-if="name === 'income'">
            <path d="M12 3.5v11" />
            <path d="m7.75 10.5 4.25 4 4.25-4" />
            <path d="M4.5 16.75v1.5A2.25 2.25 0 0 0 6.75 20.5h10.5a2.25 2.25 0 0 0 2.25-2.25v-1.5" />
        </template>

        <!-- Outcome -->
        <template v-else-if="name === 'outcome'">
            <path d="M12 14.5v-11" />
            <path d="m7.75 7.5 4.25-4 4.25 4" />
            <path d="M4.5 16.75v1.5A2.25 2.25 0 0 0 6.75 20.5h10.5a2.25 2.25 0 0 0 2.25-2.25v-1.5" />
        </template>

        <!-- Loans / banknotes -->
        <template v-else-if="name === 'loans'">
            <rect x="2.75" y="6.25" width="18.5" height="11.5" rx="2.5" />
            <circle cx="12" cy="12" r="2.5" />
            <path d="M6.25 10v4M17.75 10v4" />
        </template>

        <!-- Settings -->
        <template v-else-if="name === 'settings'">
            <circle cx="12" cy="12" r="3" />
            <path d="M19.25 12c0 .46-.05.9-.14 1.33l1.86 1.42-2.06 3.56-2.2-.88a7.6 7.6 0 0 1-2.3 1.33l-.33 2.34h-4.12l-.33-2.34a7.6 7.6 0 0 1-2.3-1.33l-2.2.88-2.06-3.56 1.86-1.42a7.6 7.6 0 0 1 0-2.66L2.6 9.25l2.06-3.56 2.2.88a7.6 7.6 0 0 1 2.3-1.33l.33-2.34h4.12l.33 2.34a7.6 7.6 0 0 1 2.3 1.33l2.2-.88 2.06 3.56-1.86 1.42c.09.43.14.87.14 1.33Z" />
        </template>

        <!-- Chevron -->
        <template v-else-if="name === 'chevron'">
            <path d="m9 6 6 6-6 6" />
        </template>

        <!-- Collapse -->
        <template v-else-if="name === 'collapse'">
            <rect x="3" y="4" width="18" height="16" rx="2.5" />
            <path d="M9.5 4v16" />
            <path d="m15.5 9.5-2.5 2.5 2.5 2.5" />
        </template>

        <!-- Logout -->
        <template v-else-if="name === 'logout'">
            <path d="M15.75 9V5.75A2.25 2.25 0 0 0 13.5 3.5h-6a2.25 2.25 0 0 0-2.25 2.25v12.5A2.25 2.25 0 0 0 7.5 20.5h6a2.25 2.25 0 0 0 2.25-2.25V15" />
            <path d="M17.5 12H9.75" />
            <path d="m14.75 8.75 3.25 3.25-3.25 3.25" />
        </template>

        <!-- Dot fallback -->
        <template v-else>
            <circle cx="12" cy="12" r="3" />
        </template>
    </svg>
</template>
