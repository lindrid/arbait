<template>
    <div>
        <div class="panel panel-default">
            <div class="panel-body">
                <form @submit.prevent="saveForm()">
                    <div class="row col-xs-12 form-group">
                        <button class="col-xs-5 col-md-1"
                                @click="clearFields">Стереть</button>
                    </div>

                    <div class="row  visually-hidden col-xs-12 form-group">
                        <radio name="radiobtn_dispatcher"
                               v-bind:value="1" v-model="application.dispatcher_id"
                        >
                            Глебас
                        </radio>
                        <radio name="radiobtn_dispatcher"
                               v-bind:value="2" v-model="application.dispatcher_id"
                        >
                            Димас
                        </radio>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <input
                                    id="address"
                                    ref="addr"
                                    type="text"
                                    v-model="application.address"
                                    class="form-control"
                                    placeholder="Адрес"
                            >
                            <span class="help-block" v-if="error && errors.address">{{ errors.address }}</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label class="control-label">Дата</label>
                            <input id="date"
                                   type="text"
                                   v-model="application.date"
                                   @focus="selectDay()"
                                   style="width: 120px;"
                            >
                        </div>
                    </div>

                    <div class="row mytime">
                        <div class="col-xs-12 form-group">
                            <label class="control-label">Время</label>
                            <input id="time_hours"
                                   type="text"
                                   ref="th"
                                   v-model="time_hours"
                                   @focus="$event.target.select()"
                                   style="width: 70px;"
                                   :onchange="examineHoursAndFocusOnMinutes()"
                                   v-mask="'99'"
                            >

                            <b> : </b>
                            <input id="time_minutes"
                                   ref="tm"
                                   type="text"
                                   v-model="time_minutes"
                                   @focus="$event.target.select()"
                                   style="width: 70px;"
                                   v-mask="'99'"
                                   :onchange="examineMinutes()"
                            >
                            <span class="help-block" v-if="error && errors.time_hours">{{ errors.time_hours }}</span>
                            <span class="help-block" v-if="error && errors.time_minutes">{{ errors.time_minutes }}</span>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label class="control-label">Количество: человек / часов</label><br>
                            <input
                                    style="width: 60px;"
                                    id="worker_total"
                                    type="text"
                                    v-model="application.worker_total"
                                    @click="$event.target.select()"
                            >
                                <b> &nbsp; / &nbsp; </b>
                                <input
                                        style="width: 60px;"
                                        id="work_hours"
                                        type="text"
                                        v-show="application.hourly_job"
                                        v-model="application.work_hours[application.hourly_job]"
                                        @click="$event.target.select()"
                                >
                            <span class="help-block" v-if="error && errors.worker_total">{{ errors.worker_total }}</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 form-group">

                            <label class="control-label">Оплата: от клиента / грузчикам</label><br>

                            <button
                                    class="btn btn-default"
                                    v-on:click.stop.prevent="calcSummTotal();"
                            >
                                <i  class="glyphicon glyphicon-arrow-down"></i>
                            </button>

                            <input
                                    id="price"
                                    ref="price"
                                    type="text"
                                    v-model="application.price[application.hourly_job]"
                                    style="width: 60px;"
                                    @click="$event.target.select()"
                            >

                            <b> &nbsp; / &nbsp; </b>

                            <input
                                    id="price_for_worker"
                                    type="text"
                                    v-model="application.price_for_worker[application.hourly_job]"
                                    style="width: 60px;"
                                    @click="$event.target.select()"
                            >

                            <span class="help-block" v-if="error && errors.price">{{ errors.price }}</span>
                            <span class="help-block" v-if="error && errors.price_for_worker">{{ errors.price_for_worker }}</span>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label class="control-label">Доход / расход / прибыль</label><br>

                            <button
                                    class="btn btn-default"
                                    v-on:click.stop.prevent="calcPayValues()"
                            >
                                <i  class="glyphicon glyphicon-arrow-up"></i>
                            </button>

                            <input
                                    id = "summ_total"
                                    ref="summ_total"
                                    type="text"
                                    v-model="application.summ_total[application.hourly_job]"
                                    style="width: 80px;"
                            >


                            <b> &nbsp; / &nbsp; </b>

                            <input
                                    id = "summ_w_total"
                                    type="text"
                                    v-model="application.summ_w_total[application.hourly_job]"
                                    style="width: 80px;"
                            >

                            <b> &nbsp; / &nbsp; </b>

                            <input
                                    id = "profit"
                                    type="text"
                                    v-bind:value="application.summ_total[application.hourly_job] -
                                        application.summ_w_total[application.hourly_job]"
                                    style="width: 80px;"
                            >
                            <span class="help-block" v-if="error && errors.summ_total">{{ errors.summ_total }}</span>
                            <span class="help-block" v-if="error && errors.summ_w_total">{{ errors.summ_w_total }}</span>
                        </div>
                    </div>


                            <div class="row">
                        <div class="col-xs-12 form-group">
                            <radio name="rbtn_per_hour_or_piece_rate"
                                   v-bind:value="1" v-model="application.hourly_job"
                            >
                                В час
                            </radio>
                            <radio name="rbtn_per_hour_or_piece_rate"
                                   v-bind:value="0" v-model="application.hourly_job"
                            >
                                Сдельно
                            </radio>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <textarea id="text"
                                      v-model="application.what_to_do"
                                      class="form-control form-group"
                                      placeholder="Что делать?">
                            </textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <radio name="rbtn_pay_method"
                                   checked
                                   v-bind:value="1" v-model="application.pay_method"
                            >
                                Карта
                            </radio>
                            <radio name="rbtn_pay_method"
                                   v-bind:value="2" v-model="application.pay_method"
                            >
                                Наличка
                            </radio>
                            <radio name="rbtn_pay_method"
                                   v-bind:value="3" v-model="application.pay_method"
                            >
                                Расчетный счет
                            </radio>
                        </div>
                    </div>

                    <div class="form-group" v-bind:class="{ 'has-error': error && errors.client_phone_number }">
                        <label for="client_phone_number">Телефон клиента</label>
                        <div class="input-group">
                            <vue-tel-input
                                    id="client_phone_number"
                                    ref="tel"
                                    class="form-control"
                                    v-model="application.client_phone_number"
                                    required
                            ></vue-tel-input>
                        </div>
                        <span class="help-block" id="client_phone_number_help_block"
                              v-if="error && errors.client_phone_number">{{ errors.client_phone_number }}
                        </span>
                    </div>

                    <div class="row">
                        <div class="col-xs-3 col-md-1 form-group">
                            <button class="btn btn-success" @click="saveForm()">Сохранить</button>
                        </div>
                        <div class="col-xs-3 col-md-1 form-group">
                            <div v-if="devochka_mode">
                                <router-link to="/zayavki" class="btn btn-default">Назад</router-link>
                            </div>
                            <div v-else>
                                <router-link to="/apps/actual" class="btn btn-default">Назад</router-link>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</template>

