<template>
    <div>
        <div class="panel panel-default">
            <div class="panel-heading">Companies list</div>
            <div class="panel-body">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Адрес</th>
                        <th>Услуга</th>
                        <th>Примечание</th>
                        <th width="100">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="company, index in companies">
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
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                companies: []
            }
        },
        mounted() {
            var app = this;
            this.$axios.get('/v1/companies')
                .then(function (resp) {
                    console.log(resp);
                    app.companies = resp.data;
                })
                .catch(function (resp) {
                    console.log(resp);
                    alert("Не удалось загрузить компании");
                });
        },
        methods: {
            deleteEntry(id, index) {
                if (confirm("Вы действительно хотите удалить компанию?")) {
                    var app = this;
                    this.$axios.delete('/v1/companies/' + id)
                        .then(function (resp) {
                            app.companies.splice(index, 1);
                        })
                        .catch(function (resp) {
                            alert("Не удалось удалить компанию");
                        });
                }
            }
        }
    }
</script>