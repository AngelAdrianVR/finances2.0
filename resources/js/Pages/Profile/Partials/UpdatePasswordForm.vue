<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import ProfileSection from '@/Components/MyComponents/ProfileSection.vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('user-password.update'), {
        errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }

            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <ProfileSection
        title="Actualizar contraseña"
        description="Usa una contraseña larga y única para mantener tu cuenta protegida."
    >
        <form class="profile-form" @submit.prevent="updatePassword">
            <div class="profile-form__row">
                <label class="profile-label" for="current_password">Contraseña actual</label>
                <el-input
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    size="large"
                    show-password
                    autocomplete="current-password"
                />
                <p v-if="form.errors.current_password" class="profile-error">{{ form.errors.current_password }}</p>
            </div>

            <div class="profile-form__row">
                <label class="profile-label" for="password">Nueva contraseña</label>
                <el-input
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    size="large"
                    show-password
                    autocomplete="new-password"
                />
                <p v-if="form.errors.password" class="profile-error">{{ form.errors.password }}</p>
            </div>

            <div class="profile-form__row">
                <label class="profile-label" for="password_confirmation">Confirmar contraseña</label>
                <el-input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    size="large"
                    show-password
                    autocomplete="new-password"
                />
                <p v-if="form.errors.password_confirmation" class="profile-error">
                    {{ form.errors.password_confirmation }}
                </p>
            </div>
        </form>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="profile-saved">Guardado.</ActionMessage>
            <el-button type="primary" :loading="form.processing" @click="updatePassword">
                Guardar contraseña
            </el-button>
        </template>
    </ProfileSection>
</template>
