<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ProfileSection from '@/Components/MyComponents/ProfileSection.vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    setTimeout(() => passwordInput.value?.focus(), 250);
};

const deleteUser = () => {
    form.delete(route('current-user.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>
    <ProfileSection
        title="Eliminar cuenta"
        description="Elimina permanentemente tu cuenta y todos los datos asociados."
        tone="danger"
    >
        <p class="profile-text">
            Una vez que se elimine tu cuenta, todos sus recursos y datos se eliminarán de forma permanente.
            Antes de eliminar tu cuenta, descarga cualquier dato o información que desees conservar.
        </p>

        <template #actions>
            <el-button type="danger" @click="confirmUserDeletion">Eliminar cuenta</el-button>
        </template>

        <el-dialog
            v-model="confirmingUserDeletion"
            title="Eliminar cuenta"
            width="460px"
            append-to-body
            :close-on-click-modal="false"
        >
            <div class="profile-form">
                <p class="profile-text">
                    ¿Estás seguro de que deseas eliminar tu cuenta? Esta acción es permanente y eliminará todos
                    tus recursos y datos. Ingresa tu contraseña para confirmar.
                </p>
                <div class="profile-form__row">
                    <label class="profile-label" for="delete-password">Contraseña</label>
                    <el-input
                        id="delete-password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        size="large"
                        show-password
                        autocomplete="current-password"
                        @keyup.enter="deleteUser"
                    />
                    <p v-if="form.errors.password" class="profile-error">{{ form.errors.password }}</p>
                </div>
            </div>

            <template #footer>
                <el-button @click="closeModal">Cancelar</el-button>
                <el-button type="danger" :loading="form.processing" @click="deleteUser">
                    Eliminar cuenta
                </el-button>
            </template>
        </el-dialog>
    </ProfileSection>
</template>
