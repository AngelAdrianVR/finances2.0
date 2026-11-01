<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { Lock, Message, User } from '@element-plus/icons-vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';

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

<template>
    <AuthLayout title="Registro">
        <header>
            <h2 class="auth-heading">Crea tu cuenta</h2>
            <p class="auth-subheading">
                Empieza a registrar tus movimientos y descubre el panorama completo de tu dinero.
            </p>
        </header>

        <el-form class="auth-form mt-6" :model="form" label-position="top" @submit.prevent="submit">
            <el-form-item label="Nombre" :error="form.errors.name">
                <el-input
                    id="name"
                    v-model="form.name"
                    type="text"
                    size="large"
                    placeholder="Ingresa tu nombre"
                    autocomplete="name"
                    :prefix-icon="User"
                    autofocus
                />
            </el-form-item>

            <el-form-item label="Correo electrónico" :error="form.errors.email">
                <el-input
                    id="email"
                    v-model="form.email"
                    type="email"
                    size="large"
                    placeholder="tu@correo.com"
                    autocomplete="username"
                    :prefix-icon="Message"
                />
            </el-form-item>

            <el-form-item label="Contraseña" :error="form.errors.password">
                <el-input
                    id="password"
                    v-model="form.password"
                    type="password"
                    size="large"
                    placeholder="Crea una contraseña"
                    autocomplete="new-password"
                    :prefix-icon="Lock"
                    show-password
                />
            </el-form-item>

            <el-form-item label="Confirmar contraseña" :error="form.errors.password_confirmation">
                <el-input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    size="large"
                    placeholder="Repite tu contraseña"
                    autocomplete="new-password"
                    :prefix-icon="Lock"
                    show-password
                />
            </el-form-item>

            <el-form-item
                v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature"
                :error="form.errors.terms"
            >
                <el-checkbox v-model="form.terms" name="terms">
                    Acepto los
                    <a :href="route('dashboard')" target="_blank" class="auth-link">términos de servicio</a>
                    y la
                    <a :href="route('dashboard')" target="_blank" class="auth-link">política de privacidad</a>
                </el-checkbox>
            </el-form-item>

            <el-button
                type="primary"
                native-type="submit"
                class="auth-submit"
                :loading="form.processing"
            >
                Crear cuenta
            </el-button>
        </el-form>

        <p class="auth-switch">
            ¿Ya tienes una cuenta?
            <Link :href="route('login')" class="auth-link auth-link--strong">Inicia sesión</Link>
        </p>
    </AuthLayout>
</template>