<script setup>
import { ref, computed, watch } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import ConfirmsPassword from '@/Components/ConfirmsPassword.vue';
import ProfileSection from '@/Components/MyComponents/ProfileSection.vue';

const props = defineProps({
    requiresConfirmation: Boolean,
});

const page = usePage();
const enabling = ref(false);
const confirming = ref(false);
const disabling = ref(false);
const qrCode = ref(null);
const setupKey = ref(null);
const recoveryCodes = ref([]);

const confirmationForm = useForm({
    code: '',
});

const twoFactorEnabled = computed(
    () => !enabling.value && page.props.auth.user?.two_factor_enabled,
);

watch(twoFactorEnabled, () => {
    if (!twoFactorEnabled.value) {
        confirmationForm.reset();
        confirmationForm.clearErrors();
    }
});

const enableTwoFactorAuthentication = () => {
    enabling.value = true;

    router.post(route('two-factor.enable'), {}, {
        preserveScroll: true,
        onSuccess: () => Promise.all([
            showQrCode(),
            showSetupKey(),
            showRecoveryCodes(),
        ]),
        onFinish: () => {
            enabling.value = false;
            confirming.value = props.requiresConfirmation;
        },
    });
};

const showQrCode = () => {
    return axios.get(route('two-factor.qr-code')).then(response => {
        qrCode.value = response.data.svg;
    });
};

const showSetupKey = () => {
    return axios.get(route('two-factor.secret-key')).then(response => {
        setupKey.value = response.data.secretKey;
    });
};

const showRecoveryCodes = () => {
    return axios.get(route('two-factor.recovery-codes')).then(response => {
        recoveryCodes.value = response.data;
    });
};

const confirmTwoFactorAuthentication = () => {
    confirmationForm.post(route('two-factor.confirm'), {
        errorBag: 'confirmTwoFactorAuthentication',
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            confirming.value = false;
            qrCode.value = null;
            setupKey.value = null;
        },
    });
};

const regenerateRecoveryCodes = () => {
    axios
        .post(route('two-factor.recovery-codes'))
        .then(() => showRecoveryCodes());
};

const disableTwoFactorAuthentication = () => {
    disabling.value = true;

    router.delete(route('two-factor.disable'), {
        preserveScroll: true,
        onSuccess: () => {
            disabling.value = false;
            confirming.value = false;
        },
    });
};
</script>

<template>
    <ProfileSection
        title="Autenticación de dos factores"
        description="Agrega una capa adicional de seguridad a tu cuenta."
    >
        <h3 class="profile-subtitle">
            <template v-if="twoFactorEnabled && !confirming">La autenticación de dos factores está habilitada.</template>
            <template v-else-if="twoFactorEnabled && confirming">Termina de habilitar la autenticación de dos factores.</template>
            <template v-else>La autenticación de dos factores está deshabilitada.</template>
        </h3>

        <p class="profile-text">
            Cuando la autenticación de dos factores está habilitada, se te solicitará un token aleatorio seguro
            al iniciar sesión. Puedes obtenerlo desde tu aplicación de autenticación (Google Authenticator,
            Authy, etc.).
        </p>

        <div v-if="twoFactorEnabled">
            <div v-if="qrCode">
                <p v-if="confirming" class="profile-text profile-text--strong">
                    Escanea el siguiente código QR con tu aplicación de autenticación o ingresa la clave de
                    configuración y proporciona el código OTP generado.
                </p>
                <p v-else class="profile-text">
                    La autenticación de dos factores ya está habilitada. Escanea el código QR o ingresa la clave
                    de configuración si necesitas reconfigurar tu aplicación.
                </p>

                <div class="two-factor-qr" v-html="qrCode" />

                <p v-if="setupKey" class="profile-text profile-text--strong">
                    Clave de configuración: <span class="two-factor-key" v-html="setupKey" />
                </p>

                <div v-if="confirming" class="profile-form__row profile-form__row--narrow">
                    <label class="profile-label" for="code">Código de verificación</label>
                    <el-input
                        id="code"
                        v-model="confirmationForm.code"
                        size="large"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        placeholder="000000"
                        @keyup.enter="confirmTwoFactorAuthentication"
                    />
                    <p v-if="confirmationForm.errors.code" class="profile-error">
                        {{ confirmationForm.errors.code }}
                    </p>
                </div>
            </div>

            <div v-if="recoveryCodes.length > 0 && !confirming">
                <p class="profile-text profile-text--strong">
                    Guarda estos códigos de recuperación en un lugar seguro. Puedes usarlos para recuperar el
                    acceso si pierdes tu dispositivo de autenticación.
                </p>

                <div class="recovery-codes">
                    <span v-for="code in recoveryCodes" :key="code">{{ code }}</span>
                </div>
            </div>
        </div>

        <template #actions>
            <div v-if="!twoFactorEnabled">
                <ConfirmsPassword @confirmed="enableTwoFactorAuthentication">
                    <el-button type="primary" :loading="enabling" :disabled="enabling">Habilitar</el-button>
                </ConfirmsPassword>
            </div>

            <div v-else class="profile-action-row">
                <ConfirmsPassword @confirmed="confirmTwoFactorAuthentication">
                    <el-button v-if="confirming" type="primary" :loading="enabling" :disabled="enabling">
                        Confirmar
                    </el-button>
                </ConfirmsPassword>

                <ConfirmsPassword @confirmed="regenerateRecoveryCodes">
                    <el-button v-if="recoveryCodes.length > 0 && !confirming">
                        Regenerar códigos
                    </el-button>
                </ConfirmsPassword>

                <ConfirmsPassword @confirmed="showRecoveryCodes">
                    <el-button v-if="recoveryCodes.length === 0 && !confirming">
                        Mostrar códigos de recuperación
                    </el-button>
                </ConfirmsPassword>

                <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
                    <el-button v-if="confirming" :disabled="disabling">Cancelar</el-button>
                </ConfirmsPassword>

                <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
                    <el-button v-if="!confirming" type="danger" plain :loading="disabling" :disabled="disabling">
                        Deshabilitar
                    </el-button>
                </ConfirmsPassword>
            </div>
        </template>
    </ProfileSection>
</template>
