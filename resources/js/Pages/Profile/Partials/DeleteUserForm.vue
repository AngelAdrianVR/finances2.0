<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import InputError from '@/Components/InputError.vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const deleteUser = () => {
    form.delete(route('current-user.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>
    <ActionSection>
        <template #title>
            Eliminar cuenta
        </template>

        <template #description>
            Eliminar permanentemente su cuenta.
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
                Una vez que se elimine su cuenta, todos sus recursos y datos se eliminarán de forma permanente. Antes de eliminar su cuenta, descargue todos los datos o la información que desee conservar.
            </div>

            <div class="mt-5">
                <el-button type="danger" @click="confirmUserDeletion">Eliminar cuenta</el-button>
            </div>

            <el-dialog v-model="confirmingUserDeletion" title="Eliminar cuenta" width="480px">
                    ¿Está seguro de que desea eliminar su cuenta? Una vez que se elimine su cuenta, todos sus recursos y datos se eliminarán de forma permanente. Ingrese su contraseña para confirmar que desea eliminar su cuenta de forma permanente.

                 <el-form-item label="Contrasena">
                    <el-input
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        placeholder="Contrasena"
                        autocomplete="current-password"
                        @keyup.enter="deleteUser"
                    />
                    <InputError :message="form.errors.password" />
                </el-form-item>

                <template #footer>
                    <el-button @click="closeModal">Cancelar</el-button>
                    <el-button type="danger" :loading="form.processing" @click="deleteUser">Eliminar cuenta</el-button>
                </template>
            </el-dialog>
        </template>
    </ActionSection>
</template>