<script>
    import VueTelInput from 'vue-tel-input';
    //import DispatcherRadioList from '../components/DispatcherRadioList.vue';

    export default {
        name: "applicationCreateOrEdit",

        computed: {
        },
        components: {
            VueTelInput//,
            //DispatcherRadioList
        },
        data: function () {
            return {
                ID_GLEB: 1,
                ID_DIMA: 2,
                ID_DEVOCHKA: 4,

                NAME_GLEB: 'gleb',
                NAME_DIMA: 'dima',
                NAME_DEVOCHKA: 'nastya',

                PRICE_PIECE_RATE: 2000,
                PRICE_PER_HOUR: 300,
                PRICE_PR_FOR_WORKER: 1500,
                PRICE_PH_FOR_WORKER: 200,

                devochka_mode: false,

                application: {
                    id:0,
                    what_to_do: '',
                    address: '',
                    date: '',
                    time: '',
                    price: {0: 2400, 1: 350},
                    price_for_worker: {0: 1900, 1: 250},
                    hourly_job: 1,
                    edg: 0,
                    pay_method: 1,
                    client_phone_number: '',
                    state: 1,
                    income: 0,
                    outcome: 0,
                    profit: 0,
                    worker_count: 2,
                    worker_total: 2,
                    work_hours: {0: 1, 1: 2},
                    summ_total: {0: 4800, 1: 700},
                    summ_w_total: {0: 3800, 1: 500},
                    dispatcher_id: 0
                },
                calc: {
                  'summ': true,
                  'pays': false
                },
                cwaIsOpen: 0,   //parsed text area is open or not
                success: false,
                time_hours: '',
                time_minutes: '',
                action: 'create',

                error: false,
                errors: {
                    application: undefined,
                    outcome: undefined,
                    what_to_do: undefined
                },
            }
        },
        methods: {
            clearFields: function ()
            {
                var a = this.application;
                this.application = {
                    id: a.id,
                    what_to_do: '',
                    address: '',
                    date: this.application.date,
                    time: '',
                    price: a.price[a.hourly_job],
                    price_for_worker: a.price_for_worker[a.hourly_job],
                    hourly_job: 1,
                    edg: 0,
                    pay_method: 1,
                    client_phone_number: '',
                    state: 1,
                    income: 0,
                    outcome: 0,
                    profit: 0,
                    worker_count: 2,
                    worker_total: 2,
                    work_hours: a.work_hours[a.hourly_job],
                    summ_total: a.summ_total[a.hourly_job],
                    summ_w_total: a.summ_w_total[a.hourly_job],
                    dispatcher_id: this.dispatcher_id
                }
            },

            calcSummTotal: function ()
            {
                /*if (this.calc['pays']) {
                    this.calc['pays'] = false;
                    return;
                }
                this.calc['summ'] = true;*/
                var hj = this.application.hourly_job;
                var app = this.application;
                var h = app.work_hours[hj];
                var wt = app.worker_total;


                this.$set(app.summ_total, hj, app.price[hj] * h * wt);

                //app.summ_total[hj] = app.price[hj] * h * wt;
                app.summ_w_total[hj] = app.price_for_worker[hj] * h * wt;

                console.log(app.summ_total[hj]);
                document.getElementById("summ_total").scrollIntoView({block: "center", behavior: "smooth"});
            },

            calcPayValues: function ()
            {
                /*if (this.calc['summ']) {
                    this.calc['summ'] = false;
                    return;
                }
                this.calc['pays'] = true;*/

                var hj = this.application.hourly_job;
                var app = this.application;
                var h = app.work_hours[hj];
                var wt = app.worker_total;

                app.price[hj] = (app.summ_total[hj]) / (h * wt);
                app.price_for_worker[hj] = (app.summ_w_total[hj]) / (h * wt);
                document.getElementById("price").scrollIntoView({block: "center", behavior: "smooth"});
            },

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
                //console.log('BBBBBBBBBBBB')
                event.preventDefault();
                var app = this;

                if (this.error == true) {
                    this.error = false;
                    this.errors.application = undefined;
                    this.errors.outcome = undefined;
                    this.errors.what_to_do = undefined;
                }

                this.application.time = this.time_hours + ':' + this.time_minutes;

                var uri = '/application/store';
                if (this.action == 'edit') {
                    uri = '/application/update/' + this.application.id;
                }
                this.$axios.post(uri,
                    {
                        address: this.application.address,
                        date: this.application.date,
                        time: this.application.time,
                        worker_total: this.application.worker_total,
                        price: this.application.price[this.application.hourly_job],
                        price_for_worker: this.application.price_for_worker[this.application.hourly_job],
                        hourly_job: this.application.hourly_job,
                        what_to_do: this.application.what_to_do,
                        edg: this.application.edg,
                        pay_method: this.application.pay_method,
                        client_phone_number: this.application.client_phone_number,
                        dispatcher_id: this.application.dispatcher_id
                    }
                ).then(response => {
                    console.log(response);
                    if (response.status == 200) {
                        //this.day_income = response.data.day_income;
                        this.success = true;
                        var name = '';
                        localStorage.removeItem('dispatcher_id');
                        localStorage.removeItem('address');
                        localStorage.removeItem('date');
                        localStorage.removeItem('time_hours');
                        localStorage.removeItem('time_minutes');
                        localStorage.removeItem('what_to_do');
                        localStorage.removeItem('price');
                        localStorage.removeItem('price_for_worker');
                        localStorage.removeItem('hourly_job');
                        localStorage.removeItem('pay_method');
                        localStorage.removeItem('client_phone_number');
                        localStorage.removeItem('worker_count');
                        localStorage.removeItem('worker_total');

                        if (this.application.dispatcher_id == this.ID_DEVOCHKA) {
                            this.$router.push({ name: 'zayavki' });
                        }
                        else {
                            this.$router.push({name: 'applications'});
                        }
                    }
                    else {
                        // а тут что?
                    }

                }).catch(function (error) {
                    var words = error.toString().split(' ');
                    var errorStatus = words[words.length - 1];
                    if (errorStatus == '401') {
                        alert("Вы должны залогиниться!");
                        app.$router.push({name: 'login'});
                    }
                    app.error = true;
                    //console.log(error.response.data);
                    app.errors = error.response.data.errors;
                    console.log(error);
                    if (typeof app.errors.income !== 'undefined') {
                        document.getElementById("income").scrollIntoView({block: "center", behavior: "smooth"});
                    }
                    if (typeof app.errors.outcome !== 'undefined') {
                        document.getElementById("outcome").scrollIntoView({block: "center", behavior: "smooth"});
                    }
                    if (typeof app.errors.what_to_do !== 'undefined') {
                        document.getElementById("what_to_do").scrollIntoView({block: "center", behavior: "smooth"});
                    }
                });
            },
            examineHoursAndFocusOnMinutes(event) {
                //console.log(this.time_hours.length);
                //document.getElementById("time_minutes").focus();
                if (document.activeElement.id != 'time_hours') {
                    return;
                }

                if (this.error == true) {
                    this.error = false;
                    this.errors.time_hours = undefined;
                    this.errors.time_minutes = undefined;
                }

                var th = this.time_hours.replace(/[^0-9]/g,"");
                if (th < 0 || th > 23) {
                    this.error = true;
                    this.errors.time_hours = 'Неверное количество часов';
                    document.getElementById("time_hours").scrollIntoView({block: "center", behavior: "smooth"});
                    document.getElementById("time_hours").select();
                    return;
                }
                if (th.length > 1) {
                    console.log('123124');
                    document.getElementById("time_minutes").focus();
                }
            },
            examineMinutes() {
                if (this.error == true) {
                    this.error = false;
                    this.errors.time_minutes = undefined;
                }

                var tm = this.time_minutes;
                console.log(tm);
                if (tm < 0 || tm > 60) {
                    this.error = true;
                    console.log('AAAS');
                    //this.errors.time_minutes = 'Неверное количество минут';
                   // document.getElementById("time_minutes").scrollIntoView({block: "center", behavior: "smooth"});
                    //document.getElementById("time_minutes").select();
                }
            },
            selectDay() {
                var input = document.getElementById("date");
                var s = input.value;
                if (s.length) {
                    window.setTimeout(function() {
                        input.setSelectionRange(s.length-2, s.length);
                    }, 0);
                }
            }
        },

        mounted () {
            const input = this.$refs.price;

            var dispatcher_name = this.$route.params.dispatcher_name;
            var app = this;
            var path = this.$route.path.split("/");
            this.action = path[2];

            //console.log(localStorage);
            /*if (localStorage.dispatcher_id) {
                this.application.dispatcher_id = localStorage.dispatcher_id;
            }
            if (localStorage.address) {
                this.application.address = localStorage.address;
            }
            if (localStorage.date) {
                this.application.date = localStorage.date;
            }
            if (localStorage.time_hours) {
                this.time_hours = localStorage.time_hours;
            }
            if (localStorage.time_minutes) {
                this.time_minutes = localStorage.time_minutes;
            }
            if (localStorage.what_to_do) {
                this.application.what_to_do = localStorage.what_to_do;
            }
            if (localStorage.price) {
                this.application.price = localStorage.price;
            }
            if (localStorage.price_for_worker) {
                this.application.price_for_worker = localStorage.price_for_worker;
            }
            if (localStorage.hourly_job) {
                this.application.hourly_job = localStorage.hourly_job;
            }
            if (localStorage.pay_method) {
                this.application.pay_method = localStorage.pay_method;
            }
            if (localStorage.client_phone_number) {
                this.application.client_phone_number = localStorage.client_phone_number;
            }
            if (localStorage.worker_count) {
                this.application.worker_count = localStorage.worker_count;
            }
            if (localStorage.worker_total) {
                this.application.worker_total = localStorage.worker_total;
            }*/

            if (this.action == 'edit') {
                this.$axios.get('/application/edit/' + app.$route.params.id)
                    .then(function (resp) {
                        app.application = resp.data.application;
                        // не реактивное свойство work_hours
                        app.application.work_hours = {0: 1, 1:2};

                        if (app.application.hourly_job) {
                            // нельзя использовать просто =
                            // т.к. новое поле объекта не будет реактивным.
                            // чтобы оно было реактивным, нужно использовать $set

                            app.$set(app.application, 'summ_total', {
                                0 : app.PRICE_PIECE_RATE * app.application.worker_total,
                                1 : app.application.price * app.application.worker_total * 2});

                            app.$set(app.application, 'summ_w_total', {
                                0 : app.PRICE_PR_FOR_WORKER * app.application.worker_total,
                                1 : app.application.price_for_worker * app.application.worker_total * 2});

                            app.$set(app.application, 'price', {
                                0: app.PRICE_PIECE_RATE,
                                1: app.application.price
                            });

                            app.$set(app.application, 'price_for_worker', {
                                0: app.PRICE_PR_FOR_WORKER,
                                1: app.application.price_for_worker
                            });
                        }
                        else {
                            app.$set(app.application, 'summ_total', {
                                0 : app.application.price * app.application.worker_total,
                                1 : app.PRICE_PER_HOUR * app.application.worker_total * 2});

                            app.$set(app.application, 'summ_w_total', {
                                0 : app.application.price_for_worker * app.application.worker_total,
                                1 : app.PRICE_PH_FOR_WORKER * app.application.worker_total * 2});

                            app.$set(app.application, 'price', {
                                0: app.application.price,
                                1: app.PRICE_PER_HOUR
                            });

                            app.$set(app.application, 'price_for_worker', {
                                0: app.application.price_for_worker,
                                1: app.PRICE_PH_FOR_WORKER
                            });
                        }

                        var arr = app.application.time.split(':');
                        app.time_hours = arr[0];
                        app.time_minutes = arr[1];
                        console.log(app.application);
                    })
                    .catch(function (error) {
                        var words = error.toString().split(' ');
                        var errorStatus = words[words.length - 1];
                        if (errorStatus == '401') {
                            alert("Вы должны залогиниться!");
                            app.$router.push({name: 'login'});
                        }
                    });
            }

            if (dispatcher_name == this.NAME_GLEB) {
                this.application.dispatcher_id = this.ID_GLEB;
            }
            else if (dispatcher_name == this.NAME_DIMA) {
                this.application.dispatcher_id = this.ID_DIMA;
            }
            else if (dispatcher_name == this.NAME_DEVOCHKA) {
                this.application.dispatcher_id = this.ID_DEVOCHKA;
                this.devochka_mode = true;
            }
            else {
                this.application.dispatcher_id = this.ID_GLEB;
            }

            if (this.$route.name == 'zayavkaCreate' || this.$route.name == 'zayavkaEdit') {
                this.application.dispatcher_id = this.ID_DEVOCHKA;
                this.devochka_mode = true;
            }

            this.application.date = this.curday('-');
            this.$refs.addr.focus();
        },

       /*watch: {
           'application.dispatcher_id' (newVal) {
               localStorage.dispatcher_id = newVal;
           },
           'application.address' (newVal) {
               localStorage.address = newVal;
           },
           'application.date' (newVal) {
                localStorage.date = newVal;
           },
           'time_hours' (newVal) {
               localStorage.time_hours = newVal;
           },
           'time_minutes' (newVal) {
               localStorage.time_minutes = newVal;
           },
           'application.what_to_do' (newVal) {
               localStorage.what_to_do = newVal;
           },
           'application.price' (newVal) {
               localStorage.price = newVal;
           },
           'application.price_for_worker' (newVal) {
               localStorage.price_for_worker = newVal;
           },
           'application.hourly_job' (newVal) {
               localStorage.hourly_job = newVal;
           },
           'application.pay_method' (newVal) {
               localStorage.pay_method = newVal;
           },
           'application.client_phone_number' (newVal) {
               localStorage.client_phone_number = newVal;
           },
           'application.worker_count' (newVal) {
               localStorage.worker_count = newVal;
           },
           'application.worker_total' (newVal) {
               localStorage.worker_total = newVal;
           },
        }*/
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

    .mytime, .form-group {
        font-size: 110%
    }
    .form-control{
        font-size: 120%;
    }
    .worker-count {
        width: 100px;
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
</style>