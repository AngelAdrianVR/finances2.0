<script setup>
import { CircleCheck } from '@element-plus/icons-vue';
import { Link } from '@inertiajs/vue3';
import { vReveal } from '@/composables/useReveal';
</script>

<template>
    <section class="lp-cta">
        <div class="lp-container">
            <div class="lp-cta__card" v-reveal>
                <span class="lp-cta__ring lp-cta__ring--one"></span>
                <span class="lp-cta__ring lp-cta__ring--two"></span>

                <div class="lp-cta__inner">
                    <div>
                        <span class="lp-kicker">Empieza hoy</span>
                        <h2 class="lp-cta__title">
                            Tu organización financiera está a un clic de distancia
                        </h2>
                        <p class="lp-cta__text">
                            Crea tu cuenta gratis y registra tu primer movimiento en minutos. Sin instalar nada,
                            desde cualquier dispositivo.
                        </p>
                        <p class="lp-cta__note">
                            <CircleCheck />
                            Sin cargos ocultos, sin compromisos
                        </p>
                    </div>

                    <div class="lp-cta__actions">
                        <Link :href="route('register')" class="lp-btn lp-btn--bright lp-btn--lg">
                            Crear cuenta gratis
                        </Link>
                        <Link :href="route('login')" class="lp-btn lp-btn--ghost lp-btn--lg">
                            Iniciar sesión
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
