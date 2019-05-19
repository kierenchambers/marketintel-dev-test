<template>
    <div>
        <apexchart :options="chart_options" :series="chart_series" type="bar"/>
    </div>
</template>
<script>
    import moment from 'moment';

    export default {
        props: ['data', 'options', 'chart_type', 'score_type'],
        data: function () {
            return {
                type: 'line',
                chart_options: {},
                chart_series: []
            }
        },
        methods: {
            calcChart: function () {
                this.type = this.chart_type;
                if (this.data) {
                    let labels = [];
                    let Serieses = [];
                    if (this.score_type == 'daily') {
                        // alright, let the fun begin.
                        let isFirst = true;
                        for (let domain in this.data) {
                            let newSeries = {name: domain, data: []};
                            for (let score_date in this.data[domain].daily_scores) {
                                newSeries.data.push(this.data[domain].daily_scores[score_date]);
                                if (isFirst) {
                                    labels.push(moment(score_date).format('DD MMM YYYY'));
                                }
                            }
                            isFirst = false;
                            Serieses.push(newSeries);
                        }

                    } else if (this.score_type == 'monthly') {
                        let isFirst = true;
                        for (let domain in this.data) {
                            let newSeries = {name: domain, data: []};
                            for (let score_date in this.data[domain].average_monthly) {
                                newSeries.data.push(this.data[domain].average_monthly[score_date]);
                                if (isFirst) {
                                    labels.push(moment(score_date).format('MMM YYYY'));
                                }
                            }
                            isFirst = false;
                            Serieses.push(newSeries);
                        }
                    } else if (this.score_type == 'avg') {
                        for (let domain in this.data) {
                            labels.push('All time average');
                            let newSeries = {name: domain, data: [this.data[domain].average_daily]};
                            Serieses.push(newSeries);
                        }
                    } else {
                        return false;
                    }

                    this.chart_series = Serieses;
                    this.chart_options = {
                        'xaxis': {'categories': labels, labels: {show: this.score_type != 'avg'}},
                        dataLabels: {enabled: false}
                    };

                }
            }
        },
        mounted: function () {
            this.calcChart();
        },
        watch: {
            data: function () {
                this.calcChart
            },
            options: function () {
                this.calcChart
            },
            chart_type: function () {
                this.calcChart
            },
            score_type: function () {
                this.calcChart
            }
        }
    }
</script>
