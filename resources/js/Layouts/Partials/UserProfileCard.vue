<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { ElMessage } from 'element-plus';
import { EditPen } from '@element-plus/icons-vue';
import MenuIcon from '@/Layouts/Partials/MenuIcon.vue';

defineProps({
    showActions: { type: Boolean, default: true },
});

const page = usePage();
const user = computed(() => page.props.auth?.user || {});

const totalGlobal = computed(() => {
    const money = parseFloat(user.value?.total_money || 0);
    const loan = parseFloat(user.value?.total_loan || 0);
    return money + loan;
});

function formatMoney(val) {
    return Number(val || 0)
        .toFixed(2)
        .replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function logout() {
    router.post(route('logout'));
}

// ==========================================
// Edit available money (Disponible)
// ==========================================
const editDialogVisible = ref(false);
const savingAvailableMoney = ref(false);
const availableMoneyDraft = ref(0);

function openEditAvailableMoney() {
    availableMoneyDraft.value = Number(user.value?.total_money ?? 0);
    editDialogVisible.value = true;
}

async function saveAvailableMoney() {
    const amount = Number(availableMoneyDraft.value);
    if (!Number.isFinite(amount) || amount < 0) {
        ElMessage.warning('Ingresa un monto válido.');
        return;
    }

    savingAvailableMoney.value = true;
    try {
        const { data } = await axios.patch(route('users.update-available-money'), {
            total_money: amount,
        });

        // Keep the shared auth.user in sync so every card updates instantly.
        if (page.props.auth?.user) {
            page.props.auth.user.total_money = Number(data.total_money);
        }

        editDialogVisible.value = false;
        ElMessage.success(data.message || 'Monto disponible actualizado correctamente.');
    } catch (error) {
        const fieldError = error?.response?.data?.errors?.total_money?.[0];
        ElMessage.error(fieldError || error?.response?.data?.message || 'Error al actualizar el monto disponible.');
        console.error(error);
    } finally {
        savingAvailableMoney.value = false;
    }
}
</script>

<template>
    <div class="profile-summary">
        <!-- Identity -->
        <div class="profile-summary__head">
            <Link :href="route('profile.show')" class="profile-summary__identity" title="Ver mi perfil">
                <img
                    class="profile-summary__avatar"
                    :src="user.profile_photo_url"
                    :alt="user.name"
                />
                <span class="profile-summary__meta">
                    <span class="profile-summary__name">{{ user.name }}</span>
                    <span class="profile-summary__mail">{{ user.email }}</span>
                </span>
            </Link>
        </div>

        <!-- Financial summary -->
        <div class="profile-summary__stats">
            <div class="profile-summary__row">
                <span class="profile-summary__label">
                    <span class="profile-summary__dot profile-summary__dot--success" aria-hidden="true" />
                    Disponible
                </span>
                <span class="profile-summary__value profile-summary__value--success">
                    ${{ formatMoney(user.total_money) }}
                </span>
                <button
                    type="button"
                    class="profile-summary__icon-btn"
                    title="Editar monto disponible"
                    aria-label="Editar monto disponible"
                    @click="openEditAvailableMoney"
                >
                    <el-icon :size="12"><EditPen /></el-icon>
                </button>
            </div>

            <div class="profile-summary__row">
                <span class="profile-summary__label">
                    <span class="profile-summary__dot profile-summary__dot--warning" aria-hidden="true" />
                    Prestado
                </span>
                <span class="profile-summary__value profile-summary__value--warning">
                    ${{ formatMoney(user.total_loan) }}
                </span>
            </div>

            <div class="profile-summary__row profile-summary__row--total">
                <span class="profile-summary__label profile-summary__label--total">Total</span>
                <span class="profile-summary__value profile-summary__value--total">
                    ${{ formatMoney(totalGlobal) }}
                </span>
            </div>
        </div>

        <!-- Quick actions -->
        <div v-if="showActions" class="profile-summary__actions">
            <Link :href="route('profile.show')" class="profile-summary__action">
                <MenuIcon name="settings" :size="15" />
                <span>Editar perfil</span>
            </Link>
            <button
                type="button"
                class="profile-summary__action profile-summary__action--danger"
                @click="logout"
            >
                <MenuIcon name="logout" :size="15" />
                <span>Cerrar sesión</span>
            </button>
        </div>

        <!-- Edit available money dialog -->
        <el-dialog
            v-model="editDialogVisible"
            title="Editar monto disponible"
            width="320px"
            append-to-body
            :close-on-click-modal="false"
            destroy-on-close
        >
            <div class="profile-summary__dialog">
                <p class="profile-summary__dialog-text">
                    Ingresa el nuevo monto que tienes disponible. Solo se modificará el campo «Disponible».
                </p>
                <el-input-number
                    v-model="availableMoneyDraft"
                    :min="0"
                    :max="999999999999"
                    :precision="2"
                    controls-position="right"
                    class="!w-full font-mono"
                    placeholder="Monto disponible"
                />
                <div class="profile-summary__dialog-actions">
                    <el-button :disabled="savingAvailableMoney" @click="editDialogVisible = false">Cancelar</el-button>
                    <el-button type="primary" :loading="savingAvailableMoney" @click="saveAvailableMoney">Guardar</el-button>
                </div>
            </div>
        </el-dialog>
    </div>
</template>
