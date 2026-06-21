<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    size: { type: String, default: 'default' },
    showActions: { type: Boolean, default: true },
    dark: { type: Boolean, default: false },
});

const page = usePage();
const user = computed(() => page.props.auth?.user || {});

const totalGlobal = computed(() => {
    const money = parseFloat(user.value?.total_money || 0);
    const loan = parseFloat(user.value?.total_loan || 0);
    return money + loan;
});

function formatMoney(val) {
    return val?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function logout() {
    router.post(route('logout'));
}
</script>

<template>
    <div class="text-sm">
        <!-- User info -->
        <div class="flex items-center space-x-3 mb-3">
            <img
                class="size-12 rounded-full object-cover"
                :src="$page.props.auth.user.profile_photo_url"
                :alt="$page.props.auth.user.name"
            />
            <div>
                <p :class="dark ? 'text-white font-medium' : 'font-semibold text-gray-800 dark:text-gray-800'">
                    {{ $page.props.auth.user.name }}
                </p>
                <p :class="dark ? 'text-[#9CACAC]' : 'text-xs text-gray-500 dark:text-gray-500'">
                    {{ $page.props.auth.user.email }}
                </p>
            </div>
        </div>

        <!-- Financial summary -->
        <div v-if="!dark" class="space-y-1.5 text-xs text-gray-600 border-t border-gray-100 dark:border-gray-200 pt-3">
            <div class="flex justify-between">
                <span class="text-gray-500 dark:text-gray-400">Disponible</span>
                <span class="font-mono font-semibold" style="color: #2F9E5B;">
                    ${{ formatMoney($page.props.auth.user?.total_money) }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500 dark:text-gray-400">Prestado</span>
                <span class="font-mono font-semibold" style="color: #D99A2B;">
                    ${{ formatMoney($page.props.auth.user?.total_loan) }}
                </span>
            </div>
            <div class="flex justify-between border-t border-gray-100 dark:border-gray-200 pt-1.5 mt-1.5">
                <span class="text-gray-700 dark:text-gray-300 font-medium">Total</span>
                <span class="font-mono font-semibold text-gray-900 dark:text-gray-100">${{ formatMoney(totalGlobal) }}</span>
            </div>
        </div>

        <!-- Financial summary (dark mode) -->
        <div v-else class="space-y-1.5 text-xs text-[#9CACAC]">
            <div class="flex justify-between">
                <span class="text-[#9CACAC]">Disponible</span>
                <span class="font-mono font-semibold" style="color: #2F9E5B;">
                    ${{ formatMoney($page.props.auth.user?.total_money) }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-[#9CACAC]">Prestado</span>
                <span class="font-mono font-semibold" style="color: #D99A2B;">
                    ${{ formatMoney($page.props.auth.user?.total_loan) }}
                </span>
            </div>
            <div class="flex justify-between border-t border-[#1A3A3B] pt-1.5 mt-1.5">
                <span class="text-gray-200 font-medium">Total</span>
                <span class="font-mono font-semibold text-white">${{ formatMoney(totalGlobal) }}</span>
            </div>
        </div>

        <!-- Actions -->
        <div v-if="showActions" class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-200 space-y-1">
            <Link :href="route('profile.show')"
                class="block text-sm text-gray-700 dark:text-gray-300 hover:text-primary rounded px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-100 transition-colors">
                Editar perfil
            </Link>
            <button @click="logout"
                class="w-full text-left text-sm text-red-500 hover:text-red-600 rounded px-2 py-1.5 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                Cerrar sesion
            </button>
        </div>
    </div>
</template>
