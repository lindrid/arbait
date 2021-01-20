<template>
    <div>
        <div class="panel panel-default">
            <div class="panel-heading">Доходы, расходы</div>
            <div class="panel-body">
                <div class="alert alert-danger" v-if="error">
                    <div name="main" v-if="error && errors.main">
                        <p>{{errors.main}}</p>
                    </div>
                </div>

                <form v-on:submit="saveForm()">
                    <div class="row visually-hidden">
                        <div class="col-xs-12">
                            <radio class="input-group" name="radiobtn_dispatcher"
                                   v-bind:value="1" v-model="dispatcher_id">
                                Глебас
                            </radio>
                        </div>
                        <div class="col-xs-12">
                            <radio class="input-group" name="radiobtn_dispatcher"
                                   v-bind:value="2" v-model="dispatcher_id">
                                Димас
                            </radio>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label class="control-label">Текст заявки</label>
                            <textarea id="text" v-model="income.text" class="form-control">
                            </textarea>
                            <span class="help-block" v-if="error && errors.text">{{ errors.text }}</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label class="control-label">Доход с заявки</label>
                            <input id="income"  type="text" v-model="income.money_amount" class="form-control"
                                   v-on:keyup.enter="saveForm()">
                            <span class="help-block" v-if="error && errors.income">{{ errors.income }}</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label class="control-label">Расход ЗА ДЕНЬ</label>
                            <input id="outcome" type="text" v-model="outcome" class="form-control"
                                   v-on:keyup.enter="saveForm()">
                            <span class="help-block" v-if="error && errors.outcome">{{ errors.outcome }}</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label class="control-label">Дата доходов, расходов</label>
                            <input id="date" type="text" v-model="date" class="form-control"
                                   v-on:keyup.enter="saveForm()">
                            <span class="help-block" v-if="error && errors.date">{{ errors.date }}</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <button class="btn btn-success">Сохранить</button>

                        <router-link :to="{ name: 'accDetailDay' }" class="btn btn-default">
                            Детализация за день
                        </router-link>

                        <!--<router-link  :to="{name: 'accDetailDay', params: {date: date}}"
                                      class="btn btn-default">
                            Детализиция за {{date}}
                        </router-link>!-->

                        <router-link :to="{ name: 'accDetailMonth' }" class="btn btn-default">
                            Детализация за месяц
                        </router-link>

                        <router-link :to="{ name: 'applications',  params: {date: 'actual'} }" class="btn btn-default">
                            Заявки
                        </router-link>
                        </div>
                    </div>

                    <div class="alert alert-success" v-if="success">
                        <div class="bs">
                            <div class="container">
                                <div class="row">
                                    <div class="col-xs-12 col-md-4">
                                      <p>Отправить Димасу: {{ten_percent}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-success" v-if="success">
                        <div class="bs">
                            <div class="container">
                                <div class="row">
                                    <div class="col-xs-12 col-md-4">
                                        <p>Доход за сегодня: {{day_income}} </p>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <p>Доход за месяц ({{month_day_count}} дней): {{month_income}} </p>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <p>ВСЕГО доход за {{year_day_count}} дней: {{year_income}} </p>
                                    </div>
                                  <div class="col-xs-12 d-md-none">
                                    <p>&nbsp;</p>
                                  </div>
                                </div>
                            </div>
                        </div>

                        <div class="bs">
                            <div class="container">
                                <div class="row">
                                    <div class="col-xs-12 col-md-4">
                                        <p>Расход за сегодня: {{day_outcome}}</p>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <p>Расход за месяц ({{month_day_count}} дней): {{month_outcome}}</p>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <p>ВСЕГО расход за {{year_day_count}} дней: {{year_outcome}}</p>
                                    </div>
                                  <div class="col-xs-12 d-md-none">
                                    <p>&nbsp;</p>
                                  </div>
                                </div>
                            </div>
                        </div>

                        <div class="bs">
                            <div class="container">
                                <div class="row">
                                    <div class="col-xs-12 col-md-4">
                                        <p v-if="day_profit >= 0">Прибыль за сегодня: {{day_profit}}</p>
                                        <div class="alert alert-danger" v-if="success && (day_profit < 0)">
                                            <p>Прибыль за сегодня: {{day_profit}}</p>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <p v-if="month_profit >= 0">Прибыль за месяц ({{month_day_count}} дня): {{month_profit}}</p>
                                        <div class="alert alert-danger" v-if="success && (month_profit < 0)">
                                            <p>Прибыль за месяц ({{month_day_count}} дней): {{month_profit}}</p>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <p v-if="year_profit >= 0">ВСЕГО прибыль за {{year_day_count}} дней: {{year_profit}}</p>
                                        <div class="alert alert-danger" v-if="success && (year_profit < 0)">
                                            <p>ВСЕГО прибыль : {{year_profit}}</p>
                                        </div>
                                      <div class="col-xs-12 d-md-none">
                                        <p>&nbsp;</p>
                                      </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "accountancy",
        data: function () {
            return {
                dispatcher_id: 0,

                card_outcome: -1,
                income: {
                    text: '',
                    was_parsed: 0,
                    money_amount: '',
                },
                outcome: '',
                date: '',

                day_income: false,
                day_income_gleb: false,
                day_income_dima: false,
                day_outcome: false,
                day_profit: false,
                ten_percent: false,

                gleb_must_send: false,
                dima_must_send: false,

                month_income: false,
                month_outcome: false,
                month_profit: false,
                month_day_count: false,

                year_income: false,
                year_outcome: false,
                year_profit: false,
                year_day_count: false,
                success: false,

                error: false,
                errors: {},
            }
        },
        methods: {
            curday: function (sp) {
                var today = new Date();
                var dd = today.getDate();
                var mm = today.getMonth()+1; //As January is 0.
                var yyyy = today.getFullYear();

                if(dd<10) dd='0'+dd;
                if(mm<10) mm='0'+mm;
                return (yyyy+sp+mm+sp+dd);
            },
            saveForm() {
                event.preventDefault();
                var app = this;
                var newAcc = app.income;
                if (this.error == true) {
                    this.error = false;
                    this.errors.income = undefined;
                    this.errors.outcome = undefined;
                    this.errors.text = undefined;
                    this.errors.main = undefined;
                    this.errors.date = undefined;
                }

                this.$axios.post('/accountancy/store',
                    {
                        dispatcher_id: this.dispatcher_id,
                        text: this.income.text,
                        was_parsed: this.income.was_parsed,
                        income: this.income.money_amount,
                        outcome: this.outcome,
                        date: this.date
                    }
                ).then(response => {
                    if (response.status == 200) {
                        this.day_income = response.data.day_income;
                        this.day_income_gleb = response.data.day_income_gleb;
                        this.day_income_dima = response.data.day_income_dima;

                        this.gleb_must_send = response.data.gleb_must_send;
                        this.dima_must_send = response.data.dima_must_send;

                        this.day_outcome = response.data.day_outcome;
                        this.day_profit = response.data.day_profit;

                        this.month_income = response.data.month_income;
                        this.month_outcome = response.data.month_outcome;
                        this.month_profit = response.data.month_profit;
                        this.month_day_count = response.data.month_day_count;

                        this.year_income = response.data.year_income;
                        this.year_outcome = response.data.year_outcome;
                        this.year_profit = response.data.year_profit;
                        this.year_day_count = response.data.year_day_count;

                        this.card_outcome = response.data.card_outcome;
                        this.ten_percent = response.data.ten_percent;

                        this.success = true;
                        this.income.text = '';
                        this.income.money_amount = '';
                        this.outcome = '';
                    }
                    console.log(response);
                }).catch(function (error) {
                    app.error = true;
                    //console.log(error.response.data);
                    app.errors = error.response.data.errors;
                    console.log(app.errors);
                    if (typeof(app.errors) != 'undefined') {
                        if (typeof app.errors.main !== 'undefined') {
                            document.getElementById("main").scrollIntoView({block: "center", behavior: "smooth"});
                        }
                        if (typeof app.errors.income !== 'undefined') {
                            document.getElementById("income").scrollIntoView({block: "center", behavior: "smooth"});
                        }
                        if (typeof app.errors.outcome !== 'undefined') {
                            document.getElementById("outcome").scrollIntoView({block: "center", behavior: "smooth"});
                        }
                        if (typeof app.errors.text !== 'undefined') {
                            document.getElementById("text").scrollIntoView({block: "center", behavior: "smooth"});
                        }
                    }
                    else {
                        app.errors = {main: false};
                    }

                });
            }
        },
        mounted () {
            console.log('Route:');
            console.log(this.$window.route);
            console.log('User privileges');
            console.log(this.$window.userPrivileges);
            var dispatcher_name = this.$route.params.dispatcher_name;
            if (dispatcher_name == 'gleb') {
                this.dispatcher_id = 1;
            }
            else if (dispatcher_name == 'dima') {
                this.dispatcher_id = 2;
            }
            else {
                this.dispatcher_id = 1;
            }
            console.log(dispatcher_name);
            console.log(this.dispatcher_id);
            this.date = this.curday('-');
        }
    }
</script>

<style scoped>
    .visually-hidden {
        position: absolute;
        clip: rect(0 0 0 0);
        width: 1px;
        height: 1px;
        margin: -1px;
    }

    .bs .row > .col, .bs .row > [class^="col-"] {
        padding-top: .75rem;
        padding-bottom: .75rem;
        border: 1px solid #000;
    }

    .panel {
        font-size: 130%;
    }
    body {
        margin: 0;
    }
    #text {
        height: 25vh;
    }
    .help-block{
        color: red;
    }

    .checkbox-component > input + label > .input-box,
    .radio-component > input + label > .input-box {
        font-size: 1.2em;
        text-align: center;
        line-height: 1;
        color: transparent;
    }
    .checkbox-component > input + label > .input-box > .input-box-tick,
    .radio-component > input + label > .input-box > .input-box-circle {
        display: none;
    }
    .checkbox-component > input + label > .input-box:before,
    .radio-component > input + label > .input-box:before {
        content: '✔';
    }
    .checkbox-component > input:checked + label > .input-box:before,
    .radio-component > input:checked + label > .input-box:before {
        color: #000;
    }
</style>