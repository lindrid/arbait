<template>
    <div>
        <div class="panel panel-default">
            <div class="panel-heading">Список заявок за {{date}}</div>
            <div class="panel-body">
                <p>Количество заявок : <b>{{app_count}} шт</b></p>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Заявка</th>
                        <th>Диспетчер</th>
                        <th>Сумма</th>
                        <th width="100">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(application, index) in income_applications">
                        <td>{{ application.text }}</td>
                        <td>{{ dispatcher_names[application.user_id]}}</td>
                        <td>{{ application.money_amount }}</td>
                        <td>
                            <router-link :to="{name: 'accEditIncome', params: {id: application.id}}"
                                         class="btn btn-xs btn-default">
                                Реактировать
                            </router-link>
                            <a href="#"
                               class="btn btn-xs btn-danger"
                               v-on:click="deleteEntry(application.id, index)">
                                Удалить
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <router-link :to="{name: component_name, params: component_params}" class="btn btn-default">
                Назад
            </router-link>
        </div>

    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                income_applications: [],
                date: '',
                component_name: 'accountancy',
                component_params: {dispatcher_name: 'gleb'},
                app_count: 0,
                dispatcher_names: []
                //prevRoute: ''
            }
        },
        mounted() {
            this.date = this.$route.params.date;
            var app = this;

            if (typeof this.date === 'undefined') {
                this.$axios.get('/accountancy/index')
                    .then(function (resp) {
                        console.log(resp);
                        app.income_applications = resp.data.apps;
                        app.date = resp.data.date;
                        app.app_count = resp.data.appCount;
                        app.dispatcher_names = resp.data.dispatcher_names;
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        alert("Не удалось загрузить");
                    });
            }
            else {
                console.log('AAAA');
                app.component_name = 'accDetailMonth';
                app.component_params = {};
                this.$axios.get('/accountancy/index/' + app.date)
                    .then(function (resp) {
                        console.log(resp);
                        app.income_applications = resp.data.apps;
                        app.date = resp.data.date;
                        app.app_count = resp.data.appCount;
                        app.dispatcher_names = resp.data.dispatcher_names;
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
            }
        }
    }
</script>
