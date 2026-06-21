<script setup>
import { ref, onMounted } from 'vue';
import { useForm, router, usePage } from '@inertiajs/vue3';
import { CopyDocument, Check, UserFilled, Delete, Link } from '@element-plus/icons-vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import axios from 'axios';

const props = defineProps({
    bankCards: Object,
    linkedUsers: Array,
    pendingSent: Array,
    pendingReceived: Array,
});

const activeTab = ref('links');
const copied = ref(false);

// Link by code form
const linkForm = useForm({
    link_code: '',
});

function linkByCode() {
    linkForm.post(route('settings.link-by-code'), {
        onSuccess: () => {
            linkForm.reset();
        },
    });
}

async function acceptLink(linkId) {
    try {
        await axios.post(route('settings.accept-link', linkId));
        ElMessage.success('Vinculación aceptada.');
        router.reload();
    } catch (error) {
        console.error(error);
    }
}

async function removeLink(linkId) {
    try {
        await ElMessageBox.confirm(
            '\u00bfEstás seguro de eliminar esta vinculación?',
            'Confirmar',
            { confirmButtonText: 'S\u00ed, eliminar', cancelButtonText: 'Cancelar', type: 'warning' }
        );
        await axios.delete(route('settings.remove-link', linkId));
        ElMessage.success('Vinculación eliminada.');
        router.reload();
    } catch (err) {
        if (err !== 'cancel') console.error(err);
    }
}

function copyLinkCode() {
    const page = usePage();
    const code = page.props.auth.user?.link_code;
    if (!code) return;

    const doCopy = () => {
        copied.value = true;
        ElMessage.success({
            message: 'Código copiado al portapapeles.',
            duration: 2500,
        });
        setTimeout(() => { copied.value = false; }, 2500);
    };

    navigator.clipboard.writeText(code).then(doCopy).catch(() => {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = code;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        doCopy();
    });
}

onMounted(() => {
    const page = usePage();
    if (page.props.flash?.success) {
        ElMessage.success(page.props.flash.success);
    }
    if (page.props.flash?.error) {
        ElMessage.error(page.props.flash.error);
    }
});
</script>

<template>
    <AppLayout title="Configuraciones">
        <main class="py-6 px-4 md:px-8 max-w-4xl mx-auto dark:text-gray-200">
            <h1 class="page-header">Configuraciones</h1>

            <el-tabs v-model="activeTab" class="income-tabs">
                <!-- Tab: Vinculaciones -->
                <el-tab-pane label="Vinculaciones" name="links">
                    <div class="space-y-6 mt-4">
                        <!-- My link code -->
                        <div class="dashboard-card">
                            <h2 class="dashboard-card-title">Mi código de vinculación</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                                Comparte este código con otros usuarios para que puedan vincularte. También puedes ingresar el código de otro usuario para vincularte con él.
                            </p>
                            <div class="flex items-center gap-3">
                                <code class="bg-gray-100 dark:bg-gray-800 text-primary dark:text-primary-400 font-mono text-lg font-bold px-4 py-2 rounded-lg tracking-widest select-all">
                                    {{ $page.props.auth.user?.link_code || '---' }}
                                </code>
                                <el-button
                                    :icon="copied ? Check : CopyDocument"
                                    circle
                                    size="small"
                                    :type="copied ? 'success' : 'default'"
                                    :title="copied ? 'Copiado' : 'Copiar código'"
                                    @click="copyLinkCode"
                                />
                            </div>
                        </div>

                        <!-- Link by entering another user's code -->
                        <div class="dashboard-card">
                            <h2 class="dashboard-card-title">Vincularse con otro usuario</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                Ingresa el código único de otro usuario para vincularte con él. Podrás dividir gastos en partes iguales.
                            </p>
                            <el-form :model="linkForm" @submit.prevent="linkByCode" inline>
                                <el-form-item>
                                    <el-input
                                        v-model="linkForm.link_code"
                                        placeholder="código de 8 caracteres"
                                        maxlength="8"
                                        class="!w-48"
                                        style="text-transform: uppercase;"
                                        @input="linkForm.link_code = linkForm.link_code.toUpperCase()"
                                    />
                                </el-form-item>
                                <el-form-item>
                                    <el-button type="primary" :loading="linkForm.processing" @click="linkByCode">
                                        Vincular
                                    </el-button>
                                </el-form-item>
                            </el-form>
                            <p v-if="linkForm.errors?.link_code" class="text-sm text-red-500 mt-2">{{ linkForm.errors.link_code }}</p>
                        </div>

                        <!-- Pending received -->
                        <div v-if="pendingReceived?.length" class="dashboard-card">
                            <h2 class="dashboard-card-title">Solicitudes recibidas</h2>
                            <div v-for="link in pendingReceived" :key="link.id"
                                class="flex items-center justify-between border border-gray-100 dark:border-gray-700 rounded-lg p-3 mb-2">
                                <div class="flex items-center gap-3">
                                    <img :src="link.user.profile_photo_url" :alt="link.user.name"
                                        class="size-10 rounded-full object-cover" />
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ link.user.name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ link.user.email }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <el-button size="small" type="primary" @click="acceptLink(link.id)">Aceptar</el-button>
                                    <el-button size="small" @click="removeLink(link.id)">Rechazar</el-button>
                                </div>
                            </div>
                        </div>

                        <!-- My linked users -->
                        <div class="dashboard-card">
                            <h2 class="dashboard-card-title">
                                Mis vinculaciones
                                <span class="text-sm font-normal text-gray-500 dark:text-gray-400 ml-2">({{ linkedUsers?.length || 0 }})</span>
                            </h2>

                            <div v-if="!linkedUsers?.length" class="empty-state py-6">
                                <el-icon :size="40" class="empty-state-icon"><UserFilled /></el-icon>
                                <p class="empty-state-title">No tienes usuarios vinculados</p>
                                <p class="empty-state-text">Vincula otros usuarios para dividir gastos en partes iguales</p>
                            </div>

                            <div v-else class="space-y-2">
                                <div v-for="user in linkedUsers" :key="user.id"
                                    class="flex items-center justify-between border border-gray-100 dark:border-gray-700 rounded-lg p-3">
                                    <div class="flex items-center gap-3">
                                        <img :src="user.profile_photo_url" :alt="user.name"
                                            class="size-10 rounded-full object-cover" />
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-gray-200">{{ user.name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ user.email }}</p>
                                        </div>
                                    </div>
                                    <el-icon :size="16" class="text-[#2F9E5B] dark:text-[#3BB970]"><Link /></el-icon>
                                </div>
                            </div>
                        </div>
                    </div>
                </el-tab-pane>

                <!-- Tab: Tarjetas -->
                <el-tab-pane label="Tarjetas" name="cards">
                    <div class="mt-4">
                        <div class="empty-state py-8">
                            <p class="empty-state-title">Tarjetas bancarias</p>
                            <p class="empty-state-text">Administra tus tarjetas bancarias aqu\u00ed</p>
                        </div>
                    </div>
                </el-tab-pane>
            </el-tabs>
        </main>
    </AppLayout>
</template>