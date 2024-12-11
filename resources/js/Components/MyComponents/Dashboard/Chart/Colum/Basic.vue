<template>
    <div id="chart">
        <apexchart type="bar" height="350" :options="chartOptions" :series="series"></apexchart>
    </div>
</template>

<script>
export default {
    props: {
        series: {
            type: Array,
            required: true
        },
        chartOptions: {
            type: Object,
            required: true
        }
    }
}
</script>
