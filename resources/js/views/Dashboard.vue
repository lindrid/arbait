<template>
    <section id="home">
        <div class="home-wrapper col-xs-12">
            <h1>Личный кабинет пользвателя</h1>
        </div>
        <div>
            <h2>Мои заявки</h2>
            <div class="panel panel-default">
                <div class="panel-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Адрес</th>
                            <th>Услуга</th>
                            <th>Примечание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="company, index in companies">
                            <td>{{ company.name }}</td>
                            <td>{{ company.address }}</td>
                            <td>{{ company.website }}</td>
                            <td>{{ company.email }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        data: function () {
            return {
                name: "dashboard",
                companies: []
            }
        },
        mounted() {
            var app = this;
            this.$axios.get('/indexuser')
                .then(function (resp) {
                    app.companies = resp.data;
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
                            app.companies.splice(index, 1);
                        })
                        .catch(function (resp) {
                            alert("Не удалось удалить");
                        });
                }
            }
        }
    }
</script>

<style scoped>

</style>