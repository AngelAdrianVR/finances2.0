<script setup>
import { Calendar, Coin, TrendCharts, Wallet } from '@element-plus/icons-vue';
import { vReveal } from '@/composables/useReveal';
import LandingChartViz from './LandingChartViz.vue';
import LandingLoansViz from './LandingLoansViz.vue';
import LandingCalendarViz from './LandingCalendarViz.vue';
import LandingBudgetViz from './LandingBudgetViz.vue';

const marqueeItems = [
    'Ingresos recurrentes',
    'Gastos fijos',
    'Préstamos otorgados',
    'Préstamos recibidos',
    'Inversiones',
    'Recordatorios',
    'Categorías',
    'Calendario',
    'Notificaciones',
    'Gráficas interactivas',
];

const features = [
    {
        eyebrow: 'Análisis',
        title: 'Gráficas intuitivas',
        text: 'Consulta de un vistazo tus ingresos, gastos y saldo total con gráficos interactivos que simplifican la toma de decisiones financieras.',
        icon: TrendCharts,
        size: 'wide',
        viz: LandingChartViz,
    },
    {
        eyebrow: 'Préstamos',
        title: 'Control total de préstamos',
        text: 'Registra préstamos otorgados y recibidos y automatiza los abonos para mantener tus finanzas organizadas sin esfuerzo.',
        icon: Wallet,
        size: 'narrow',
        viz: LandingLoansViz,
    },
    {
        eyebrow: 'Calendario',
        title: 'Gestión desde un calendario',
        text: 'Planea y organiza con el calendario integrado: los movimientos recurrentes se generan solos en la fecha que elijas.',
        icon: Calendar,
        size: 'narrow',
        viz: LandingCalendarViz,
    },
    {
        eyebrow: 'Categorías',
        title: 'Administra tus gastos e ingresos',
        text: 'Organiza tus movimientos por categoría y mantén el control al día, sin hojas de cálculo ni cálculos manuales.',
        icon: Coin,
        size: 'wide',
        viz: LandingBudgetViz,
    },
];

function handleSpotlight(event) {
    const card = event.currentTarget;
    const rect = card.getBoundingClientRect();

    card.style.setProperty('--lp-mx', `${event.clientX - rect.left}px`);
    card.style.setProperty('--lp-my', `${event.clientY - rect.top}px`);
}
</script>

<template>
    <div class="lp-marquee" aria-hidden="true">
        <div class="lp-marquee__track">
            <div v-for="copy in 2" :key="copy" class="lp-marquee__group">
                <span v-for="item in marqueeItems" :key="`${copy}-${item}`" class="lp-marquee__item">
                    <TrendCharts />
                    {{ item }}
                </span>
            </div>
        </div>
    </div>

    <section id="funciones" class="lp-section">
        <div class="lp-container">
            <div class="lp-section__head" v-reveal>
                <span class="lp-kicker">Funciones</span>
                <h2 class="lp-section__title">Todo lo que tu dinero necesita, en un solo panel</h2>
                <p class="lp-section__lead">
                    Deja las hojas de cálculo: registra, planea y analiza cada movimiento desde una sola
                    herramienta pensada para tus finanzas personales.
                </p>
            </div>

            <div class="lp-bento">
                <article
                    v-for="(feature, index) in features"
                    :key="feature.title"
                    v-reveal="{ delay: (index % 2) * 120 }"
                    class="lp-feature"
                    :class="`lp-feature--${feature.size}`"
                    @mousemove="handleSpotlight"
                >
                    <div class="lp-feature__top">
                        <span class="lp-feature__icon">
                            <component :is="feature.icon" />
                        </span>
                        <span class="lp-feature__eyebrow">{{ feature.eyebrow }}</span>
                    </div>

                    <h3 class="lp-feature__title">{{ feature.title }}</h3>
                    <p class="lp-feature__text">{{ feature.text }}</p>

                    <component :is="feature.viz" />
                </article>
            </div>
        </div>
    </section>
</template>