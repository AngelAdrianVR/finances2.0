<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import ProfileSection from '@/Components/MyComponents/ProfileSection.vue';

const props = defineProps({
    user: Object,
});

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
    photo: null,
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (!photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>

<template>
    <ProfileSection
        title="Información del perfil"
        description="Actualiza tu foto, tu nombre y la dirección de correo electrónico de tu cuenta."
    >
        <form class="profile-form" @submit.prevent="updateProfileInformation">
            <input
                id="photo"
                ref="photoInput"
                type="file"
                class="hidden"
                accept="image/*"
                @change="updatePhotoPreview"
            />

            <div v-if="$page.props.jetstream.managesProfilePhotos" class="profile-photo">
                <img
                    v-show="!photoPreview"
                    class="profile-photo__preview"
                    :src="user.profile_photo_url"
                    :alt="user.name"
                />
                <span
                    v-show="photoPreview"
                    class="profile-photo__preview"
                    :style="{ backgroundImage: `url('${photoPreview}')` }"
                />

                <div class="profile-photo__meta">
                    <div class="profile-photo__actions">
                        <el-button @click.prevent="selectNewPhoto">Cambiar foto</el-button>
                        <el-button
                            v-if="user.profile_photo_path"
                            type="danger"
                            text
                            @click.prevent="deletePhoto"
                        >
                            Eliminar foto
                        </el-button>
                    </div>
                    <p class="profile-photo__hint">Formatos JPG, PNG o GIF. Tamaño máximo 1 MB.</p>
                    <p v-if="form.errors.photo" class="profile-error">{{ form.errors.photo }}</p>
                </div>
            </div>

            <div class="profile-form__row">
                <label class="profile-label" for="name">Nombre</label>
                <el-input
                    id="name"
                    v-model="form.name"
                    size="large"
                    autocomplete="name"
                    placeholder="Tu nombre"
                />
                <p v-if="form.errors.name" class="profile-error">{{ form.errors.name }}</p>
            </div>

            <div class="profile-form__row">
                <label class="profile-label" for="email">Correo electrónico</label>
                <el-input
                    id="email"
                    v-model="form.email"
                    size="large"
                    type="email"
                    autocomplete="username"
                    placeholder="tucorreo@ejemplo.com"
                />
                <p v-if="form.errors.email" class="profile-error">{{ form.errors.email }}</p>

                <div
                    v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null"
                    class="profile-alert"
                >
                    <p>Tu correo electrónico aún no ha sido verificado.</p>
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="profile-alert__link"
                        @click.prevent="sendEmailVerification"
                    >
                        Reenviar correo de verificación
                    </Link>
                    <p v-show="verificationLinkSent" class="profile-alert__success">
                        Hemos enviado un nuevo enlace de verificación a tu correo.
                    </p>
                </div>
            </div>
        </form>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="profile-saved">Guardado.</ActionMessage>
            <el-button type="primary" :loading="form.processing" @click="updateProfileInformation">
                Guardar cambios
            </el-button>
        </template>
    </ProfileSection>
</template>
