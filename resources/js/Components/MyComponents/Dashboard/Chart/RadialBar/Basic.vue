<template>
    <apexchart type="radialBar" height="150" :width="width" :options="chartOptions" :series="series" />
</template>

<script>
export default {
    props: {
        series: { type: Array, default: () => [0] },
        width: { type: String, default: undefined },
    },
    data() {
        return {
            isDark: false,
            observer: null,
        };
    },
    computed: {
        gradientColors() {
            const value = Number(this.series?.[0] ?? 0);

            if (value <= 30) {
                return [
                    { offset: 0, color: '#D64545' },
                    { offset: 100, color: '#F08A8A' },
                ];
            }

            if (value <= 60) {
                return [
                    { offset: 0, color: '#B47F1F' },
                    { offset: 100, color: '#E8B450' },
                ];
            }

            return [
                { offset: 0, color: '#247A47' },
                { offset: 100, color: '#4FC98A' },
            ];
        },
        chartOptions() {
            return {
                chart: {
                    height: 150,
                    type: 'radialBar',
                    background: 'transparent',
                },
                plotOptions: {
                    radialBar: {
                        hollow: { size: '62%' },
                        dataLabels: {
                            value: {
                                offsetY: -6,
                                color: this.isDark ? '#EDF1F1' : '#1A1F1F',
                                fontSize: '14px',
                                fontWeight: 600,
                            },
                        },
                        track: {
                            background: this.isDark ? 'rgba(255, 255, 255, 0.08)' : '#EDF1F1',
                            strokeWidth: '97%',
                            margin: 5,
                        },
                    },
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        shadeIntensity: 0.4,
                        inverseColors: false,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 50, 53, 91],
                        colorStops: this.gradientColors,
                    },
                },
                stroke: { lineCap: 'round' },
                labels: [''],
            };
        },
    },
    mounted() {
        this.syncTheme();
        this.observer = new MutationObserver(() => this.syncTheme());
        this.observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    },
    beforeUnmount() {
        this.observer?.disconnect();
    },
    methods: {
        syncTheme() {
            this.isDark = document.documentElement.classList.contains('dark');
        },
    },
};
</script>
