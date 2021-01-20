<template>
    <div>
        <div class="panel panel-default">
            <div class="panel-heading">Доходы расходы</div>
            <div class="panel-body">
                <div class="alert alert-danger" v-if="error">
                    <div id="main" v-if="error && errors.main">
                        <p>{{errors.main}}</p>
                    </div>
                </div>
                <form v-on:submit="saveForm()">
                    <div v-for="ipublic in publics">
                        <div class="row form-group">
                            <div class="col-xs-12">
                                <radio name="radiobtn_insta_public"
                                       :value="ipublic.id"
                                       v-model="public_id"
                                >
                                    {{ipublic.name}}
                                </radio>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label class="control-label">Описание</label>
                            <input id="text"  type="text" v-model="text" class="form-control">
                            <span class="help-block" v-if="error && errors.text">{{ errors.text }}</span>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label class="control-label">Доход с картинки</label>
                                <button class="btn btn-default col-auto"
                                        @click="income_arr[public_id] = ''"
                                        :disabled="income_arr[public_id] == ''"
                                >
                                    <i class="glyphicon glyphicon-remove-circle"></i>
                                </button>
                                <input
                                        id="income"
                                        type="text"
                                        v-model="income_arr[public_id]"
                                        class="form-control"
                                >
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

                        <div class="nopadding col-md-1 form-group" style="align-items: left">
                            <button class="btn btn-success" >Сохранить</button>
                        </div>
                </form>

                <div class="row form-group">
                    <div class="col-xs-3 col-md-1 ">
                        <button @click="deleteLast" class="btn btn-warning">Удалить</button>
                    </div>

                    <div class="col-xs-3 col-md-2">
                        <button @click="seeStatistics" class="btn">Посмотреть</button>
                    </div>

                    <div class="col-xs-3 col-md-2">
                        <router-link :to="{
                            name: 'accInstagramDetailDay',
                            params: {date: date}
                        }" class="btn btn-default"
                        >
                            Детализация за день
                        </router-link>
                    </div>
                </div>

                <br><br>

                <div class=" alert alert-success form-group" v-if="success">
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="alert alert-success" v-if="success">
                        <div class="h3">{{publics[0].name}}</div>
                        <div class="bs">
                            <div class="container">
                                <div class="row">
                                    <div class="col-xs-12 col-md-4">
                                        <p>Доход за сегодня: {{day_income_rabota}} </p>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <p>Доход за месяц ({{month_day_count_rabota}} дней): {{month_income_rabota}} </p>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <p>ВСЕГО доход за {{year_day_count_rabota}} дней: {{year_income_rabota}} </p>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="bs">
                            <div class="container">
                                <div class="row">
                                    <div class="col-xs-12 col-md-4">
                                        <p>Расход за сегодня: {{day_outcome_rabota}}</p>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <p>Расход за месяц ({{month_day_count_rabota}} дней): {{month_outcome_rabota}}</p>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <p>ВСЕГО расход за {{year_day_count_rabota}} дней: {{year_outcome_rabota}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bs">
                            <div class="container">
                                <div class="row">
                                    <div class="col-xs-12 col-md-4">
                                        <p v-if="day_profit_rabota >= 0">Прибыль за сегодня: {{day_profit_rabota}}</p>
                                        <div class="alert alert-danger" v-if="success && (day_profit_rabota < 0)">
                                            <p>Прибыль за сегодня: {{day_profit_rabota}}</p>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <p v-if="month_profit_rabota >= 0">
                                            Прибыль за месяц ({{month_day_count_rabota}} дня): {{month_profit_rabota}}</p>
                                        <div class="alert alert-danger" v-if="success && (month_profit_rabota < 0)">
                                            <p>Прибыль за месяц ({{month_day_count_rabota}} дней): {{month_profit_rabota}}</p>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <p v-if="year_profit_rabota >= 0">ВСЕГО прибыль за {{year_day_count_rabota}} дней: {{year_profit_rabota}}</p>
                                        <div class="alert alert-danger" v-if="success && (year_profit_rabota < 0)">
                                            <p>ВСЕГО прибыль : {{year_profit_rabota}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="alert alert-success" v-if="success">
                        <div class="h3">{{publics[1].name}}</div>
                        <div class="bs">
                            <div class="container">
                                <div class="row">
                                    <div class="col-xs-12 col-md-4">
                                        <p>Доход за сегодня: {{day_income_models}} </p>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <p>Доход за месяц ({{month_day_count_models}} дней): {{month_income_models}} </p>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <p>ВСЕГО доход за {{year_day_count_models}} дней: {{year_income_models}} </p>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="bs">
                            <div class="container">
                                <div class="row">
                                    <div class="col-xs-12 col-md-4">
                                        <p>Расход за сегодня: {{day_outcome_models}}</p>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <p>Расход за месяц ({{month_day_count_models}} дней): {{month_outcome_models}}</p>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <p>ВСЕГО расход за {{year_day_count_models}} дней: {{year_outcome_models}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bs">
                            <div class="container">
                                <div class="row">
                                    <div class="col-xs-12 col-md-4">
                                        <p v-if="day_profit_models >= 0">Прибыль за сегодня: {{day_profit_models}}</p>
                                        <div class="alert alert-danger" v-if="success && (day_profit_models < 0)">
                                            <p>Прибыль за сегодня: {{day_profit_models}}</p>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <p v-if="month_profit >= 0">
                                            Прибыль за месяц ({{month_day_count_models}} дня): {{month_profit_models}}</p>
                                        <div class="alert alert-danger" v-if="success && (month_profit_models < 0)">
                                            <p>Прибыль за месяц ({{month_day_count_models}} дней): {{month_profit_models}}</p>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <p v-if="year_profit_models >= 0">
                                            ВСЕГО прибыль за {{year_day_count_models}} дней: {{year_profit_models}}</p>
                                        <div class="alert alert-danger" v-if="success && (year_profit_models < 0)">
                                            <p>ВСЕГО прибыль : {{year_profit_models}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "accountancyInstagram",
        data: function () {
            return {
                ID_RABOTA_VDK: -1,
                ID_MODELS_VDK: -1,

                income_arr: [],

                text: '',
                outcome: '',
                date: '',

                publics: [],
                public_id: 0,

                day_income: [],
                day_outcome: [],
                day_profit: [],

                day_income_rabota: [],
                day_outcome_rabota: [],
                day_profit_rabota: [],

                day_income_models: [],
                day_outcome_models: [],
                day_profit_models: [],

                month_income: [],
                month_outcome: [],
                month_profit: [],
                month_day_count: [],

                month_income_rabota: [],
                month_outcome_rabota: [],
                month_profit_rabota: [],
                month_day_count_rabota: [],

                month_income_models: [],
                month_outcome_models: [],
                month_profit_models: [],
                month_day_count_models: [],

                year_income: [],
                year_outcome: [],
                year_profit: [],
                year_day_count: [],

                year_income_rabota: [],
                year_outcome_rabota: [],
                year_profit_rabota: [],
                year_day_count_rabota: [],

                year_income_models: [],
                year_outcome_models: [],
                year_profit_models: [],
                year_day_count_models: [],

                success: false,
                error: false,
                errors: {
                    main: undefined
                },
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

            deleteLast()
            {
                this.$axios.delete('/accountancy/instagram/delete/last/' + this.date)
                    .then(response => {
                        if (response.status == 200)
                        {
                            this.day_income = response.data.day_income;
                            this.day_outcome = response.data.day_outcome;
                            this.day_profit = response.data.day_profit;

                            this.day_income_rabota = response.data.day_income_rabota;
                            this.day_outcome_rabota = response.data.day_outcome_rabota;
                            this.day_profit_rabota = response.data.day_profit_rabota;

                            this.day_income_models = response.data.day_income_models;
                            this.day_outcome_models = response.data.day_outcome_models;
                            this.day_profit_models = response.data.day_profit_models;

                            // ==============

                            this.month_income = response.data.month_income;
                            this.month_outcome = response.data.month_outcome;
                            this.month_profit = response.data.month_profit;
                            this.month_day_count = response.data.month_day_count;

                            this.month_income_rabota = response.data.month_income_rabota;
                            this.month_outcome_rabota = response.data.month_outcome_rabota;
                            this.month_profit_rabota = response.data.month_profit_rabota;
                            this.month_day_count_rabota = response.data.month_day_count_rabota;

                            this.month_income_models = response.data.month_income_models;
                            this.month_outcome_models = response.data.month_outcome_models;
                            this.month_profit_models = response.data.month_profit_models;
                            this.month_day_count_models = response.data.month_day_count_models;

                            // ==============

                            this.year_income = response.data.year_income;
                            this.year_outcome = response.data.year_outcome;
                            this.year_profit = response.data.year_profit;
                            this.year_day_count = response.data.year_day_count;

                            this.year_income_rabota = response.data.year_income_rabota;
                            this.year_outcome_rabota = response.data.year_outcome_rabota;
                            this.year_profit_rabota = response.data.year_profit_rabota;
                            this.year_day_count_rabota = response.data.year_day_count_rabota;

                            this.year_income_models = response.data.year_income_models;
                            this.year_outcome_models = response.data.year_outcome_models;
                            this.year_profit_models = response.data.year_profit_models;
                            this.year_day_count_models = response.data.year_day_count_models;

                            this.success = true;
                    }})
                    .catch(function (response) {
                        //alert("Не удалось удалить");
                        app.error = true;
                        app.errors = response.data.errors;
                        console.log(typeof(app.errors));
                        if (typeof(app.errors) != 'undefined') {
                            if (typeof app.errors.main !== 'undefined') {
                                document.getElementById("main").scrollIntoView({block: "center", behavior: "smooth"});
                            }
                        }
                    });
            },

            seeStatistics()
            {
                console.log('asdasd');
                var app = this;
                this.$axios.get('/accountancy/instagram/stat/' + app.date)
                    .then(response => {
                        if (response.status == 200)
                    {
                        this.day_income = response.data.day_income;
                        this.day_outcome = response.data.day_outcome;
                        this.day_profit = response.data.day_profit;

                        this.day_income_rabota = response.data.day_income_rabota;
                        this.day_outcome_rabota = response.data.day_outcome_rabota;
                        this.day_profit_rabota = response.data.day_profit_rabota;

                        this.day_income_models = response.data.day_income_models;
                        this.day_outcome_models = response.data.day_outcome_models;
                        this.day_profit_models = response.data.day_profit_models;

                        // ==============

                        this.month_income = response.data.month_income;
                        this.month_outcome = response.data.month_outcome;
                        this.month_profit = response.data.month_profit;
                        this.month_day_count = response.data.month_day_count;

                        this.month_income_rabota = response.data.month_income_rabota;
                        this.month_outcome_rabota = response.data.month_outcome_rabota;
                        this.month_profit_rabota = response.data.month_profit_rabota;
                        this.month_day_count_rabota = response.data.month_day_count_rabota;

                        this.month_income_models = response.data.month_income_models;
                        this.month_outcome_models = response.data.month_outcome_models;
                        this.month_profit_models = response.data.month_profit_models;
                        this.month_day_count_models = response.data.month_day_count_models;

                        // ==============

                        this.year_income = response.data.year_income;
                        this.year_outcome = response.data.year_outcome;
                        this.year_profit = response.data.year_profit;
                        this.year_day_count = response.data.year_day_count;

                        this.year_income_rabota = response.data.year_income_rabota;
                        this.year_outcome_rabota = response.data.year_outcome_rabota;
                        this.year_profit_rabota = response.data.year_profit_rabota;
                        this.year_day_count_rabota = response.data.year_day_count_rabota;

                        this.year_income_models = response.data.year_income_models;
                        this.year_outcome_models = response.data.year_outcome_models;
                        this.year_profit_models = response.data.year_profit_models;
                        this.year_day_count_models = response.data.year_day_count_models;

                        this.success = true;
                    }}).
                catch(function (error) {
                    app.error = true;
                    //console.log(error.response.data);
                    app.errors = error.response.data.errors;
                    console.log(app.errors);
                    if (typeof(app.errors) !== 'undefined') {

                    }
                });
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
                    this.errors.date = undefined;
                }

                this.$axios.post('/accountancy/instagram/store',
                    {
                        public_id: this.public_id,
                        text: this.text,
                        income: this.income_arr[this.public_id],
                        outcome: this.outcome,
                        date: this.date
                    }
                ).then(response => {
                    if (response.status == 200) {
                        this.day_income = response.data.day_income;
                        this.day_outcome = response.data.day_outcome;
                        this.day_profit = response.data.day_profit;

                        this.day_income_rabota = response.data.day_income_rabota;
                        this.day_outcome_rabota = response.data.day_outcome_rabota;
                        this.day_profit_rabota = response.data.day_profit_rabota;

                        this.day_income_models = response.data.day_income_models;
                        this.day_outcome_models = response.data.day_outcome_models;
                        this.day_profit_models = response.data.day_profit_models;

                        // ==============

                        this.month_income = response.data.month_income;
                        this.month_outcome = response.data.month_outcome;
                        this.month_profit = response.data.month_profit;
                        this.month_day_count = response.data.month_day_count;

                        this.month_income_rabota = response.data.month_income_rabota;
                        this.month_outcome_rabota = response.data.month_outcome_rabota;
                        this.month_profit_rabota = response.data.month_profit_rabota;
                        this.month_day_count_rabota = response.data.month_day_count_rabota;

                        this.month_income_models = response.data.month_income_models;
                        this.month_outcome_models = response.data.month_outcome_models;
                        this.month_profit_models = response.data.month_profit_models;
                        this.month_day_count_models = response.data.month_day_count_models;

                        // ==============

                        this.year_income = response.data.year_income;
                        this.year_outcome = response.data.year_outcome;
                        this.year_profit = response.data.year_profit;
                        this.year_day_count = response.data.year_day_count;

                        this.year_income_rabota = response.data.year_income_rabota;
                        this.year_outcome_rabota = response.data.year_outcome_rabota;
                        this.year_profit_rabota = response.data.year_profit_rabota;
                        this.year_day_count_rabota = response.data.year_day_count_rabota;

                        this.year_income_models = response.data.year_income_models;
                        this.year_outcome_models = response.data.year_outcome_models;
                        this.year_profit_models = response.data.year_profit_models;
                        this.year_day_count_models = response.data.year_day_count_models;

                        //===========

                        this.success = true;
                        this.outcome = '';
                    }
                    console.log(response);
                }).catch(function (error) {
                    app.error = true;
                    console.log(error);
                    app.errors = error.response.data.errors;
                    console.log(typeof(app.errors));
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
                    }
                    else {
                        app.errors = {main: false};
                    }
                });
            }
        },
        mounted () {
            var public_name = this.$route.params.public_name;
            if (public_name == 'rabota') {
                this.public_id = 1;
            }
            else if (public_name == 'models') {
                this.public_id = 2;
            }
            console.log(public_name);
            console.log(this.public_id);
            this.date = this.curday('-');
            var app = this;

            this.$axios.get('/accountancy/instagram/get-publics')
                .then(function (resp) {
                    console.log(resp);
                        app.publics = resp.data.publics;
                        app.ID_RABOTA_VDK = resp.data.ID_RABOTA_VDK;
                        app.ID_MODELS_VDK = resp.data.ID_MODELS_VDK;
                        app.income_arr = resp.data.income_arr;
                }).catch(function (error) {
                app.error = true;
                //console.log(error.response.data);
                app.errors = error.response.data.errors;
                console.log(app.errors);
                if (typeof(app.errors) !== 'undefined') {

                }
            });
        }
    }
</script>

<style scoped>

    .nopadding {
        padding: 0 !important;
        margin: 0 !important;
    }

    .panel {
        font-size: 130%;
    }
    body {
        margin: 0;
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