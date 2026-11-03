<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const isScrolled = ref(false);
let frame = null;

function handleScroll() {
    if (frame) {
        return;
    }

    frame = requestAnimationFrame(() => {
        isScrolled.value = window.scrollY > 8;
        frame = null;
    });
}

function scrollTo(sectionId) {
    document.getElementById(sectionId)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

onMounted(() => {
    handleScroll();
    window.addEventListener('scroll', handleScroll, { passive: true });
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', handleScroll);

    if (frame) {
        cancelAnimationFrame(frame);
    }
});
</script>

<template>
    <header class="lp-header" :class="{ 'is-scrolled': isScrolled }">
        <div class="lp-container lp-header__inner">
            <a href="#inicio" class="lp-header__brand" aria-label="Ir al inicio" @click.prevent="scrollTo('inicio')">
                <img src="/images/white_logo.png" alt="Finanzas" class="lp-header__logo" />
            </a>

            <nav class="lp-nav" aria-label="Secciones de la página">
                <button type="button" class="lp-nav__link" @click="scrollTo('inicio')">Inicio</button>
                <button type="button" class="lp-nav__link" @click="scrollTo('funciones')">Funciones</button>
                <button type="button" class="lp-nav__link" @click="scrollTo('como-funciona')">Cómo funciona</button>
            </nav>

            <div class="lp-header__actions">
                <Link :href="route('login')" class="lp-btn lp-btn--ghost lp-btn--sm">Iniciar sesión</Link>
                <Link :href="route('register')" class="lp-btn lp-btn--bright lp-btn--sm">Crear cuenta</Link>
            </div>
        </div>
    </header>
</template>
