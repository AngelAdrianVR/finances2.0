<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import ProfileSection from '@/Components/MyComponents/ProfileSection.vue';

defineProps({
    sessions: Array,
});

const confirmingLogout = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmLogout = () => {
    confirmingLogout.value = true;

    setTimeout(() => passwordInput.value?.focus(), 250);
};

const logoutOtherBrowserSessions = () => {
    form.delete(route('other-browser-sessions.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingLogout.value = false;

    form.reset();
};
</script>

<template>
    <ProfileSection
        title="Sesiones del navegador"
        description="Administra y cierra sesión en tus sesiones activas en otros navegadores y dispositivos."
    >
        <p class="profile-text">
            Si es necesario, puedes cerrar la sesión de todas las demás sesiones del navegador en todos tus
            dispositivos. A continuación se enumeran algunas de tus sesiones recientes; sin embargo, es posible
            que esta lista no sea exhaustiva. Si crees que tu cuenta ha sido comprometida, también deberías
            actualizar tu contraseña.
        </p>

        <div v-if="sessions.length > 0" class="session-list">
            <div v-for="(session, i) in sessions" :key="i" class="session-item">
                <div class="session-item__icon">
                    <svg v-if="session.agent.is_desktop" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                    </svg>
                </div>

                <div>
                    <p class="session-item__title">
                        {{ session.agent.platform || 'Dispositivo desconocido' }}
                        <span aria-hidden="true">·</span>
                        {{ session.agent.browser || 'Navegador desconocido' }}
                    </p>
                    <p class="session-item__meta">
                        {{ session.ip_address }}
                        <span v-if="session.is_current_device" class="session-item__current">· Este dispositivo</span>
                        <span v-else>· Última actividad {{ session.last_active }}</span>
                    </p>
                </div>
            </div>
        </div>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="profile-saved">Hecho.</ActionMessage>
            <el-button type="primary" @click="confirmLogout">
                Cerrar sesión en otros dispositivos
            </el-button>
        </template>

        <el-dialog
            v-model="confirmingLogout"
            title="Cerrar sesión en otros dispositivos"
            width="460px"
            append-to-body
            :close-on-click-modal="false"
        >
            <div class="profile-form">
                <p class="profile-text">
                    Ingresa tu contraseña para confirmar que deseas cerrar la sesión en tus otras sesiones del
                    navegador en todos tus dispositivos.
                </p>
                <div class="profile-form__row">
                    <label class="profile-label" for="logout-password">Contraseña</label>
                    <el-input
                        id="logout-password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        size="large"
                        show-password
                        autocomplete="current-password"
                        @keyup.enter="logoutOtherBrowserSessions"
                    />
                    <p v-if="form.errors.password" class="profile-error">{{ form.errors.password }}</p>
                </div>
            </div>

            <template #footer>
                <el-button @click="closeModal">Cancelar</el-button>
                <el-button type="primary" :loading="form.processing" @click="logoutOtherBrowserSessions">
                    Cerrar sesión en otros dispositivos
                </el-button>
            </template>
        </el-dialog>
    </ProfileSection>
</template>
