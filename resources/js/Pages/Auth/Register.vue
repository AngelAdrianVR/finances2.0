<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<style>
    /* From Uiverse.io by SelfMadeSystem */ 
.container {
  cursor: pointer;
}

.container input {
  display: none;
}

.container svg {
  overflow: visible;
}

.path {
  fill: none;
  stroke: rgb(34, 146, 0);
  stroke-width: 4;
  stroke-linecap: round;
  stroke-linejoin: round;
  transition: stroke-dasharray 0.5s ease, stroke-dashoffset 0.5s ease;
  stroke-dasharray: 241 9999999;
  stroke-dashoffset: 0;
}

.container input:checked ~ svg .path {
  stroke-dasharray: 70.5096664428711 9999999;
  stroke-dashoffset: -262.2723388671875;
}
</style>

<template>
    <Head title="Registro" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Nombre" class="ml-3" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="name"
                    :errorMessage="form.errors.name"
                    placeholder="Ingresa tu nombre"
                />
                <!-- <InputError class="mt-2" :message="form.errors.name" /> -->
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Correo electrónico" class="ml-3" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autocomplete="username"
                    :errorMessage="form.errors.email"
                    placeholder="Ingresa tu correo electrónico"
                />
                <!-- <InputError class="mt-2" :message="form.errors.email" /> -->
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Contraseña" class="ml-3" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                    placeholder="Ingresa una contraseña"
                    :errorMessage="form.errors.password"
                />
                <!-- <InputError class="mt-2" :message="form.errors.password" /> -->
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirmar contraseña" class="ml-3" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                    placeholder="Ingresa a misma contraseña"
                    :errorMessage="form.errors.password_confirmation"
                />
                <!-- <InputError class="mt-2" :message="form.errors.password_confirmation" /> -->
            </div>

            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                <InputLabel for="terms" class="ml-3">
                    <div class="flex items-center">
                        <!-- <Checkbox id="terms" v-model:checked="form.terms" name="terms" required /> -->
                        <label class="container flex">
                            <input v-model="form.terms" type="checkbox">
                            <svg viewBox="0 0 64 64" height="2em" width="2em">
                                <path d="M 0 16 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 16 L 32 48 L 64 16 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 16" pathLength="575.0541381835938" class="path"></path>
                            </svg>
                            <div class="ms-2">
                                Estoy de acuerdo con <a target="_blank" :href="route('dashboard')"
                                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Términos de servicio</a> y <a target="_blank" :href="route('dashboard')" 
                                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Política de privacidad</a>
                            </div>
                        </label>
                    </div>
                    <InputError class="mt-2" :message="form.errors.terms" />
                </InputLabel>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md">
                    ¿Ya tienes una cuenta?
                </Link>

                <PrimaryButton class="ms-4" :disabled="form.processing">
                    <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                    Resgistrarse
                </PrimaryButton>
            </div>
        </form>

        <div class="mt-32 text-sm lg:flex justify-between">
            <p>copyright 2024 Finanzas. Todos los derechos reservados.</p>
            <p><a class="text-primary hover:underline cursor-pointer" href="">Política de privacidad</a> • <a class="text-primary hover:underline cursor-pointer" href="">Términos y condiciones</a></p>
        </div>
    </AuthenticationCard>
</template>
