<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, Moon, Sunny, Calendar, TopRight } from '@element-plus/icons-vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import NotificationsCenter from '@/Components/MyComponents/NotificationsCenter.vue';
import { useDarkMode } from '@/composables/useDarkMode';

const emit = defineEmits(['update:notifications', 'openMobileMenu']);
const { isDark, toggleDark } = useDarkMode();

const page = usePage();
const user = computed(() => page.props.auth?.user || {});
const firstName = computed(() => (user.value.name || '').split(' ')[0]);
const todayLabel = computed(() => {
    const label = format(new Date(), "EEEE d 'de' MMMM", { locale: es });

    return label.charAt(0).toUpperCase() + label.slice(1);
});

function onNotificationsUpdate(value) {
    emit('update:notifications', value);
}
</script>

<template>
    <header class="app-topbar">
        <div class="app-topbar__inner">
            <!-- Left: mobile menu + context -->
            <div class="app-topbar__left">
                <button
                    type="button"
                    class="app-topbar__icon-btn app-topbar__menu-btn"
                    title="Abrir menú"
                    @click="emit('openMobileMenu')"
                >
                    <el-icon :size="20"><Menu /></el-icon>
                </button>

                <Link :href="route('dashboard')" class="app-topbar__logo">
                    <ApplicationMark class="h-8 w-auto" />
                </Link>

                <div class="app-topbar__greeting">
                    <p class="app-topbar__greeting-hello">Hola, {{ firstName }}</p>
                    <p class="app-topbar__greeting-date">{{ todayLabel }}</p>
                </div>
            </div>

            <!-- Right: utilities -->
            <div class="app-topbar__right">
                <div class="app-topbar__utilities">
                    <NotificationsCenter @notifications="onNotificationsUpdate" />

                    <Link
                        :href="route('calendars.index')"
                        class="app-topbar__icon-btn"
                        :class="{ 'is-active': route().current('calendars.*') }"
                        title="Calendario"
                    >
                        <el-icon :size="18"><Calendar /></el-icon>
                    </Link>

                    <button
                        type="button"
                        class="app-topbar__icon-btn"
                        :title="isDark ? 'Modo claro' : 'Modo oscuro'"
                        @click="toggleDark"
                    >
                        <el-icon :size="18">
                            <Sunny v-if="isDark" />
                            <Moon v-else />
                        </el-icon>
                    </button>
                </div>

                <span class="app-topbar__divider" aria-hidden="true" />

                <a
                    href="https://ezyventas.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="app-topbar__promo"
                    title="EzyVentas · Punto de venta para tu negocio"
                    aria-label="Abrir EzyVentas, punto de venta para tu negocio"
                >
                    <span class="app-topbar__promo-mark">
                        <img src="/images/ezyventas-isologo.png" alt="EzyVentas" />
                    </span>
                    <span class="app-topbar__promo-copy">
                        <span class="app-topbar__promo-name">EzyVentas</span>
                        <span class="app-topbar__promo-tagline">Punto de venta para tu negocio</span>
                    </span>
                    <el-icon class="app-topbar__promo-arrow" :size="12"><TopRight /></el-icon>
                </a>
            </div>
        </div>
    </header>
</template>
