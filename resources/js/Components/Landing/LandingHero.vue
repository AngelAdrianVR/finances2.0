<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { Bell, CircleCheck, Lock, TrendCharts, Wallet } from '@element-plus/icons-vue';
import { Link } from '@inertiajs/vue3';
import LandingDashboardPreview from './LandingDashboardPreview.vue';

const rotatingWords = ['ingresos', 'gastos', 'préstamos', 'inversiones'];
const activeWord = ref(0);
const stage = ref(null);

const benefits = [
    { icon: TrendCharts, label: 'Gráficas claras' },
    { icon: Bell, label: 'Recordatorios automáticos' },
    { icon: Wallet, label: 'Préstamos bajo control' },
    { icon: Lock, label: 'Datos privados' },
];

let rotationTimer = null;
let pointerFrame = null;

onMounted(() => {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (prefersReducedMotion) {
        return;
    }

    rotationTimer = window.setInterval(() => {
        activeWord.value = (activeWord.value + 1) % rotatingWords.length;
    }, 2600);
});

onBeforeUnmount(() => {
    window.clearInterval(rotationTimer);

    if (pointerFrame) {
        cancelAnimationFrame(pointerFrame);
    }
});

function handlePointerMove(event) {
    if (!stage.value || window.matchMedia('(prefers-reduced-motion: reduce)').matches || pointerFrame) {
        return;
    }

    const { clientX, clientY } = event;

    pointerFrame = requestAnimationFrame(() => {
        const rect = stage.value.getBoundingClientRect();

        stage.value.style.setProperty('--lp-px', ((clientX - rect.left) / rect.width - 0.5).toFixed(3));
        stage.value.style.setProperty('--lp-py', ((clientY - rect.top) / rect.height - 0.5).toFixed(3));
        pointerFrame = null;
    });
}

function handlePointerLeave() {
    if (!stage.value) {
        return;
    }

    stage.value.style.setProperty('--lp-px', '0');
    stage.value.style.setProperty('--lp-py', '0');
}
</script>

<template>
    <section id="inicio" class="lp-hero">
        <div class="lp-hero__grid"></div>
        <div class="lp-hero__glow lp-hero__glow--amber"></div>
        <div class="lp-hero__glow lp-hero__glow--mint"></div>
        <div class="lp-hero__glow lp-hero__glow--teal"></div>

        <div class="lp-container lp-hero__inner">
            <div class="lp-hero__copy">
                <span class="lp-eyebrow">
                    <span class="lp-eyebrow__dot"></span>
                    Finanzas personales
                </span>

                <h1 class="lp-hero__title">
                    <span class="lp-sr-only">
                        Controla tus ingresos, gastos, préstamos e inversiones en un solo lugar
                    </span>
                    <span aria-hidden="true">Controla tus</span>
                    <span class="lp-rotator" aria-hidden="true">
                        <span
                            v-for="(word, index) in rotatingWords"
                            :key="word"
                            class="lp-rotator__word"
                            :class="{ 'is-active': index === activeWord }"
                        >
                            {{ word }}
                        </span>
                    </span>
                    <span class="lp-hero__title-tail" aria-hidden="true">en un solo lugar</span>
                </h1>

                <p class="lp-hero__lead">
                    Registra ingresos, gastos, préstamos e inversiones y entiende hacia dónde va tu dinero
                    con gráficas, calendario y recordatorios que trabajan por ti.
                </p>

                <div class="lp-hero__actions">
                    <Link :href="route('register')" class="lp-btn lp-btn--bright lp-btn--lg">
                        Crear cuenta gratis
                    </Link>
                    <Link :href="route('login')" class="lp-btn lp-btn--ghost lp-btn--lg">
                        Ya tengo cuenta
                    </Link>
                </div>

                <p class="lp-hero__hint">
                    <CircleCheck />
                    Sin cargos ocultos · Sin compromisos
                </p>

                <ul class="lp-hero__benefits">
                    <li v-for="benefit in benefits" :key="benefit.label" class="lp-hero__benefit">
                        <component :is="benefit.icon" />
                        {{ benefit.label }}
                    </li>
                </ul>
            </div>

            <div ref="stage" class="lp-hero__stage" @mousemove="handlePointerMove" @mouseleave="handlePointerLeave">
                <div class="lp-stage__halo"></div>
                <div class="lp-stage__ring"></div>
                <div class="lp-stage__ring lp-stage__ring--outer"></div>

                <div class="lp-stage__tilt">
                    <LandingDashboardPreview />

                    <div class="lp-chip lp-chip--success">
                        <span class="lp-chip__icon"><CircleCheck /></span>
                        <span>
                            <span class="lp-chip__title">Préstamo liquidado</span>
                            <span class="lp-chip__sub">Cristina Flores · hace 2 min</span>
                        </span>
                    </div>

                    <div class="lp-chip lp-chip--warning">
                        <span class="lp-chip__icon"><Bell /></span>
                        <span>
                            <span class="lp-chip__title">Recordatorio</span>
                            <span class="lp-chip__sub">Renta en 3 días</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
