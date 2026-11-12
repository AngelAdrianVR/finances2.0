<script setup>
defineProps({
    title: { type: String, required: true },
    description: { type: String, default: '' },
    tone: { type: String, default: 'default' },
});
</script>

<template>
    <section class="profile-section" :class="`profile-section--${tone}`">
        <header class="profile-section__header">
            <h2 class="profile-section__title">{{ title }}</h2>
            <p v-if="description" class="profile-section__description">{{ description }}</p>
        </header>

        <div class="profile-section__body">
            <slot />
        </div>

        <footer v-if="$slots.actions" class="profile-section__footer">
            <slot name="actions" />
        </footer>
    </section>
</template>
