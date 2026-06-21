<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { ArrowLeftBold, ArrowRightBold, MoreFilled } from '@element-plus/icons-vue';

const props = defineProps({
    pagination: Object,
});

const showPagination = computed(() =>
    props.pagination?.links?.length > 3
);

// Detect prev/next links by looking for chevron in label
function isPrev(label) {
    return typeof label === 'string' && (label.includes('&laquo;') || label.toLowerCase().includes('previous') || label.includes('Anterior'));
}

function isNext(label) {
    return typeof label === 'string' && (label.includes('&raquo;') || label.toLowerCase().includes('next') || label.includes('Siguiente'));
}

// Decode HTML entities for clean display
function formatLabel(label) {
    if (!label) return '';
    const txt = document.createElement('textarea');
    txt.innerHTML = label;
    return txt.value || label;
}
</script>

<template>
    <div v-if="showPagination" class="flex items-center justify-center gap-1 py-3">
        <template v-for="(link, key) in pagination.links" :key="key">
            <!-- Prev nav arrow -->
            <component
                :is="link.url ? Link : 'span'"
                v-if="isPrev(link.label)"
                :href="link.url || undefined"
                class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-150"
                :class="link.url
                    ? 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer'
                    : 'text-gray-300 dark:text-gray-600 cursor-not-allowed'"
            >
                <el-icon :size="16">
                    <ArrowLeftBold />
                </el-icon>
            </component>

            <!-- Next nav arrow -->
            <component
                :is="link.url ? Link : 'span'"
                v-else-if="isNext(link.label)"
                :href="link.url || undefined"
                class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-150"
                :class="link.url
                    ? 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer'
                    : 'text-gray-300 dark:text-gray-600 cursor-not-allowed'"
            >
                <el-icon :size="16">
                    <ArrowRightBold />
                </el-icon>
            </component>

            <!-- Ellipsis -->
            <span
                v-else-if="!link.url"
                class="inline-flex items-center justify-center w-8 h-8 text-gray-400 dark:text-gray-500"
            >
                <el-icon :size="14"><MoreFilled /></el-icon>
            </span>

            <!-- Numeric page button -->
            <Link
                v-else
                :href="link.url"
                class="inline-flex items-center justify-center min-w-[32px] h-8 px-2 rounded-lg text-sm font-medium transition-all duration-150"
                :class="link.active
                    ? 'bg-primary text-white shadow-sm'
                    : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-800 dark:hover:text-gray-200'"
                v-text="formatLabel(link.label)"
            />
        </template>
    </div>
</template>