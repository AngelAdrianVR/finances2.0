<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Reset Password" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <form @submit.prevent="submit">
            <div>
                <InputLabel class="ml-3" for="email" value="Correo electrónico" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    placeholder="Ingresa tu correo electrónico"
                    autocomplete="username"
                    :errorMessage="form.errors.email"
                />
                <!-- <InputError class="mt-2" :message="form.errors.email" /> -->
            </div>

            <div class="mt-4">
                <InputLabel class="ml-3" for="password" value="Contraseña" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                    placeholder="Ingresa tu contraseña"
                    :errorMessage="form.errors.password"
                />
                <!-- <InputError class="mt-2" :message="form.errors.password" /> -->
            </div>

            <div class="mt-4">
                <InputLabel class="ml-3" for="password_confirmation" value="Confirmar contraseña" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                    placeholder="Confirma tu contraseña"
                    :errorMessage="form.errors.password_confirmation"
                />
                <!-- <InputError class="mt-2" :message="form.errors.password_confirmation" /> -->
            </div>

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton :disabled="form.processing">
                    <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                    Reestablecer contraseña
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
