<template>
    <div>
        <a @click.prevent="switchDailyTableColumns" class="btn btn-table-rotate" href="#"
           v-show="score_type == 'daily'">{{ rotateTableText }}</a>
        <div class="table-responsive" ref="tableholder">
            <datatable :columns="columns" :data="rows"></datatable>
        </div>
    </div>
</template>
<script>
    import moment from 'moment';

    export default {
        props: ['data', 'score_type'],
        data: function () {
            return {
                columns: [],
                rows: [],
                dailyTableUseDateColumns: true
            }
        },
        methods: {
            calcRows: function () {
                if (this.data) {

                    let labels = [];
                    let rows = [];

                    labels.push({label: 'domain', field: 'domain'});

                    if (this.score_type == 'daily') {
                        // alright, let the fun begin.
                        /*** Changing this as data is probably better presented if dates are set as rows and domain as columns ***/
                        if (this.dailyTableUseDateColumns) {
                            let isFirst = true;
                            for (let domain in this.data) {
                                let newRow = {domain: domain};
                                for (let score_date in this.data[domain].daily_scores) {
                                    newRow[score_date] = this.data[domain].daily_scores[score_date];
                                    if (isFirst) {
                                        labels.push({
                                            label: moment(score_date).format('DD MMM YYYY'),
                                            'field': score_date
                                        });
                                    }
                                }
                                isFirst = false;
                                rows.push(newRow);
                            }
                        } else {
                            let isFirst = true;
                            labels = [{label: 'date', field: 'date'}];
                            let domainMapping = [];

                            // Building a key mapping of all our domains.
                            for (let domain in this.data) {
                                if (isFirst) {
                                    labels.push({label: domain, 'field': domain.replace(/\./g, '')});
                                }
                                domainMapping.push(domain);
                            }

                            // now lets build our rows table.
                            for (let date in this.data[domainMapping[0]].daily_scores) {
                                let newRow = {date: moment(date).format('DD MMM YYYY')};
                                domainMapping.forEach((i) => {
                                    newRow[i.replace(/\./g, '')] = this.data[i].daily_scores[date];
                                });
                                rows.push(newRow);
                            }

                        }


                    } else if (this.score_type == 'monthly') {
                        let isFirst = true;
                        for (let domain in this.data) {
                            let newRow = {domain: domain};
                            for (let score_date in this.data[domain].average_monthly) {
                                newRow[score_date] = this.data[domain].average_monthly[score_date];
                                if (isFirst) {
                                    labels.push({label: moment(score_date).format('MMM YYYY'), 'field': score_date});
                                }
                            }
                            isFirst = false;
                            rows.push(newRow);
                        }
                    } else if (this.score_type == 'avg') {
                        labels.push({label: 'All time average', field: 'all'});
                        for (let domain in this.data) {
                            let newRow = {domain: domain, all: this.data[domain].average_daily};
                            rows.push(newRow);
                        }
                    } else {
                        return false;
                    }

                    this.rows = rows;
                    this.columns = labels;
                }

            },
            switchDailyTableColumns: function () {
                this.dailyTableUseDateColumns = !this.dailyTableUseDateColumns;
                this.$refs.tableholder.scroll(0, 0);
                this.calcRows();
            }
        },
        mounted: function () {
            this.calcRows();
        },
        computed: {
            rotateTableText: function () {
                return !this.dailyTableUseDateColumns ? 'Use dates for columns' : 'Use domains for columns';
            }
        },
        watch: {
            data: function () {
                this.calcRows
            },
            score_type: function () {
                this.calcRows
            }
        }
    }
</script>
