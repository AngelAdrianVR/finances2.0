<script setup>
import { Coin, Plus, TrendCharts } from '@element-plus/icons-vue';
import { vReveal } from '@/composables/useReveal';

const steps = [
    {
        number: '01',
        title: 'Crea tu cuenta',
        text: 'Regístrate en menos de dos minutos y empieza con tu panel financiero listo para usar.',
        icon: Plus,
    },
    {
        number: '02',
        title: 'Registra tus movimientos',
        text: 'Agrega ingresos, gastos, préstamos e inversiones, y marca los que se repiten para automatizarlos.',
        icon: Coin,
    },
    {
        number: '03',
        title: 'Observa y decide',
        text: 'Revisa tus gráficas, el calendario y los recordatorios para tomar mejores decisiones cada mes.',
        icon: TrendCharts,
    },
];
</script>

<template>
    <section id="como-funciona" class="lp-section lp-section--tint">
        <div class="lp-container">
            <div class="lp-section__head" v-reveal>
                <span class="lp-kicker">Cómo funciona</span>
                <h2 class="lp-section__title">Tres pasos para ordenar tu dinero</h2>
                <p class="lp-section__lead">
                    Sin configuraciones complicadas: crea tu cuenta y empieza a ver tus finanzas con claridad
                    desde el primer día.
                </p>
            </div>

            <div class="lp-steps">
                <article
                    v-for="(step, index) in steps"
                    :key="step.number"
                    v-reveal="{ delay: index * 120 }"
                    class="lp-step"
                >
                    <span class="lp-step__ghost">{{ step.number }}</span>
                    <span class="lp-step__icon">
                        <component :is="step.icon" />
                    </span>
                    <h3 class="lp-step__title">{{ step.title }}</h3>
                    <p class="lp-step__text">{{ step.text }}</p>
                </article>
            </div>
        </div>
    </section>
</template>
