<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import {
    HomeFilled, Money, Coin, Setting, ArrowLeftBold, ArrowRightBold,
} from '@element-plus/icons-vue';
import UserProfileCard from '@/Layouts/Partials/UserProfileCard.vue';

// Props & emits
const props = defineProps({
    mobileOpen: { type: Boolean, default: false },
});
const emit = defineEmits(['update:mobileOpen']);

// Sidebar collapsed state (persisted)
const collapsed = ref(true);
onMounted(() => {
    const stored = localStorage.getItem('app_menu_collapsed');
    if (stored !== null) collapsed.value = JSON.parse(stored);
});

function toggleCollapsed() {
    collapsed.value = !collapsed.value;
    localStorage.setItem('app_menu_collapsed', collapsed.value);
}

// ==========================================
// SINGLE MENU DEFINITION (source of truth)
// ==========================================
const menuItems = [
    {
        index: 'dashboard',
        label: 'Inicio',
        iconComponent: HomeFilled,
        routeName: 'dashboard',
        children: [],
    },
    {
        index: 'transactions',
        label: 'Transacciones',
        iconComponent: Money,
        children: [
            { index: 'incomes', label: 'Ingresos', routeName: 'incomes.index' },
            { index: 'outcomes', label: 'Gastos', routeName: 'outcomes.index' },
        ],
    },
    {
        index: 'loans',
        label: 'Préstamos',
        iconComponent: Coin,
        routeName: 'loans.index',
        children: [],
    },
    {
        index: 'settings',
        label: 'Configuraciones',
        iconComponent: Setting,
        routeName: 'settings.index',
        children: [],
    },
];

// Active menu detection
const activeMenuIndex = computed(() => {
    const current = route().current();
    // Direct match
    if (route().current('dashboard')) return 'dashboard';
    if (route().current('loans.*')) return 'loans';
    if (route().current('settings.*')) return 'settings';
    if (route().current('incomes.*')) return 'incomes';
    if (route().current('outcomes.*')) return 'outcomes';
    return '';
});

// Navigation handler
function navigate(indexPath) {
    const findItem = (items, idx) => {
        for (const item of items) {
            if (item.index === idx) return item;
            if (item.children) {
                for (const child of item.children) {
                    if (child.index === idx) return child;
                }
            }
        }
        return null;
    };
    const item = findItem(menuItems, indexPath);
    if (item && item.routeName) {
        router.get(route(item.routeName));
    }
    emit('update:mobileOpen', false);
}

// Mobile drawer v-model
const drawerVisible = computed({
    get: () => props.mobileOpen,
    set: (val) => emit('update:mobileOpen', val),
});
</script>

