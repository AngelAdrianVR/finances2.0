<template>
    <div id="chart">
        <apexchart type="area" height="350" :options="chartOptions" :series="series"></apexchart>
    </div>
</template>

<script>
export default {
    data() {
        return {
            series: [{
            name: 'series1',
            data: [31, 40, 28, 51, 42, 109, 100]
          }, {
            name: 'series2',
            data: [11, 32, 45, 32, 34, 52, 41]
          }],
          chartOptions: {
            chart: {
              height: 350,
              type: 'area'
            },
            dataLabels: {
              enabled: false
            },
            stroke: {
              curve: 'smooth'
            },
            xaxis: {
              type: 'datetime',
              categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z", "2018-09-19T06:30:00.000Z"]
            },
            tooltip: {
              x: {
                format: 'dd/MM/yy HH:mm'
              },
            },
          },
        }
    },
    // props: {
    //     series: {
    //         type: Array,
    //         required: true
    //     },
    //     chartOptions: {
    //         type: Object,
    //         required: true
    //     }
    // }
}
</script>
