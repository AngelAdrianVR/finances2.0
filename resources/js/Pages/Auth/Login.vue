<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>   

<template>
    <Head title="Inicio de sesión" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

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
                    autocomplete="username"
                    :errorMessage="form.errors.email"
                    placeholder="Ingresa tu correo electrónico"
                />
                <!-- <InputError class="mt-2" :message="form.errors.email" /> -->
            </div>

            <div class="mt-4">
                <InputLabel class="ml-3" for="password" value="Contraseña" />
                <div class="relative">
                    <TextInput
                        id="password"
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        :errorMessage="form.errors?.email"
                        class="mt-1 block w-full"
                        required
                        autocomplete="current-password"
                        placeholder="Contraseña"
                    />
                    <button @click="showPassword = !showPassword" type="button" class="z-10 absolute top-2 right-2">
                        <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-4 text-gray-500">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-4 text-gray-500">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                <!-- <InputError class="mt-2" :message="form.errors.password" /> -->
            </div>

            <div class="flex justify-between mt-3 ml-2">
                <el-checkbox class="!text-gray-500" v-model="form.remember" name="remember" label="Mantener sesión abierta" size="small" />
                <Link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm text-primary hover:text-secondary rounded-md">
                    ¿Olvidaste tu contraseña?
                </Link>
            </div>

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton class="mx-auto px-28 md:px-48 lg:px-52 mt-3 flex space-x-2 items-center" :disabled="form.processing">
                    <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                    Ingresar
                </PrimaryButton>
            </div>
            
            <div class="border-t border-[#999999] mt-9 w-full relative">
                <p class="bg-gray-100 px-4 absolute -bottom-3 right-[45%]">ó</p>
            </div>

            <!-- Logueo por google o apple -->
            <div class="mt-7 flex items-center space-x-3">
                <button type="button" class="flex items-center justify-center space-x-2 p-4 w-1/2 border border-grayD9 rounded-md">
                    <img src="@/../../public/images/google_icon.png" alt="">
                    <span>Continuar con Google</span>
                </button>
                <button type="button" class="flex items-center justify-center space-x-2 py-4 px-5 w-1/2 border border-grayD9 rounded-md">
                    <img src="@/../../public/images/apple_icon.png" alt="">
                    <span>Continuar con Apple</span>
                </button>
            </div>

            <p class="mt-12 text-center">¿No tienes cuenta? <strong @click="$inertia.get(route('register'))" class="text-primary cursor-pointer hover:underline ml-2">Regístrate</strong></p>
        </form>

        <div class="mt-24 text-sm lg:flex justify-between">
            <p>copyright 2024 Finanzas. Todos los derechos reservados.</p>
            <p><a class="text-primary hover:underline cursor-pointer" href="">Política de privacidad</a> • <a class="text-primary hover:underline cursor-pointer" href="">Términos y condiciones</a></p>
        </div>
    </AuthenticationCard>
</template>
