<script setup>
import { Link } from '@inertiajs/vue3';
import DTWMark from '@/Components/MyComponents/DTWMark.vue';
</script>

<template>
    <footer class="lp-footer">
        <div class="lp-container">
            <div class="lp-footer__inner">
                <div class="lp-footer__brand">
                    <img src="/images/white_logo.png" alt="Finanzas" class="lp-footer__logo" />
                    <span class="lp-footer__tagline">Organiza, ahorra y crece</span>
                </div>

                <nav class="lp-footer__nav" aria-label="Enlaces del pie de página">
                    <Link :href="route('legal.terms')" class="lp-footer__link">Términos y condiciones</Link>
                    <Link :href="route('legal.privacy')" class="lp-footer__link">Política de privacidad</Link>
                    <Link :href="route('login')" class="lp-footer__link">Iniciar sesión</Link>
                    <Link :href="route('register')" class="lp-footer__link">Crear cuenta</Link>
                </nav>
            </div>

            <div class="lp-footer__bottom">
                <p class="lp-footer__copy">Copyright © 2025 FinanzasDTW. Todos los derechos reservados</p>

                <a
                    href="https://app.dtw.com.mx"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="lp-footer__by"
                >
                    <small>By</small>
                    <DTWMark class="lp-footer__dtw" />
                </a>
            </div>
        </div>
    </footer>
</template>
