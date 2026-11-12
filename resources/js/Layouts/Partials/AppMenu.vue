<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { ArrowDown, TopRight } from '@element-plus/icons-vue';
import MenuIcon from '@/Layouts/Partials/MenuIcon.vue';
import SidebarNav from '@/Layouts/Partials/SidebarNav.vue';
import UserProfileCard from '@/Layouts/Partials/UserProfileCard.vue';

const props = defineProps({
    mobileOpen: { type: Boolean, default: false },
});
const emit = defineEmits(['update:mobileOpen']);

function readCollapsedState() {
    try {
        return JSON.parse(localStorage.getItem('app_menu_collapsed')) === true;
    } catch {
        return false;
    }
}

const collapsed = ref(readCollapsedState());

function toggleCollapsed() {
    collapsed.value = !collapsed.value;
    localStorage.setItem('app_menu_collapsed', collapsed.value);
}

function logout() {
    router.post(route('logout'));
}

// ==========================================
// SINGLE MENU DEFINITION (source of truth)
// ==========================================
const menuSections = [
    {
        label: 'Principal',
        items: [
            { index: 'dashboard', label: 'Inicio', icon: 'dashboard', routeName: 'dashboard', children: [] },
        ],
    },
    {
        label: 'Gestión',
        items: [
            {
                index: 'transactions',
                label: 'Transacciones',
                icon: 'transactions',
                children: [
                    { index: 'incomes', label: 'Ingresos', routeName: 'incomes.index' },
                    { index: 'outcomes', label: 'Gastos', routeName: 'outcomes.index' },
                ],
            },
            { index: 'loans', label: 'Préstamos', icon: 'loans', routeName: 'loans.index', children: [] },
            { index: 'settings', label: 'Configuraciones', icon: 'settings', routeName: 'settings.index', children: [] },
        ],
    },
];

const activeMenuIndex = computed(() => {
    if (route().current('dashboard')) return 'dashboard';
    if (route().current('loans.*')) return 'loans';
    if (route().current('settings.*')) return 'settings';
    if (route().current('incomes.*')) return 'incomes';
    if (route().current('outcomes.*')) return 'outcomes';
    return '';
});

const drawerVisible = computed({
    get: () => props.mobileOpen,
    set: (val) => emit('update:mobileOpen', val),
});
</script>

<template>
    <!-- ==================== DESKTOP SIDEBAR ==================== -->
    <aside class="app-sidebar" :class="collapsed ? 'is-collapsed' : 'is-expanded'">
        <!-- Brand -->
        <div class="app-sidebar__brand">
            <Link :href="route('dashboard')" class="app-sidebar__brand-link">
                <img
                    v-if="collapsed"
                    src="@/../../public/images/isologo.png"
                    alt="Logo"
                    class="h-9 w-auto"
                />
                <img
                    v-else
                    src="@/../../public/images/white_logo.png"
                    alt="Logo"
                    class="h-7 w-auto"
                />
            </Link>

            <button
                v-if="!collapsed"
                type="button"
                class="app-sidebar__icon-btn"
                title="Contraer menú"
                @click="toggleCollapsed"
            >
                <MenuIcon name="collapse" :size="18" />
            </button>
        </div>

        <!-- User summary (opens the financial card on click) -->
        <div class="app-sidebar__summary">
            <el-popover
                placement="right-start"
                :width="264"
                trigger="click"
                :offset="12"
                popper-class="sidebar-profile-popover"
            >
                <template #reference>
                    <button
                        type="button"
                        class="app-sidebar__identity-btn"
                        :class="{ 'is-compact': collapsed }"
                        title="Mi resumen financiero"
                    >
                        <img
                            class="app-sidebar__avatar"
                            :src="$page.props.auth.user.profile_photo_url"
                            :alt="$page.props.auth.user.name"
                        />
                        <span v-if="!collapsed" class="app-sidebar__identity-text">
                            <span class="app-sidebar__identity-name">{{ $page.props.auth.user.name }}</span>
                            <span class="app-sidebar__identity-mail">{{ $page.props.auth.user.email }}</span>
                        </span>
                        <el-icon v-if="!collapsed" class="app-sidebar__identity-chevron" :size="12">
                            <ArrowDown />
                        </el-icon>
                    </button>
                </template>

                <UserProfileCard />
            </el-popover>
        </div>

        <!-- Navigation -->
        <div class="app-sidebar__nav">
            <SidebarNav
                :sections="menuSections"
                :active-index="activeMenuIndex"
                :collapsed="collapsed"
            />
        </div>

        <!-- Footer (only needed while collapsed) -->
        <div v-if="collapsed" class="app-sidebar__footer">
            <div class="app-sidebar__actions is-stacked">
                <button
                    type="button"
                    class="app-sidebar__icon-btn"
                    title="Expandir menú"
                    @click="toggleCollapsed"
                >
                    <MenuIcon name="collapse" :size="18" />
                </button>
            </div>
        </div>
    </aside>

    <!-- ==================== MOBILE DRAWER ==================== -->
    <el-drawer
        v-model="drawerVisible"
        direction="ltr"
        size="288"
        :with-header="false"
        class="sidebar-drawer"
        :z-index="2000"
    >
        <div class="sidebar-drawer__inner">
            <div class="sidebar-drawer__brand">
                <img src="@/../../public/images/white_logo.png" alt="Logo" class="h-7 w-auto" />
            </div>

            <div class="sidebar-drawer__profile">
                <el-popover
                    placement="bottom-start"
                    :width="264"
                    trigger="click"
                    :offset="10"
                    popper-class="sidebar-profile-popover"
                >
                    <template #reference>
                        <button type="button" class="app-sidebar__identity-btn" title="Mi resumen financiero">
                            <img
                                class="app-sidebar__avatar"
                                :src="$page.props.auth.user.profile_photo_url"
                                :alt="$page.props.auth.user.name"
                            />
                            <span class="app-sidebar__identity-text">
                                <span class="app-sidebar__identity-name">{{ $page.props.auth.user.name }}</span>
                                <span class="app-sidebar__identity-mail">{{ $page.props.auth.user.email }}</span>
                            </span>
                            <el-icon class="app-sidebar__identity-chevron" :size="12">
                                <ArrowDown />
                            </el-icon>
                        </button>
                    </template>

                    <UserProfileCard />
                </el-popover>
            </div>

            <div class="sidebar-drawer__nav">
                <SidebarNav
                    :sections="menuSections"
                    :active-index="activeMenuIndex"
                    :collapsed="false"
                />
            </div>

            <div class="sidebar-drawer__footer">
                <a
                    href="https://ezyventas.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="sidebar-drawer__promo"
                >
                    <span class="sidebar-drawer__promo-mark">
                        <img src="/images/ezyventas-isologo.png" alt="EzyVentas" />
                    </span>
                    <span class="sidebar-drawer__promo-copy">
                        <span class="sidebar-drawer__promo-name">EzyVentas</span>
                        <span class="sidebar-drawer__promo-tagline">Punto de venta para tu negocio</span>
                    </span>
                    <el-icon class="sidebar-drawer__promo-arrow" :size="12"><TopRight /></el-icon>
                </a>

                <Link :href="route('profile.show')" class="sidebar-drawer__link">
                    <MenuIcon name="settings" :size="16" />
                    <span>Editar perfil</span>
                </Link>
                <button type="button" class="sidebar-drawer__link sidebar-drawer__link--danger" @click="logout">
                    <MenuIcon name="logout" :size="16" />
                    <span>Cerrar sesión</span>
                </button>
            </div>
        </div>
    </el-drawer>
</template>
