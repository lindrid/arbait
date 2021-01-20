<template>
        <div class="panel panel-default">

            <section class="select-skills">
                <div class="skills-header" style="text-align: center">
                    <p>Количество заявок : <b>{{app_count}} шт</b></p>
                </div>

                <div class="worker-skills" v-for="(app, index) in applications">
                    <div class="message" v-bind:class="{ 'is-primary': app_accordion_open[app.id],
                        'is-closed': !app_accordion_open[app.id] }"
                    >
                        <div class="category message-header"
                        @click="app_accordion_open[app.id]=!app_accordion_open[app.id]">
                            <h3>{{app.address}}</h3>
                        </div>
                        <div class="message-body"  style="width: 700px;">
                            <div class="message-content" >
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Карта</th>
                                        <th>Сумма</th>
                                        <th>Перевод с банка</th>
                                        <th>Ссылка</th>
                                        <th>Статус</th>
                                        <th width="100">&nbsp;</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(payment, index) in all_payments[app.id]">
                                        <td>{{ payment.receiver_card_number }}</td>
                                        <td>{{ payment.money_amount }}</td>
                                        <td > Сбербанк </td>
                                        <td> Ссылка </td>
                                        <td> {{ payment.state }} </td>
                                        <td>
                                            <router-link :to="{name: 'accEditIncome', params: {id: payment.id}}" class="btn btn-xs btn-default">
                                                Реактировать
                                            </router-link>
                                            <a href="#"
                                               class="btn btn-xs btn-danger"
                                               v-on:click="deleteEntry(payment.id, index)">
                                                Удалить
                                            </a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <br><b>ДОХОД:</b> <input type="text" size="5px" name="fname" v-bind:value="app.income"><br>
                                <br><b>РАСХОД:</b> {{app.outcome}}
                            </div>
                        </div>
                    </div>
                </div>

                    <router-link :to="{name: 'acc-create-payment'}" class="btn btn-default">
                        Назад
                    </router-link>

            </section>
    </div>
</template>

<script>
    export default {
        name: "acc-payments",
        data: function () {
            return {
                applications: [],
                all_payments:[],
                date: '',
                component_name: 'accountancy',
                app_count: 0,
                app_accordion_open: []
                //prevRoute: ''
            }
        },
        mounted() {
            this.date = this.$route.params.date;
            var app = this;

            if (typeof this.date === 'undefined') {
                this.$axios.get('/accountancy/payment/index/')
                    .then(function (resp) {
                        console.log(resp);
                        app.income_applications = resp.data.apps;
                        app.date = resp.data.date;
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        alert("Не удалось загрузить");
                    });
            }
            else {
                this.$axios.get('/accountancy/payment/index/' + app.date)
                    .then(function (resp) {
                        console.log(resp);
                        app.applications = resp.data.apps;
                        app.all_payments = resp.data.payments;
                        app.date = resp.data.date;
                        app.app_count = resp.data.app_count;
                        app.app_accordion_open = resp.data.app_accordion_open;
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        alert("Не удалось загрузить");
                    });
            }
        },
        methods: {
            deleteEntry(id, index) {
                if (confirm("Вы действительно хотите удалить?")) {
                    var app = this;
                    this.$axios.delete('/accountancy/app/delete/' + id)
                        .then(function (resp) {
                            app.income_applications.splice(index, 1);
                        })
                        .catch(function (resp) {
                            alert("Не удалось удалить");
                        });
                }
            },
        }
    }
</script>