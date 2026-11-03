<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { Moon, Sunny } from '@element-plus/icons-vue';
import AuthShowcase from '@/Components/Auth/AuthShowcase.vue';
import { useDarkMode } from '@/composables/useDarkMode';

defineProps({
    title: {
        type: String,
        required: true,
    },
});

const { isDark, toggleDark } = useDarkMode();

const logoSrc = computed(() => (isDark.value ? '/images/white_logo.png' : '/images/color_logo.png'));
const toggleLabel = computed(() => (isDark.value ? 'Modo claro' : 'Modo oscuro'));
const currentYear = new Date().getFullYear();
</script>

<template>
    <Head :title="title" />

    <div class="auth-shell">
        <aside class="auth-showcase">
            <AuthShowcase />
        </aside>

        <main class="auth-panel">
            <div class="auth-panel__bar">
                <img class="auth-panel__logo" :src="logoSrc" alt="Finanzas" />

                <el-tooltip :content="toggleLabel" placement="bottom" :show-after="300">
                    <button
                        type="button"
                        class="auth-theme-toggle"
                        :aria-label="toggleLabel"
                        @click="toggleDark"
                    >
                        <el-icon :size="18">
                            <Sunny v-if="isDark" />
                            <Moon v-else />
                        </el-icon>
                    </button>
                </el-tooltip>
            </div>

            <div class="auth-panel__body">
                <div class="auth-card">
                    <slot />
                </div>

                <p class="auth-legal">
                    © {{ currentYear }} Finanzas. Todos los derechos reservados.
                    <br />
                    <Link :href="route('legal.privacy')">Política de privacidad</Link>
                    &nbsp;•&nbsp;
                    <Link :href="route('legal.terms')">Términos</Link>
                </p>
            </div>
        </main>
    </div>
</template>
