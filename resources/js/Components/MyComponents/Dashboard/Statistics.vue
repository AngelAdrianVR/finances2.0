<template>
    <main class="border border-grayD9 rounded-xl p-5">
        <h2 class="text-[#575757] text-2xl font-bold">ESTADISTICAS</h2>
        <Basic :chartOptions="options" :series="series" />
    </main>
</template>

<script>
import Basic from '@/Components/MyComponents/Dashboard/Chart/Colum/Basic.vue';

export default {
    components: {
        Basic,
    },
    props: {
        outcomes: {
            type: Array,
            default: () => []
        },
        incomes: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            options: null,
            series: null
        };
    },
    computed: {
        categories() {
            const allDates = [...(this.outcomes || []), ...(this.incomes || [])].map(item =>
                new Date(item.created_at).toLocaleString('default', { month: 'short' })
            );
            return [...new Set(allDates)];
        },
    },
    watch: {
        // Cuando los ingresos o gastos cambien, actualiza los datos
        outcomes: {
            handler() {
                this.calculateChartData();
            },
            immediate: true,
            deep: true
        },
        incomes: {
            handler() {
                this.calculateChartData();
            },
            immediate: true,
            deep: true
        }
    },
    methods: {
        calculateChartData() {
            // Agrupar por mes y sumar los montos
            const sumByMonth = (data) => {
                const result = {};
                data?.forEach(item => {
                    const month = new Date(item.created_at).toLocaleString('default', { month: 'short' });
                    result[month] = (result[month] || 0) + item.amount;
                });
                return this.categories.map(month => result[month] || 0);
            };

            const incomesData = sumByMonth(this.incomes);
            const outcomesData = sumByMonth(this.outcomes);

            // Actualizar `series`
            this.series = [
                { name: 'Ingresos', data: incomesData },
                { name: 'Gastos', data: outcomesData }
            ];

            // Configurar las opciones del gráfico
            this.options = {
                chart: {
                    type: 'bar',
                    height: 350,
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '30%',
                        borderRadius: 5,
                        borderRadiusApplication: 'end'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: this.categories,
                },
                yaxis: {
                    title: {
                        text: '$ MXN'
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return "$ " + val + " Pesos";
                        }
                    }
                },
                colors: ['#B1E33D', '#296A6B'] // Aquí defines los colores
            };
        }
    }
};
</script>
