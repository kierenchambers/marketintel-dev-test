<template>
    <div :class="{'container': !wide, 'container-fluid': wide}">
        <div class="row well">
            <div class="col-md-3">
                <h4>Data to show</h4>
                <select class="form-control" v-model="score_type">
                    <option value="daily">Daily Scores</option>
                    <option value="monthly">Monthly Averages</option>
                    <option value="avg">All Time Averages</option>
                </select>
            </div>
            <div class="col-md-3">
                <h4>Type of report</h4>
                <select class="form-control" v-model="show">
                    <option value="all">All</option>
                    <option value="chart">Chart Only</option>
                    <option value="table">Table Only</option>
                </select>
            </div>
            <div class="col-md-3" v-show="score_type != 'avg'">
                <h4>Date From</h4>
                <select @change="dateInputsChanged(0)" class="form-control" v-model="date_from_value">
                    <option :value="value" v-for="(label, value) in available_months">{{label}}</option>
                </select>
            </div>
            <div class="col-md-3" v-show="score_type != 'avg'">
                <h4>Date To</h4>
                <select @change="dateInputsChanged(1)" class="form-control" v-model="date_to_value">
                    <option :value="value" v-for="(label, value) in available_months">{{label}}</option>
                </select>
            </div>
        </div>
        <div class="row" v-show="show === 'all' || show === 'chart'">
            <div class="col-md-12"><h2>Chart</h2></div>
            <div class="col-md-12">
                <chart-component
                    :chart_type="chart_type"
                    :data="domain_scores"
                    :key="resyncChartKey"
                    :options="chartOptions"
                    :score_type="score_type"
                />
            </div>
        </div>
        <div class="row" v-show="show === 'all' || show === 'table'">
            <div class="col-md-12"><h2>Table</h2></div>
            <div class="col-md-12">
                <table-component
                    :data="domain_scores"
                    :key="resyncTableKey"
                    :score_type="score_type"
                />
            </div>
        </div>

    </div>
</template>
<script>
    import ChartComponent from '../components/ChartComponent';
    import TableComponent from '../components/TableComponent';
    import axios from 'axios';
    import moment from 'moment';

    export default {
        data: function () {
            return {
                domain_scores: null,
                chartOptions: {},
                score_type: 'daily',
                chart_type: 'bar',
                resyncChartKey: 0,
                resyncTableKey: 100,
                show: 'all',
                available_months: null,
                date_from_value: null,
                date_to_value: null
            }
        },
        methods: {
            getScoreData: function (from, to) {
                if (window.hasOwnProperty('testApiUrls') && window.testApiUrls.hasOwnProperty('getScores')) {
                    let params = (from !== undefined && to !== undefined) ? {from: from, to: to} : {};
                    axios.get(window.testApiUrls.getScores, {params: params})
                        .then((a) => {
                            if (a.data.success) {
                                this.domain_scores = a.data.data;
                                this.$emit('hideScreenMessage');
                            } else {
                                this.$emit(
                                    'showScreenMessage',
                                    'An error has occurred',
                                    a.data.hasOwnProperty('message') ? a.data.message : 'An unknown error has occurred.'
                                );
                            }
                        })
                        .catch((a) => {
                            this.$emit(
                                'showScreenMessage',
                                'An error has occurred',
                                a.data.hasOwnProperty('message') ? a.data.message : 'An unknown error has occurred.'
                            );
                            console.log('Failed to get score data from the API');
                        })
                }
            },
            getMonthData: function () {
                if (window.hasOwnProperty('testApiUrls') && window.testApiUrls.hasOwnProperty('getMonths')) {
                    axios.get(window.testApiUrls.getMonths)
                        .then((a) => {
                            let m = {};
                            let last = '';
                            a.data.forEach((i) => {
                                m[i.months] = moment(i.months).format('MMM YYYY');
                                last = i.months;
                            });
                            this.available_months = m;
                            this.date_from_value = last;
                            this.date_to_value = last;
                        })
                        .catch((a) => {
                            console.log('Failed to get month data from the API');
                        })
                }
            },
            dateInputsChanged: function (dateOption) {
                if (this.date_from_value > this.date_to_value) {
                    if (dateOption === 0) {
                        this.date_to_value = this.date_from_value;
                    } else {
                        this.date_from_value = this.date_to_value;
                    }
                }
                this.getScoreData(this.date_from_value, this.date_to_value);
            },
        },
        props: ['wide', 'showScreenMessage', 'hideScreenMessage'],
        watch: {
            domain_scores: function (newData) {
                this.resyncChartKey++;
                this.resyncTableKey++;
                this.domain_scores = newData;
            },
            score_type: function () {
                this.resyncChartKey++;
                this.resyncTableKey++;
            },
        },
        mounted: function () {
            setTimeout(() => {
                this.getScoreData();
                this.getMonthData();
            }, 1000);
        },
        components: {
            TableComponent,
            ChartComponent
        }
    }
</script>