<template>
    <!-- ==================== DESKTOP SIDEBAR ==================== -->
    <aside
        class="hidden md:flex flex-col h-screen shrink-0 transition-all duration-300 ease-in-out"
        :class="collapsed ? 'w-16' : 'w-60'"
        style="background-color: #0F2728;"
    >
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 shrink-0">
            <Link :href="route('dashboard')">
                <img v-if="collapsed" src="@/../../public/images/isologo.png" alt="Logo" class="h-10 w-auto" />
                <img v-else src="@/../../public/images/white_logo.png" alt="Logo" class="h-8 w-auto px-3" />
            </Link>
        </div>

        <!-- User avatar + profile popover (Element Plus) -->
        <div class="px-3 mb-5 flex justify-center">
            <el-popover
                v-if="$page.props.jetstream.managesProfilePhotos"
                placement="right-start"
                :width="240"
                trigger="click"
                :offset="8"
                popper-class="profile-popover"
            >
                <template #reference>
                    <button
                        class="menu-profile-btn flex items-center rounded-full transition-all duration-200"
                        :class="collapsed ? 'size-9 justify-center' : 'w-full px-2 py-1.5 space-x-3 rounded-lg'"
                    >
                        <img
                            class="size-8 rounded-full object-cover shrink-0"
                            :src="$page.props.auth.user.profile_photo_url"
                            :alt="$page.props.auth.user.name"
                        />
                        <span v-if="!collapsed" class="text-sm text-gray-300 truncate">
                            {{ $page.props.auth.user.name }}
                        </span>
                        <el-icon v-if="!collapsed" class="text-gray-500 shrink-0" :size="12">
                            <ArrowRightBold />
                        </el-icon>
                    </button>
                </template>

                <!-- Popover content: user card -->
                <UserProfileCard />
            </el-popover>
        </div>

        <!-- Navigation (Element Plus ElMenu) -->
        <el-menu
            :collapse="collapsed"
            :default-active="activeMenuIndex"
            :collapse-transition="false"
            class="app-menu flex-1 overflow-y-auto overflow-x-hidden px-0"
            background-color="transparent"
            text-color="#9CACAC"
            active-text-color="#FFFFFF"
            @select="navigate"
        >
            <template v-for="item in menuItems" :key="item.index">
                <!-- Sub-menu -->
                <el-sub-menu v-if="item.children && item.children.length" :index="item.index">
                    <template #title>
                        <el-icon :size="20">
                            <component :is="item.iconComponent" />
                        </el-icon>
                        <span>{{ item.label }}</span>
                    </template>
                    <el-menu-item
                        v-for="child in item.children"
                        :key="child.index"
                        :index="child.index"
                    >
                        {{ child.label }}
                    </el-menu-item>
                </el-sub-menu>

                <!-- Simple item -->
                <el-menu-item v-else :index="item.index">
                    <el-icon :size="20">
                        <component :is="item.iconComponent" />
                    </el-icon>
                    <span>{{ item.label }}</span>
                </el-menu-item>
            </template>
        </el-menu>

        <!-- Collapse toggle -->
        <div class="p-2 border-t border-[#1A3A3B]">
            <button @click="toggleCollapsed" class="menu-collapse-btn">
                <el-icon :size="20">
                    <ArrowRightBold v-if="collapsed" />
                    <ArrowLeftBold v-else />
                </el-icon>
            </button>
        </div>
    </aside>

    <!-- ==================== MOBILE DRAWER ==================== -->
    <el-drawer
        v-model="drawerVisible"
        direction="ltr"
        size="280"
        :with-header="false"
        class="mobile-drawer"
        :z-index="2000"
    >
        <!-- Drawer header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#1A3A3B]" style="background-color: #0F2728;">
            <div class="flex items-center space-x-3">
                <img src="@/../../public/images/isologo.png" alt="Logo" class="h-8 w-auto" />
                <span class="text-white font-semibold text-sm">Menú</span>
            </div>
        </div>

        <!-- User info in drawer -->
        <div class="px-5 py-4 border-b border-[#1A3A3B]" style="background-color: #0F2728;">
            <UserProfileCard :show-actions="false" dark />
        </div>

        <!-- Menu in drawer -->
        <el-menu
            :default-active="activeMenuIndex"
            class="app-menu flex-1"
            background-color="transparent"
            text-color="#9CACAC"
            active-text-color="#FFFFFF"
            @select="navigate"
        >
            <template v-for="item in menuItems" :key="item.index">
                <el-sub-menu v-if="item.children && item.children.length" :index="item.index">
                    <template #title>
                        <el-icon :size="20">
                            <component :is="item.iconComponent" />
                        </el-icon>
                        <span>{{ item.label }}</span>
                    </template>
                    <el-menu-item v-for="child in item.children" :key="child.index" :index="child.index">
                        {{ child.label }}
                    </el-menu-item>
                </el-sub-menu>
                <el-menu-item v-else :index="item.index">
                    <el-icon :size="20">
                        <component :is="item.iconComponent" />
                    </el-icon>
                    <span>{{ item.label }}</span>
                </el-menu-item>
            </template>
        </el-menu>

        <!-- Logout -->
        <div class="px-5 py-4 border-t border-[#1A3A3B]" style="background-color: #0F2728;">
            <Link :href="route('profile.show')"
                class="flex items-center space-x-2 text-sm text-[#9CACAC] hover:text-white py-2 transition-colors">
                <el-icon :size="16"><Setting /></el-icon>
                <span>Editar perfil</span>
            </Link>
            <button @click="logout"
                class="flex items-center space-x-2 text-sm text-red-400 hover:text-red-300 py-2 transition-colors w-full">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
                <span>Cerrar sesión</span>
            </button>
        </div>
    </el-drawer>
</template>
