<script setup>
import { computed } from 'vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

const props = defineProps({
    user: { type: Object, required: true },
});

const total = computed(() => Number(props.user?.total_money || 0) + Number(props.user?.total_loan || 0));

const isVerified = computed(() => Boolean(props.user?.email_verified_at));

const memberSince = computed(() => {
    if (!props.user?.created_at) return null;

    try {
        return format(new Date(props.user.created_at), 'MMMM yyyy', { locale: es });
    } catch {
        return null;
    }
});

function formatMoney(value) {
    return Number(value || 0)
        .toFixed(2)
        .replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}
</script>

<template>
    <div class="profile-card profile-identity">
        <div class="profile-identity__top">
            <img
                class="profile-identity__avatar"
                :src="user.profile_photo_url"
                :alt="user.name"
            />
            <div class="profile-identity__meta">
                <p class="profile-identity__name">{{ user.name }}</p>
                <p class="profile-identity__mail">{{ user.email }}</p>
            </div>
        </div>

        <div class="profile-identity__badges">
            <span class="profile-badge" :class="isVerified ? 'profile-badge--success' : 'profile-badge--warning'">
                {{ isVerified ? 'Correo verificado' : 'Correo sin verificar' }}
            </span>
            <span v-if="memberSince" class="profile-badge profile-badge--neutral">
                Miembro desde {{ memberSince }}
            </span>
        </div>

        <div class="profile-identity__stats">
            <div class="profile-stat">
                <span class="profile-stat__label">
                    <span class="profile-stat__dot profile-stat__dot--success" aria-hidden="true" />
                    Disponible
                </span>
                <span class="profile-stat__value profile-stat__value--success">${{ formatMoney(user.total_money) }}</span>
            </div>

            <div class="profile-stat">
                <span class="profile-stat__label">
                    <span class="profile-stat__dot profile-stat__dot--warning" aria-hidden="true" />
                    Prestado
                </span>
                <span class="profile-stat__value profile-stat__value--warning">${{ formatMoney(user.total_loan) }}</span>
            </div>

            <div class="profile-stat profile-stat--total">
                <span class="profile-stat__label">Total</span>
                <span class="profile-stat__value">${{ formatMoney(total) }}</span>
            </div>
        </div>
    </div>
</template>
