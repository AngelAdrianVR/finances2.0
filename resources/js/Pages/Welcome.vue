<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import LandingHeader from '@/Components/Landing/LandingHeader.vue';
import LandingHero from '@/Components/Landing/LandingHero.vue';
import LandingFeatures from '@/Components/Landing/LandingFeatures.vue';
import LandingSteps from '@/Components/Landing/LandingSteps.vue';
import LandingCta from '@/Components/Landing/LandingCta.vue';
import LandingFooter from '@/Components/Landing/LandingFooter.vue';

const progress = ref(0);
let frame = null;

function handleScroll() {
    if (frame) {
        return;
    }

    frame = requestAnimationFrame(() => {
        const scrollable = document.documentElement.scrollHeight - window.innerHeight;

        progress.value = scrollable > 0 ? Math.min(window.scrollY / scrollable, 1) : 0;
        frame = null;
    });
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
    <Head title="Bienvenido">
        <meta
            name="description"
            content="Registra tus ingresos, gastos, préstamos e inversiones en un solo lugar. Gráficas, calendario y recordatorios automáticos para controlar tus finanzas."
        />
    </Head>

    <div class="lp">
        <div class="lp-progress" :style="{ width: `${progress * 100}%` }"></div>

        <LandingHeader />

        <main>
            <LandingHero />
            <LandingFeatures />
            <LandingSteps />
            <LandingCta />
        </main>

        <LandingFooter />
    </div>
</template>
