<template>
    <div>
        <div class="form-group">
            <router-link to="/" class="btn btn-default">Back</router-link>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">Редактировать</div>
            <div class="panel-body">
                <form v-on:submit="saveForm()">
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label class="control-label">Дата</label>
                            <input type="text" v-model="company.name" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label class="control-label">Адрес</label>
                            <input type="text" v-model="company.address" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label class="control-label">Услуга</label>
                            <input type="text" v-model="company.website" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label class="control-label">Примечание</label>
                            <input type="text" v-model="company.email" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <button class="btn btn-success">Применить</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            let app = this;
            let id = app.$route.params.id;
            app.companyId = id;
            this.$axios.get('/v1/companies/' + id)
                .then(function (resp) {
                    app.company = resp.data;
                })
                .catch(function () {
                    alert("Не удалось загрузить компанию")
                });
        },
        data: function () {
            return {
                companyId: null,
                company: {
                    name: '',
                    user_name: '',
                    address: '',
                    website: '',
                    email: '',
                }
            }
        },
        methods: {
            saveForm() {
                event.preventDefault();
                var app = this;
                var newCompany = app.company;
                this.$axios.patch('/v1/companies/' + app.companyId, newCompany)
                    .then(function (resp) {
                        app.$router.replace('/admin');
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        alert("Не удалось создать компанию");
                    });
            }
        }
    }
</script>


<style scoped>

</style>