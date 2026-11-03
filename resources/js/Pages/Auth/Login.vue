<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { Lock, Message } from '@element-plus/icons-vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

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
    <AuthLayout title="Inicio de sesión">
        <header>
            <h2 class="auth-heading">Bienvenido de nuevo</h2>
            <p class="auth-subheading">
                Ingresa tus credenciales para retomar el control de tus finanzas.
            </p>
        </header>

        <el-alert
            v-if="status"
            class="auth-alert"
            :title="status"
            type="success"
            :closable="false"
            show-icon
        />

        <el-form class="auth-form mt-6" :model="form" label-position="top" @submit.prevent="submit">
            <el-form-item label="Correo electrónico" :error="form.errors.email">
                <el-input
                    id="email"
                    v-model="form.email"
                    type="email"
                    size="large"
                    placeholder="tu@correo.com"
                    autocomplete="username"
                    :prefix-icon="Message"
                    autofocus
                />
            </el-form-item>

            <el-form-item label="Contraseña" :error="form.errors.password">
                <el-input
                    id="password"
                    v-model="form.password"
                    type="password"
                    size="large"
                    placeholder="Ingresa tu contraseña"
                    autocomplete="current-password"
                    :prefix-icon="Lock"
                    show-password
                />
            </el-form-item>

            <div class="auth-row">
                <el-checkbox v-model="form.remember" name="remember">Mantener sesión abierta</el-checkbox>

                <Link v-if="canResetPassword" :href="route('password.request')" class="auth-link">
                    ¿Olvidaste tu contraseña?
                </Link>
            </div>

            <el-button
                type="primary"
                native-type="submit"
                class="auth-submit"
                :loading="form.processing"
            >
                Ingresar
            </el-button>
        </el-form>

        <p class="auth-switch">
            ¿No tienes una cuenta?
            <Link :href="route('register')" class="auth-link auth-link--strong">Crea una cuenta</Link>
        </p>
    </AuthLayout>
</template>