<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProfileIdentityCard from '@/Components/MyComponents/ProfileIdentityCard.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <AppLayout title="Perfil">
        <div class="page-shell">
            <header class="page-heading">
                <div>
                    <p class="page-heading__eyebrow">Tu cuenta</p>
                    <h1 class="page-heading__title">Perfil</h1>
                    <p class="page-heading__subtitle">
                        Administra tus datos personales, la seguridad de tu cuenta y tus sesiones activas.
                    </p>
                </div>
            </header>

            <div class="profile-layout">
                <aside class="profile-aside">
                    <ProfileIdentityCard :user="user" />
                </aside>

                <div class="profile-sections">
                    <UpdateProfileInformationForm
                        v-if="$page.props.jetstream.canUpdateProfileInformation"
                        :user="user"
                    />

                    <UpdatePasswordForm v-if="$page.props.jetstream.canUpdatePassword" />

                    <TwoFactorAuthenticationForm
                        v-if="$page.props.jetstream.canManageTwoFactorAuthentication"
                        :requires-confirmation="confirmsTwoFactorAuthentication"
                    />

                    <LogoutOtherBrowserSessionsForm :sessions="sessions" />

                    <DeleteUserForm v-if="$page.props.jetstream.hasAccountDeletionFeatures" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
