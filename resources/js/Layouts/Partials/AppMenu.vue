<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { ArrowRightBold } from '@element-plus/icons-vue';
import MenuIcon from '@/Layouts/Partials/MenuIcon.vue';
import SidebarNav from '@/Layouts/Partials/SidebarNav.vue';
import UserProfileCard from '@/Layouts/Partials/UserProfileCard.vue';

const props = defineProps({
    mobileOpen: { type: Boolean, default: false },
});
const emit = defineEmits(['update:mobileOpen']);

const collapsed = ref(false);

onMounted(() => {
    const stored = localStorage.getItem('app_menu_collapsed');
    if (stored !== null) collapsed.value = JSON.parse(stored);
});

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

        <!-- Navigation -->
        <div class="app-sidebar__nav">
            <SidebarNav
                :sections="menuSections"
                :active-index="activeMenuIndex"
                :collapsed="collapsed"
            />
        </div>

        <!-- Footer -->
        <div class="app-sidebar__footer">
            <el-popover
                v-if="$page.props.jetstream.managesProfilePhotos"
                placement="right-end"
                :width="248"
                trigger="click"
                :offset="10"
                popper-class="sidebar-profile-popover"
            >
                <template #reference>
                    <button
                        type="button"
                        class="app-sidebar__profile"
                        :class="{ 'is-compact': collapsed }"
                    >
                        <img
                            class="app-sidebar__avatar"
                            :src="$page.props.auth.user.profile_photo_url"
                            :alt="$page.props.auth.user.name"
                        />
                        <span v-if="!collapsed" class="app-sidebar__profile-text">
                            <span class="app-sidebar__profile-name">{{ $page.props.auth.user.name }}</span>
                            <span class="app-sidebar__profile-mail">{{ $page.props.auth.user.email }}</span>
                        </span>
                        <el-icon v-if="!collapsed" class="app-sidebar__profile-arrow" :size="12">
                            <ArrowRightBold />
                        </el-icon>
                    </button>
                </template>

                <UserProfileCard />
            </el-popover>

            <div class="app-sidebar__actions" :class="{ 'is-stacked': collapsed }">
                <button
                    v-if="collapsed"
                    type="button"
                    class="app-sidebar__icon-btn"
                    title="Expandir menú"
                    @click="toggleCollapsed"
                >
                    <MenuIcon name="collapse" :size="18" />
                </button>

                <button
                    type="button"
                    class="app-sidebar__icon-btn app-sidebar__icon-btn--danger"
                    title="Cerrar sesión"
                    @click="logout"
                >
                    <MenuIcon name="logout" :size="18" />
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
                <UserProfileCard :show-actions="false" dark />
            </div>

            <div class="sidebar-drawer__nav">
                <SidebarNav
                    :sections="menuSections"
                    :active-index="activeMenuIndex"
                    :collapsed="false"
                />
            </div>

            <div class="sidebar-drawer__footer">
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
