<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    Bell,
    Calendar,
    CircleCheck,
    Coin,
    DataLine,
    Files,
    Key,
    Lock,
    Money,
    Moon,
    Reading,
    Service,
    Sunny,
    Timer,
    Top,
    User,
    Wallet,
} from '@element-plus/icons-vue';
import { useDarkMode } from '@/composables/useDarkMode';

const props = defineProps({
    title: { type: String, required: true },
    eyebrow: { type: String, default: 'Documento legal' },
    description: { type: String, default: '' },
    updatedAt: { type: String, required: true },
    readTime: { type: String, required: true },
    sections: { type: Array, required: true },
    highlights: { type: Array, default: () => [] },
    summaryNote: { type: String, default: '' },
    documentKey: { type: String, required: true },
});

const iconMap = {
    bell: Bell,
    check: CircleCheck,
    coin: Coin,
    data: DataLine,
    files: Files,
    key: Key,
    lock: Lock,
    money: Money,
    reading: Reading,
    service: Service,
    user: User,
    wallet: Wallet,
};

const SUPPORT_EMAIL = 'soporte@finanzas.app';

const { isDark, toggleDark } = useDarkMode();
const page = usePage();

const isTerms = computed(() => props.documentKey === 'terms');
const isAuthenticated = computed(() => Boolean(page.props.auth?.user?.id));
const logoSrc = computed(() => (isDark.value ? '/images/white_logo.png' : '/images/color_logo.png'));
const toggleLabel = computed(() => (isDark.value ? 'Modo claro' : 'Modo oscuro'));
const currentYear = new Date().getFullYear();
const formattedSections = computed(() => String(props.sections.length).padStart(2, '0'));

const activeSection = ref(props.sections[0]?.id ?? '');
const scrollProgress = ref(0);

let frame = null;

function topbarOffset() {
    const header = document.querySelector('.legal-topbar');

    return header ? header.getBoundingClientRect().height + 14 : 88;
}

function sectionTop(id) {
    const element = document.getElementById(id);

    return element ? element.getBoundingClientRect().top + window.scrollY : null;
}

function syncScrollState() {
    frame = null;

    const scrollable = document.documentElement.scrollHeight - window.innerHeight;
    scrollProgress.value = scrollable > 0 ? Math.min(1, window.scrollY / scrollable) : 0;

    const reference = window.scrollY + topbarOffset() + 38;
    let current = props.sections[0]?.id ?? '';

    for (const section of props.sections) {
        const top = sectionTop(section.id);

        if (top !== null && top <= reference) {
            current = section.id;
        }
    }

    activeSection.value = current;
}

function onScroll() {
    if (frame !== null) {
        return;
    }

    frame = window.requestAnimationFrame(syncScrollState);
}

function goToSection(id) {
    const top = sectionTop(id);

    if (top === null) {
        return;
    }

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    window.scrollTo({ top: top - topbarOffset(), behavior: reduceMotion ? 'auto' : 'smooth' });
    window.history.replaceState(null, '', `#${id}`);
    activeSection.value = id;
}

function goToApp() {
    router.visit(isAuthenticated.value ? route('dashboard') : route('login'));
}

onMounted(() => {
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
    syncScrollState();
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', onScroll);
    window.removeEventListener('resize', onScroll);

    if (frame !== null) {
        window.cancelAnimationFrame(frame);
    }
});
</script>

