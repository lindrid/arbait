<template>
    <div class="panel panel-default">
        <div class="panel-heading">Детализация по месяцам</div>
        <div class="panel-body">
            <div class="row">
                <div class="row" >
                    <router-link :to="{name: 'accountancy'}" class="btn btn-default sdvinut_chutka">
                        Доходы/расходы
                    </router-link>
                </div>

                <div class="alert alert-success" v-if="success">
                    <p>Количество заявок : <b>{{month_app_count}} шт</b></p><br>
                    <p>Доход за месяц <b>{{month_name}}</b>({{month_day_count}} дней): {{month_income}} </p>
                    <p>Расход за месяц <b>{{month_name}}</b>({{month_day_count}} дней): {{month_outcome}}</p>
                    <p v-if="month_profit >= 0">Прибыль за месяц <b>{{month_name}}</b>({{month_day_count}} дня): {{month_profit}}</p>
                </div>
                <div class="alert alert-danger" v-if="success && (month_profit < 0)">
                    <p>Прибыль за месяц <b>{{month_name}}</b>({{month_day_count}} дней): {{month_profit}}</p>
                </div>

                <div class="row" v-if="selectedDate != ''">
                    <router-link  :to="{name: 'accDetailDay', params: {date: this.selectedDate}}"
                                 class="btn btn-default sdvinut">
                        Детализиция за {{selectedDate}}
                    </router-link>
                </div>
                <vue-event-calendar
                        :events="demoEvents"
                        @day-changed="handleDayChanged"
                        @month-changed="handleMonthChanged">
                </vue-event-calendar>
            </div>

        </div>
    </div>
</template>


<script>
    export default {
        name: "accDetailMonth",
        data() {
            return {
                demoEvents: [{
                    date: '2019/07/15',
                    title: 'eat',
                    desc: 'longlonglong description',
                    customClass: 'color: red'
                },{
                    date: '2019/07/12',
                    title: 'this is a title'
                }],
                selectedDate: '',
                selectedMonth: '',
                success: false,
                month_income: false,
                month_outcome: false,
                month_profit: false,
                month_day_count: false,
                month_app_count: 0,
                month_name: false,

                incomes: [],
                outcomes: [],
                profits: [],
                day_counts: [],
                month_names: [],
                month_app_count_array: []
            };
        },
        components: {
        },
        methods: {
            handleDayChanged (data) {
                //console.log(data);
                this.selectedDate = data.date.replace('/', '-');
                this.selectedDate = this.selectedDate.replace('/', '-');

            },
            handleMonthChanged (data) {
                var currMonth = data.substring(3, data.length) + '/' +  (data.substring(0, 2));

                this.month_income = this.incomes[currMonth];
                this.month_outcome = this.outcomes[currMonth];
                this.month_profit = this.profits[currMonth];
                this.month_day_count = this.day_counts[currMonth];
                this.month_name = this.month_names[currMonth];
                this.month_app_count = this.month_app_count_array[currMonth];
            }
        },
        mounted(){
            var app = this;
            var today = new Date();
            this.$axios.get('/accountancy/index/month')
                .then(function (resp) {
                    app.demoEvents = resp.data.calcs;
                    app.incomes = resp.data.incomes;
                    app.outcomes = resp.data.outcomes;
                    app.profits = resp.data.profits;
                    app.day_counts = resp.data.day_counts;
                    app.month_names = resp.data.month_names;
                    app.month_app_count_array = resp.data.month_app_count_array;
                    app.success = true;

                    var month = (today.getMonth()+1).toString();
                    if (month.length == 1) {
                        month = '0' + month;
                    }
                    var currMonth = today.getFullYear() + '/' + month;
                    console.log(currMonth);
                    app.month_income = app.incomes[currMonth];
                    app.month_outcome = app.outcomes[currMonth];
                    app.month_profit = app.profits[currMonth];
                    app.month_day_count = app.day_counts[currMonth];
                    app.month_name = app.month_names[currMonth];
                    app.month_app_count = app.month_app_count_array[currMonth];
                })
                .catch(function (resp) {
                    alert("Не удалось загрузить");
                });

        }
    }
</script>

<style>
    .sdvinut {
        margin: auto;
        left: 20%;
        position: relative;
    }
    .sdvinut_chutka {
        margin: auto;
        left: 15px;
        position: relative;
    }
</style>