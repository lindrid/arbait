<template>
    <div>
        <div class="panel panel-default">
            <div class="panel-heading">List</div>
            <div class="panel-body">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Услуга</th>
                        <th>Адрес</th>
                        <th>Примечание</th>
                        <th width="100">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="application, index in applications">
                        <td>{{ application.name }}</td>
                        <td>{{ application.address }}</td>
                        <td>{{ application.misc }}</td>
                        <td>
                            <router-link :to="{name: 'editApplication', params: {id: application.id}}" class="btn btn-xs btn-default">
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
        </div>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                applications: []
            }
        },
        mounted() {
            var app = this;
            this.$axios.get('/v1/companies') //applications -- переименовать (ЗАЯВКИ)
                .then(function (resp) {
                    console.log(resp);
                    app.applications = resp.data;
                })
                .catch(function (resp) {
                    console.log(resp);
                    alert("Не удалось загрузить");
                });
        },
        methods: {
            deleteEntry(id, index) {
                if (confirm("Вы действительно хотите удалить?")) {
                    var app = this;
                    this.$axios.delete('/v1/companies/' + id)
                        .then(function (resp) {
                            app.applications.splice(index, 1);
                        })
                        .catch(function (resp) {
                            alert("Не удалось удалить");
                        });
                }
            }
        }
    }
</script>