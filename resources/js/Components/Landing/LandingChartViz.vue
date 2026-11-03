<script setup>
import { computed, ref } from 'vue';

const RADIUS = 52;
const CIRCUMFERENCE = 2 * Math.PI * RADIUS;

const categories = [
    { label: 'Alimentos', amount: 5240, color: '#296A6B' },
    { label: 'Transporte', amount: 2180, color: '#5B9C9D' },
    { label: 'Salud', amount: 3410, color: '#2F9E5B' },
    { label: 'Ocio', amount: 3350, color: '#E8946B' },
];

const total = categories.reduce((sum, item) => sum + item.amount, 0);

const segments = computed(() => {
    let offset = 0;

    return categories.map((item) => {
        const fraction = item.amount / total;
        const segment = {
            ...item,
            percent: Math.round(fraction * 100),
            dash: fraction * CIRCUMFERENCE,
            offset,
        };

        offset += fraction * CIRCUMFERENCE;

        return segment;
    });
});

const activeIndex = ref(0);
const active = computed(() => segments.value[activeIndex.value]);

const currency = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    maximumFractionDigits: 0,
});
</script>

<template>
    <div class="lp-viz lp-chartviz" aria-hidden="true">
        <div class="lp-viz__head">
            <span class="lp-viz__label">Gastos por categoría</span>
            <span class="lp-viz__pill">Noviembre</span>
        </div>

        <div class="lp-chartviz__body">
            <div class="lp-chartviz__donut">
                <svg viewBox="0 0 140 140" role="presentation">
                    <circle class="lp-chartviz__track" cx="70" cy="70" :r="RADIUS" />
                    <g transform="rotate(-90 70 70)">
                        <circle
                            v-for="(segment, index) in segments"
                            :key="segment.label"
                            class="lp-chartviz__arc"
                            :class="{ 'is-muted': index !== activeIndex }"
                            cx="70"
                            cy="70"
                            :r="RADIUS"
                            :stroke="segment.color"
                            :stroke-dasharray="`${segment.dash} ${CIRCUMFERENCE - segment.dash}`"
                            :stroke-dashoffset="-segment.offset"
                            @mouseenter="activeIndex = index"
                        />
                    </g>
                </svg>

                <div class="lp-chartviz__center">
                    <span class="lp-chartviz__center-label">{{ active.label }}</span>
                    <strong class="lp-chartviz__center-value">{{ currency.format(active.amount) }}</strong>
                    <span class="lp-chartviz__center-sub">{{ active.percent }}% del total</span>
                </div>
            </div>

            <ul class="lp-chartviz__legend">
                <li v-for="(segment, index) in segments" :key="segment.label">
                    <div
                        class="lp-chartviz__legend-item"
                        :class="{ 'is-active': index === activeIndex }"
                        @mouseenter="activeIndex = index"
                    >
                        <span class="lp-chartviz__swatch" :style="{ background: segment.color }"></span>
                        <span class="lp-chartviz__legend-label">{{ segment.label }}</span>
                        <span class="lp-chartviz__legend-value">{{ currency.format(segment.amount) }}</span>
                    </div>
                </li>
            </ul>
        </div>

        <div class="lp-chartviz__foot">
            <span>Total del mes</span>
            <strong>{{ currency.format(total) }}</strong>
        </div>
    </div>
</template>