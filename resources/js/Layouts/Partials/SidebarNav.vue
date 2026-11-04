<script setup>
import { ref, computed, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import MenuIcon from '@/Layouts/Partials/MenuIcon.vue';

const props = defineProps({
    sections: { type: Array, required: true },
    activeIndex: { type: String, default: '' },
    collapsed: { type: Boolean, default: false },
});

const expandedGroups = ref([]);

const activeGroupIndex = computed(() => {
    for (const section of props.sections) {
        for (const item of section.items) {
            if (item.children?.some((child) => child.index === props.activeIndex)) {
                return item.index;
            }
        }
    }

    return null;
});

watch(
    activeGroupIndex,
    (groupIndex) => {
        if (groupIndex && !expandedGroups.value.includes(groupIndex)) {
            expandedGroups.value = [...expandedGroups.value, groupIndex];
        }
    },
    { immediate: true }
);

function toggleGroup(index) {
    expandedGroups.value = expandedGroups.value.includes(index)
        ? expandedGroups.value.filter((value) => value !== index)
        : [...expandedGroups.value, index];
}

function isExpanded(index) {
    return expandedGroups.value.includes(index);
}

function isActive(item) {
    return item.index === props.activeIndex;
}

function hasActiveChild(item) {
    return Boolean(item.children?.some((child) => child.index === props.activeIndex));
}
</script>

<template>
    <nav class="sidebar-nav" :class="{ 'is-collapsed': collapsed }">
        <div v-for="section in sections" :key="section.label" class="sidebar-section">
            <p v-if="!collapsed" class="sidebar-section__label">{{ section.label }}</p>
            <span v-else class="sidebar-section__divider" aria-hidden="true" />

            <ul class="sidebar-list">
                <li v-for="item in section.items" :key="item.index" class="sidebar-list__item">
                    <!-- Direct link -->
                    <Link
                        v-if="!item.children?.length"
                        :href="route(item.routeName)"
                        class="sidebar-link"
                        :class="{ 'is-active': isActive(item) }"
                        :data-label="item.label"
                    >
                        <span class="sidebar-link__icon">
                            <MenuIcon :name="item.icon" />
                        </span>
                        <span v-if="!collapsed" class="sidebar-link__label">{{ item.label }}</span>
                    </Link>

                    <!-- Group (expanded sidebar) -->
                    <template v-else-if="!collapsed">
                        <button
                            type="button"
                            class="sidebar-link"
                            :class="{ 'is-open': isExpanded(item.index), 'is-parent-active': hasActiveChild(item) }"
                            @click="toggleGroup(item.index)"
                        >
                            <span class="sidebar-link__icon">
                                <MenuIcon :name="item.icon" />
                            </span>
                            <span class="sidebar-link__label">{{ item.label }}</span>
                            <span class="sidebar-link__chevron" :class="{ 'is-rotated': isExpanded(item.index) }">
                                <MenuIcon name="chevron" :size="14" />
                            </span>
                        </button>

                        <ul v-show="isExpanded(item.index)" class="sidebar-sublist">
                            <li v-for="child in item.children" :key="child.index">
                                <Link
                                    :href="route(child.routeName)"
                                    class="sidebar-sublink"
                                    :class="{ 'is-active': child.index === activeIndex }"
                                >
                                    <span class="sidebar-sublink__dot" />
                                    {{ child.label }}
                                </Link>
                            </li>
                        </ul>
                    </template>

                    <!-- Group (collapsed sidebar → flyout) -->
                    <div v-else class="sidebar-flyout-wrap">
                        <button
                            type="button"
                            class="sidebar-link"
                            :class="{ 'is-parent-active': hasActiveChild(item) }"
                            :data-label="item.label"
                        >
                            <span class="sidebar-link__icon">
                                <MenuIcon :name="item.icon" />
                            </span>
                        </button>

                        <div class="sidebar-flyout">
                            <p class="sidebar-flyout__title">{{ item.label }}</p>
                            <Link
                                v-for="child in item.children"
                                :key="child.index"
                                :href="route(child.routeName)"
                                class="sidebar-flyout__link"
                                :class="{ 'is-active': child.index === activeIndex }"
                            >
                                {{ child.label }}
                            </Link>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</template>
