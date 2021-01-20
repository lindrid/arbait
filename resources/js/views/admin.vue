<template>
    <div>
        <div class="panel panel-default">
            <div class="panel-heading">Список заявок клиентов</div>
            <div class="panel-body">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Пользователь</th>
                        <th>Дата</th>
                        <th>Адрес</th>
                        <th>Услуга</th>
                        <th>Примечание</th>
                        <th width="100">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="company, index in companies">
                        <td>{{ company.user_name }}</td>
                        <td>{{ company.name }}</td>
                        <td>{{ company.address }}</td>
                        <td>{{ company.website }}</td>
                        <td>{{ company.email }}</td>
                        <td>
                            <router-link :to="{name: 'editCompany', params: {id: company.id}}" class="btn btn-xs btn-default">
                                Реактировать
                            </router-link>
                            <a href="#"
                               class="btn btn-xs btn-danger"
                               v-on:click="deleteEntry(company.id, index)">
                                Удалить
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <button class="btn" @click="index"></button>

                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя/Ник</th>
                        <th>email</th>
                        <th>Телефон</th>
                        <th>Тип</th>
                        <th>Создан</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="user, index in users">
                        <td>{{ user.id }}</td>
                        <td>{{ user.name }}</td>
                        <td>{{ user.email }}</td>
                        <td>{{ user.phone_number }}</td>
                        <td>{{ user.type }}</td>
                        <td>{{ user.created_at }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                // name: "admin",
                companies: [],
                users: []
            }
        },
        mounted() {
            var uuser = [];
            var app = this;
            this.$axios.get('/v1/companies')
                .then(function (resp) {
                    app.companies = resp.data;
                })
                .catch(function (resp) {
                    console.log(resp);
                    alert("Не удалось загрузить");
                });

            this.$axios.get('userindex')
                .then(function (resp) {
                    for (const prop in resp.data) {
                        if (resp.data[prop].type == 'Worker') {
                            uuser.push(resp.data[prop]);
                        }
                    }
                    app.users = uuser;
                    //app.users = resp.data;
                    console.log(app.users);
                })
                .catch(function (resp) {
                    console.log(resp);
                    alert("Не удалось загрузить");
                });
        },
        methods: {
            deleteEntry(id, index) {
                if (confirm("Вы действительно отите удалить?")) {
                    var app = this;
                    this.$axios.delete('/v1/companies/' + id)
                        .then(function (resp) {
                            app.companies.splice(index, 1);
                        })
                        .catch(function (resp) {
                            alert("Не удалось удалить");
                        });
                }
            },
            index () {
                const userr = [];
                this.$axios.get('userindex')
                    .then(function (resp) {
                        for (const prop in resp.data) {
                            if (resp.data[prop].type == 'not') {
                                userr.push(resp.data[prop]);
                            }
                        }
                        console.log(userr[0]);
                        console.log(userr);
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        alert("Не удалось загрузить");
                    });
            }
        }
    }
</script>