<template>
    <div class="legal-shell">
        <div class="legal-progress" aria-hidden="true">
            <span class="legal-progress__bar" :style="{ transform: `scaleX(${scrollProgress})` }"></span>
        </div>

        <header class="legal-topbar">
            <div class="legal-topbar__inner">
                <Link href="/" class="legal-topbar__brand" aria-label="Ir al inicio de Finanzas">
                    <img class="legal-topbar__logo" :src="logoSrc" alt="Finanzas" />
                    <span class="legal-topbar__divider" aria-hidden="true"></span>
                    <span class="legal-topbar__label">Centro legal</span>
                </Link>

                <nav class="legal-topbar__nav" aria-label="Documentos legales">
                    <Link
                        :href="route('legal.terms')"
                        class="legal-navlink"
                        :class="{ 'is-active': isTerms }"
                    >
                        Términos
                    </Link>
                    <Link
                        :href="route('legal.privacy')"
                        class="legal-navlink"
                        :class="{ 'is-active': !isTerms }"
                    >
                        Privacidad
                    </Link>
                </nav>

                <div class="legal-topbar__actions">
                    <el-tooltip :content="toggleLabel" placement="bottom" :show-after="300">
                        <button
                            type="button"
                            class="legal-theme-toggle"
                            :aria-label="toggleLabel"
                            @click="toggleDark"
                        >
                            <el-icon :size="17">
                                <Sunny v-if="isDark" />
                                <Moon v-else />
                            </el-icon>
                        </button>
                    </el-tooltip>

                    <el-button type="primary" class="legal-login" @click="goToApp">
                        {{ isAuthenticated ? 'Ir al panel' : 'Iniciar sesión' }}
                    </el-button>
                </div>
            </div>
        </header>

        <section class="legal-hero">
            <div class="legal-hero__inner">
                <div class="legal-hero__content">
                    <span class="legal-eyebrow">
                        <span class="legal-eyebrow__dot"></span>{{ eyebrow }}
                    </span>
                    <h1 class="legal-hero__title">{{ title }}</h1>
                    <p class="legal-hero__lead">{{ description }}</p>

                    <ul class="legal-meta">
                        <li class="legal-meta__item">
                            <el-icon><Calendar /></el-icon>
                            Actualizado el {{ updatedAt }}
                        </li>
                        <li class="legal-meta__item">
                            <el-icon><Timer /></el-icon>
                            {{ readTime }} de lectura
                        </li>
                        <li class="legal-meta__item">
                            <el-icon><CircleCheck /></el-icon>
                            Sin costo
                        </li>
                    </ul>
                </div>

                <aside class="legal-summary">
                    <span class="legal-summary__sticker">100% gratis</span>
                    <p class="legal-summary__title">Resumen rápido</p>

                    <ul class="legal-summary__list">
                        <li
                            v-for="highlight in highlights"
                            :key="highlight.title"
                            class="legal-summary__item"
                        >
                            <span class="legal-summary__icon">
                                <el-icon :size="15">
                                    <component :is="iconMap[highlight.icon] ?? CircleCheck" />
                                </el-icon>
                            </span>
                            <div>
                                <strong>{{ highlight.title }}</strong>
                                <span>{{ highlight.text }}</span>
                            </div>
                        </li>
                    </ul>

                    <p v-if="summaryNote" class="legal-summary__note">{{ summaryNote }}</p>
                </aside>
            </div>
        </section>

        <main class="legal-body">
            <aside class="legal-aside">
                <nav class="legal-toc" aria-label="Contenido del documento">
                    <div class="legal-toc__head">
                        <span class="legal-toc__title">En este documento</span>
                        <span class="legal-toc__count">{{ formattedSections }}</span>
                    </div>

                    <div class="legal-toc__nav">
                        <a
                            v-for="(section, index) in sections"
                            :key="section.id"
                            :href="`#${section.id}`"
                            class="legal-toc__link"
                            :class="{ 'is-active': activeSection === section.id }"
                            @click.prevent="goToSection(section.id)"
                        >
                            <span class="legal-toc__index">
                                {{ String(index + 1).padStart(2, '0') }}
                            </span>
                            <span>{{ section.label }}</span>
                        </a>
                    </div>
                </nav>

                <div class="legal-docswitch">
                    <Link
                        :href="route('legal.terms')"
                        class="legal-docswitch__item"
                        :class="{ 'is-active': isTerms }"
                    >
                        Términos
                    </Link>
                    <Link
                        :href="route('legal.privacy')"
                        class="legal-docswitch__item"
                        :class="{ 'is-active': !isTerms }"
                    >
                        Privacidad
                    </Link>
                </div>

                <div class="legal-help">
                    <p class="legal-help__title">¿Tienes dudas?</p>
                    <p class="legal-help__text">Escríbenos y te respondemos lo antes posible.</p>
                    <a class="legal-help__link" :href="`mailto:${SUPPORT_EMAIL}`">
                        <el-icon><Service /></el-icon>
                        {{ SUPPORT_EMAIL }}
                    </a>
                </div>
            </aside>

            <article class="legal-article">
                <div class="legal-prose">
                    <slot />
                </div>
            </article>
        </main>

        <footer class="legal-footer">
            <div class="legal-footer__inner">
                <p>© {{ currentYear }} Finanzas. Todos los derechos reservados.</p>

                <nav class="legal-footer__links" aria-label="Enlaces del sitio">
                    <Link href="/">Inicio</Link>
                    <Link :href="route('legal.terms')">Términos y condiciones</Link>
                    <Link :href="route('legal.privacy')">Política de privacidad</Link>
                    <a :href="`mailto:${SUPPORT_EMAIL}`">Contacto</a>
                </nav>
            </div>
        </footer>

        <el-backtop :right="28" :bottom="28" :visibility-height="480">
            <div class="legal-backtop" aria-label="Volver arriba">
                <el-icon :size="18"><Top /></el-icon>
            </div>
        </el-backtop>
    </div>
</template>
